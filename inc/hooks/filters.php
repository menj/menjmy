<?php
/**
 * Book WP — Filter Hooks
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/* ── Body Classes ──────────────────────────────────────────────────────── */

add_filter( 'body_class', 'book_wp_body_classes' );

function book_wp_body_classes( $classes ) {
    $classes[] = 'bwp-scheme-' . sanitize_key( get_option( 'book_wp_color_scheme', 'default' ) );
    $classes[] = 'bwp-font-'   . sanitize_key( get_option( 'book_wp_font', 'georgia' ) );
    // Language class uses WordPress locale — book_wp_language option was removed in v3.2.3.
    $classes[] = 'bwp-lang-' . sanitize_key( substr( get_locale(), 0, 2 ) );

    // Per-form drop cap — each prose form has its own setting.
    // Poetry forms (puisi, syair, karmina, ratapan) are never included here.
    $drop_cap_forms = array(
        'cerpen'  => '1',   // default on
        'novel'   => '1',   // default on
        'hikayat' => '1',   // default on
        'memoir'  => '1',   // default on
        'esei'    => '0',   // default off
        'nukilan' => '0',   // default off
    );
    foreach ( $drop_cap_forms as $form => $default ) {
        if ( '1' === get_option( 'book_wp_drop_cap_' . $form, $default ) ) {
            $classes[] = 'bwp-dropcap-' . $form;
        }
    }

    // Per-post override — added on single pages only.
    if ( is_single() ) {
        $override = get_post_meta( get_the_ID(), 'book_wp_drop_cap_override', true );
        if ( '1' === $override ) $classes[] = 'bwp-dropcap-force-on';
        if ( '0' === $override ) $classes[] = 'bwp-dropcap-force-off';
    }

    // Poetry typography — line height, stanza spacing, and alignment
    $lh      = get_option( 'book_wp_poetry_line_height', 'normal' );
    $spacing = get_option( 'book_wp_stanza_spacing',     'comfortable' );
    $align   = get_option( 'book_wp_poetry_align',       'left' );
    $classes[] = 'bwp-poetry-lh-' . sanitize_key( $lh );
    $classes[] = 'bwp-stanza-' . sanitize_key( $spacing );
    if ( 'center' === $align ) $classes[] = 'bwp-poetry-centred';

    if ( '1' === get_option( 'book_wp_stanza_numbers', '0' ) ) $classes[] = 'bwp-stanza-nums';

    if ( is_single() ) {
        $slug = book_wp_get_form_slug();
        if ( $slug ) $classes[] = 'bwp-form-' . $slug;

        // Decorative tag classes — add bwp-tag-{slug} for each tag carried by the post.
        // Currently used to conditionally load per-tag CSS (e.g. erotika batik background).
        // Hidden tags are excluded so their slugs are not exposed in the HTML source.
        $tags         = get_the_tags();
        $hidden_slugs = book_wp_hidden_tag_slugs();
        if ( $tags && ! is_wp_error( $tags ) ) {
            foreach ( $tags as $tag ) {
                if ( in_array( $tag->slug, $hidden_slugs, true ) ) continue;
                $classes[] = 'bwp-tag-' . sanitize_key( $tag->slug );
            }
        }
    }

    // Archive layout classes — used by JS to detect context and by CSS for grid/masonry.
    if ( is_archive() || is_tag() ) {
        $classes[] = 'bwp-is-archive';
        $layout = get_option( 'book_wp_archive_layout', 'list' );
        if ( in_array( $layout, array( 'grid', 'masonry' ), true ) ) {
            $classes[] = 'bwp-archive-' . $layout;
        }
    }

    // TOC flat-list layout classes — only when grouping is None.
    if ( is_home() && 'none' === get_option( 'book_wp_toc_grouping', 'none' ) ) {
        $classes[] = 'bwp-is-toc-flat';
        $toc_layout = get_option( 'book_wp_toc_layout', 'list' );
        if ( in_array( $toc_layout, array( 'grid', 'masonry' ), true ) ) {
            $classes[] = 'bwp-toc-' . $toc_layout;
        }
    }

    return $classes;
}

