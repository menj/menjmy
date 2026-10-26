<?php
/**
 * Book WP — Reading Analytics
 *
 * Computes reading time, word count, line count, stanza count,
 * and renders the analytics bar displayed above each work.
 *
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extract readable text from book-wp/chapter block attributes.
 *
 * Gutenberg stores chapter verse content inside block comment JSON attributes,
 * not as visible post body text. The raw post_content looks like:
 *   <!-- wp:book-wp/chapter {"content":"<p>verse</p>"} /-->
 *
 * This helper finds all such blocks via WordPress parse_blocks() when
 * available and returns all verse text concatenated, so word/line counters
 * can include it.
 *
 * @param string $raw_content Raw post_content field value.
 * @return string Plain text from all chapter blocks combined.
 */
function book_wp_extract_chapter_text( $raw_content ) {
    if ( false === strpos( $raw_content, 'wp:book-wp/chapter' ) ) return '';

    if ( function_exists( 'parse_blocks' ) ) {
        return book_wp_extract_chapter_text_from_blocks( parse_blocks( $raw_content ) );
    }

    return book_wp_extract_chapter_text_fallback( $raw_content );
}

/**
 * Extract chapter text from parsed Gutenberg blocks.
 *
 * @param array $blocks Parsed block array from parse_blocks().
 * @return string Plain text from all chapter blocks.
 */
function book_wp_extract_chapter_text_from_blocks( $blocks ) {
    $text = '';

    foreach ( $blocks as $block ) {
        $name = isset( $block['blockName'] ) ? $block['blockName'] : null;

        if ( 'book-wp/chapter' === $name ) {
            $attrs = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
            $text .= ' ' . book_wp_chapter_attrs_to_text( $attrs );
        }

        if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
            $text .= ' ' . book_wp_extract_chapter_text_from_blocks( $block['innerBlocks'] );
        }
    }

    return trim( $text );
}

/**
 * Convert book-wp/chapter attributes into readable text.
 *
 * @param array $attrs Block attributes.
 * @return string Plain text from the block's current and legacy fields.
 */
function book_wp_chapter_attrs_to_text( $attrs ) {
    $text = '';

    // Current format: content string of HTML.
    if ( ! empty( $attrs['content'] ) ) {
        $html = $attrs['content'];
        $html = preg_replace( '/<br\s*\/?>/', "\n", $html );
        $html = preg_replace( '/<\/p>/i', "\n", $html );
        $text .= ' ' . wp_strip_all_tags( $html );
    }

    // v3.3.40 array format (legacy; may exist in old posts).
    if ( ! empty( $attrs['verses'] ) && is_array( $attrs['verses'] ) ) {
        $text .= ' ' . implode( ' ', $attrs['verses'] );
    }

    return trim( $text );
}

/**
 * Fallback parser for tests or unusual runtimes without parse_blocks().
 *
 * @param string $raw_content Raw post_content field value.
 * @return string Plain text from all chapter blocks.
 */
function book_wp_extract_chapter_text_fallback( $raw_content ) {
    $text = '';

    preg_match_all(
        '/<!--\s*wp:book-wp\/chapter\s+(\{.*?\})\s*\/-->/s',
        $raw_content,
        $matches
    );

    foreach ( $matches[1] as $json ) {
        $attrs = json_decode( $json, true );
        if ( ! $attrs ) continue;

        $text .= ' ' . book_wp_chapter_attrs_to_text( $attrs );
    }

    return trim( $text );
}

/**
 * Remove book-wp/chapter blocks from raw post content.
 *
 * Uses parse_blocks() on WordPress so JSON attributes are parsed by core, with
 * a regex fallback only for the standalone PHPUnit stub environment.
 *
 * @param string $raw_content Raw post_content field value.
 * @return string Post content without chapter blocks.
 */
function book_wp_remove_chapter_blocks( $raw_content ) {
    if ( false === strpos( $raw_content, 'wp:book-wp/chapter' ) ) return $raw_content;

    if ( function_exists( 'parse_blocks' ) ) {
        return book_wp_non_chapter_html_from_blocks( parse_blocks( $raw_content ) );
    }

    return preg_replace( '/<!--\s*wp:book-wp\/chapter\s+\{.*?\}\s*\/-->/s', '', $raw_content );
}