/* ── Page Templates ────────────────────────────────────────────────────── */

add_filter( 'theme_page_templates', 'book_wp_register_page_templates' );

function book_wp_register_page_templates( $templates ) {
    $templates['templates/template-foreword.php'] = __( 'Foreword', 'book-wp' );
    $templates['templates/template-colophon.php']   = __( 'Colophon Page', 'book-wp' );
    return $templates;
}

/* ── Search Block — translate button label ─────────────────────────────── */
/*                                                                           */
/* The Gutenberg core/search block renders its own HTML and does not call   */
/* searchform.php, so theme translation via __() has no effect on it.       */
/* This filter rewrites the rendered block HTML to replace the button's     */
/* value/label with the theme's translated string for 'Search'.             */
/*                                                                           */
/* Targets only core/search blocks — no other blocks are affected.          */

add_filter( 'render_block_core/search', 'book_wp_translate_search_block', 10, 1 );

function book_wp_translate_search_block( $block_content ) {
    $label = esc_attr( __( 'Search', 'book-wp' ) );

    // Button element: <button ...>Search</button> or value="Search" on <input type="submit">
    $block_content = preg_replace(
        '/(<button[^>]*>)\s*[^<]+\s*(<\/button>)/i',
        '${1}' . esc_html( __( 'Search', 'book-wp' ) ) . '${2}',
        $block_content
    );

    // Input submit fallback: value="Search"
    $block_content = preg_replace(
        '/(<input[^>]+type=["\']submit["\'][^>]+)value=["\'][^"\']*["\']/i',
        '${1}value="' . $label . '"',
        $block_content
    );
    $block_content = preg_replace(
        '/(<input[^>]+)value=["\'][^"\']*["\']([^>]+type=["\']submit["\'])/i',
        '${1}value="' . $label . '"${2}',
        $block_content
    );

    return $block_content;
}

/* ── Hidden tag suppression from term list output ──────────────────────── */
/*                                                                           */
/* the_tags() and get_the_tag_list() both run through wp_get_object_terms() */
/* and then pass through the 'the_tags' filter. Removing hidden tags here   */
/* ensures they never appear in rendered tag link lists, regardless of       */
/* which template, widget, or helper calls the function.                    */
/*                                                                           */
/* Note: the single-post display in single.php already filters via          */
/* $thematic. This filter provides a safety net for any other output path.  */

add_filter( 'the_tags', 'book_wp_suppress_hidden_tags_from_output', 10, 4 );

/**
 * Remove hidden tags from the rendered tag link string produced by the_tags().
 *
 * @param string $tag_list HTML string of tag links, or false if none.
 * @param string $before   String before the tags.
 * @param string $sep      Separator between tag links.
 * @param string $after    String after the tags.
 * @return string|false
 */
function book_wp_suppress_hidden_tags_from_output( $tag_list, $before, $sep, $after ) {
    if ( ! $tag_list ) return $tag_list;

    $hidden_ids = book_wp_hidden_tag_ids();
    if ( empty( $hidden_ids ) ) return $tag_list;

    // Rebuild from the actual post tags rather than parsing the HTML string.
    global $post;
    if ( ! $post ) return $tag_list;

    $all_tags = get_the_tags( $post->ID );
    if ( ! $all_tags || is_wp_error( $all_tags ) ) return $tag_list;

    $visible = array_filter( $all_tags, function( $tag ) use ( $hidden_ids ) {
        return ! in_array( (int) $tag->term_id, $hidden_ids, true );
    } );

    if ( empty( $visible ) ) return false;

    // If nothing was filtered, return original string unchanged (no work needed).
    if ( count( $visible ) === count( $all_tags ) ) return $tag_list;

    $links = array_map( function( $tag ) {
        return '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" rel="tag">' . esc_html( $tag->name ) . '</a>';
    }, $visible );

    return $before . implode( $sep, $links ) . $after;
}