/**
 * Rebuild non-chapter block HTML from parsed blocks.
 *
 * @param array $blocks Parsed block array from parse_blocks().
 * @return string HTML for all non-chapter content.
 */
function book_wp_non_chapter_html_from_blocks( $blocks ) {
    $html = '';

    foreach ( $blocks as $block ) {
        $name = isset( $block['blockName'] ) ? $block['blockName'] : null;
        if ( 'book-wp/chapter' === $name ) continue;

        if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
            $html .= ' ' . book_wp_non_chapter_html_from_blocks( $block['innerBlocks'] );
            continue;
        }

        if ( isset( $block['innerHTML'] ) && '' !== $block['innerHTML'] ) {
            $html .= ' ' . $block['innerHTML'];
        } elseif ( ! empty( $block['innerContent'] ) && is_array( $block['innerContent'] ) ) {
            $html .= ' ' . implode( ' ', array_filter( $block['innerContent'], 'is_string' ) );
        }
    }

    return trim( $html );
}

/**
 * Return parsed chapter block attributes from fallback regex matches.
 *
 * @param string $raw_content Raw post_content field value.
 * @return array[] Chapter block attribute arrays.
 */
function book_wp_chapter_blocks_fallback( $raw_content ) {
    $blocks = array();

    preg_match_all(
        '/<!--\s*wp:book-wp\/chapter\s+(\{.*?\})\s*\/-->/s',
        $raw_content,
        $matches
    );

    foreach ( $matches[1] as $json ) {
        $attrs = json_decode( $json, true );
        if ( ! is_array( $attrs ) ) continue;
        $blocks[] = array(
            'blockName'   => 'book-wp/chapter',
            'attrs'       => $attrs,
            'innerBlocks' => array(),
        );
    }

    return $blocks;
}

/**
 * Flatten chapter block attributes from a parsed block tree.
 *
 * @param array $blocks Parsed block array.
 * @return array[] Chapter block attribute arrays.
 */
function book_wp_flatten_chapter_attrs( $blocks ) {
    $attrs = array();

    foreach ( $blocks as $block ) {
        $name = isset( $block['blockName'] ) ? $block['blockName'] : null;
        if ( 'book-wp/chapter' === $name ) {
            $attrs[] = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
        }

        if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
            $attrs = array_merge( $attrs, book_wp_flatten_chapter_attrs( $block['innerBlocks'] ) );
        }
    }

    return $attrs;
}

/**
 * Get the full readable text of a post, including chapter block content.
 *
 * @param int $post_id
 * @return string Combined plain text.
 */
function book_wp_get_full_text( $post_id ) {
    // Static cache: full-text extraction for a given post ID is expensive
    // (block parsing over raw post_content for chapter blocks). Cache the
    // result for the duration of the request so book_wp_reading_time() and
    // book_wp_word_count() — both called from book_wp_analytics_bar() — share
    // a single extraction pass.
    static $cache = array();
    if ( isset( $cache[ $post_id ] ) ) return $cache[ $post_id ];

    $raw     = get_post_field( 'post_content', $post_id );
    $chapter = book_wp_extract_chapter_text( $raw );

    // Strip chapter blocks so we don't double-count anything that may appear
    // in both raw post body and chapter attributes.
    $body = book_wp_remove_chapter_blocks( $raw );
    $body = wp_strip_all_tags( $body );

    $cache[ $post_id ] = trim( $body . ' ' . $chapter );
    return $cache[ $post_id ];
}

function book_wp_reading_time_minutes( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $words = str_word_count( book_wp_get_full_text( $post_id ) );
    $wpm   = book_wp_is_poetry( $post_id ) ? 120 : 200;
    return max( 1, (int) ceil( $words / $wpm ) );
}

function book_wp_reading_time( $post_id = 0 ) {
    return sprintf( __( '%d min read', 'book-wp' ), book_wp_reading_time_minutes( $post_id ) );
}

function book_wp_word_count( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    return str_word_count( book_wp_get_full_text( $post_id ) );
}

function book_wp_line_count( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $raw = get_post_field( 'post_content', $post_id );

    // For chapter blocks: each verse paragraph = one line.
    $chapter_text = book_wp_extract_chapter_text( $raw );

    // Regular post body (non-chapter content).
    $body = book_wp_remove_chapter_blocks( $raw );
    $body = preg_replace( '/<br\s*\/?>/', "\n", $body );
    $body = preg_replace( '/<\/p>/i',     "\n", $body );
    $body = wp_strip_all_tags( $body );

    // Combine and count non-empty lines.
    $combined = $body . "\n" . $chapter_text;
    $lines    = preg_split( '/\r\n|\r|\n/', trim( $combined ) );
    return count( array_filter( $lines, function ( $l ) { return '' !== trim( $l ); } ) );
}

function book_wp_stanza_count( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $content = get_post_field( 'post_content', $post_id );

    // Count chapter blocks — each block = one stanza group; count individual
    // verse <p> tags within them for the true stanza count.
    $chapter_stanzas = 0;
    if ( false !== strpos( $content, 'wp:book-wp/chapter' ) ) {
        $blocks = function_exists( 'parse_blocks' )
            ? parse_blocks( $content )
            : book_wp_chapter_blocks_fallback( $content );
        foreach ( book_wp_flatten_chapter_attrs( $blocks ) as $attrs ) {
            if ( ! empty( $attrs['content'] ) ) {
                preg_match_all( '/<p[^>]*>.*?<\/p>/is', $attrs['content'], $pm );
                $chapter_stanzas += count( array_filter( $pm[0], function( $p ) {
                    return '' !== trim( wp_strip_all_tags( $p ) );
                } ) );
            }
            if ( ! empty( $attrs['verses'] ) ) {
                $chapter_stanzas += count( array_filter( $attrs['verses'], 'trim' ) );
            }
        }
    }

    // Strip chapter blocks from content before regular stanza counting.
    $body = book_wp_remove_chapter_blocks( $content );

    $body_stanzas = 0;
    if ( false !== strpos( $body, '<!-- wp:' ) ) {
        $body_stanzas = max( 0, preg_match_all( '/<!-- wp:(?:paragraph|verse)\b/', $body ) );
    } elseif ( trim( $body ) ) {
        preg_match_all( '/<p(?:\s[^>]*)?>(.*?)<\/p>/si', $body, $matches );
        if ( ! empty( $matches[1] ) ) {
            $body_stanzas = count( array_filter( $matches[1], function ( $b ) {
                return '' !== trim( wp_strip_all_tags( $b ) );
            } ) );
        }
    }

    $total = $chapter_stanzas + $body_stanzas;
    return max( 1, $total );
}

function book_wp_analytics_bar( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $slug = book_wp_get_form_slug( $post_id );
    $is_p = book_wp_is_poetry( $post_id );
    $html = '<div class="bwp-analytics">';

    if ( $slug ) {
        $html .= '<span class="bwp-badge bwp-badge--' . esc_attr( $slug ) . '">'
            . esc_html( book_wp_get_form_label( $post_id ) ) . '</span>';
    }

    $cats = get_the_category( $post_id );
    if ( $cats && ! is_wp_error( $cats ) && 'uncategorized' !== $cats[0]->slug ) {
        $html .= '<span class="bwp-analytics__item bwp-analytics__cat">'
            . esc_html( $cats[0]->name ) . '</span>';
    }

    $html .= '<span class="bwp-analytics__item">' . esc_html( book_wp_reading_time( $post_id ) ) . '</span>';
    $html .= '<span class="bwp-analytics__item">'
        . esc_html( sprintf( __( '%d words', 'book-wp' ), book_wp_word_count( $post_id ) ) ) . '</span>';

    if ( $is_p ) {
        // Ratapan is poetical prose structured as chapters/verses — line count
        // and stanza count are not meaningful metrics for this form.
        if ( 'ratapan' !== $slug ) {
            $html .= '<span class="bwp-analytics__item">'
                . esc_html( sprintf( __( '%d lines', 'book-wp' ), book_wp_line_count( $post_id ) ) ) . '</span>';
            $sc = book_wp_stanza_count( $post_id );
            if ( $sc > 1 ) {
                $html .= '<span class="bwp-analytics__item">'
                    . esc_html( sprintf( __( '%d stanzas', 'book-wp' ), $sc ) ) . '</span>';
            }
        }
    }

    return $html . '</div>';
}
