<?php
/**
 * Sasterawan — Shortcodes
 *
 * Presentation-focused shortcodes for literary content.
 * These shortcodes control display only — no content is stored via them.
 *
 * Prefixed handles (bwp_*) are the canonical registrations.
 * Friendly aliases ([term], [quote], etc.) are registered at the bottom
 * of this file for convenience when writing in the Classic editor.
 *
 * In the block editor, dedicated blocks and the book-wp/term inline format
 * type replace shortcode usage — see assets/js/blocks.js.
 *
 * [bwp_quote]   — Styled literary quotation block: [quote source="..."]...[/quote]
 * [bwp_divider] — Decorative section divider: [divider style="fleuron"]
 * [bwp_term]    — Glossary tooltip: [term definition="..."]word[/term]
 * [bwp_verse]   — Numbered verse block: [verse num="1"]...[/verse]
 * [bwp_chapter] — Bible-style chapter with auto-numbered verses (ratapan):
 *             [chapter num="1" title="optional"]verse one...
 *
 *             verse two...[/chapter]
 * [bwp_v]       — Inline verse number marker: [v num="3"]
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/* ═══════════════════════════════════════════════════════════════════════════
   CONTENT FILTERS — run before and after do_shortcode (priority 11)
   ═══════════════════════════════════════════════════════════════════════════ */

/**
 * Normalise smart/curly quotes inside shortcode attribute values.
 *
 * Gutenberg's visual editor substitutes " " ' ' for straight ASCII quotes
 * in attribute values, breaking the shortcode regex parser. This filter runs
 * at priority 10 (before do_shortcode at 11) and converts them back.
 *
 * Only operates on the attribute portion of shortcode tags — text content
 * is intentionally left untouched so the author's typographic choices
 * in the body copy are preserved.
 */
add_filter( 'the_content', 'book_wp_normalize_shortcode_quotes', 10 );

function book_wp_normalize_shortcode_quotes( $content ) {
    // Match opening shortcode tags only: [tagname ...attrs... ] or [tagname ...attrs.../]
    // Capture the attribute string for transformation.
    return preg_replace_callback(
        '/\[(\w+)([ \t][^\]]*?)?(\/?\])/s',
        function( $m ) {
            if ( empty( $m[2] ) ) return $m[0]; // no attributes — nothing to do
            $attrs = $m[2];
            // Left double quotation mark  U+201C →  "
            // Right double quotation mark U+201D →  "
            // Left single quotation mark  U+2018 →  '
            // Right single quotation mark U+2019 →  '
            $attrs = str_replace(
                array( "\u{201C}", "\u{201D}", "\u{2018}", "\u{2019}" ),
                array( '"',        '"',        "'",         "'" ),
                $attrs
            );
            return '[' . $m[1] . $attrs . $m[3];
        },
        $content
    );
}

/**
 * Hydrate `<span class="bwp-istilah" data-definition="...">` elements
 * produced by the Gutenberg book-wp/term inline format into the full
 * tooltip structure that the PHP [term] shortcode outputs.
 *
 * Runs at priority 12 — after do_shortcode (11) so the shortcode path
 * is unaffected and both paths produce identical HTML.
 */
add_filter( 'the_content', 'book_wp_hydrate_term_spans', 12 );

function book_wp_hydrate_term_spans( $content ) {
    if ( false === strpos( $content, 'bwp-istilah' ) ) return $content;
    return preg_replace_callback(
        '/<span\b([^>]*?)\bclass="bwp-istilah"\b([^>]*?)>(.*?)<\/span>/si',
        function( $m ) {
            $attr_before = $m[1];
            $attr_after  = $m[2];
            $word        = $m[3];
            $all_attrs   = $attr_before . $attr_after;

            // Already a fully-formed span (from PHP shortcode): has aria-label but
            // no data-definition. Pass through unchanged to avoid double-processing.
            if ( false !== strpos( $all_attrs, 'aria-label' ) &&
                 false === strpos( $all_attrs, 'data-definition' ) ) {
                return $m[0];
            }

            if ( ! preg_match( '/\bdata-definition="([^"]*)"/i', $all_attrs, $dm ) ) {
                return $m[0]; // No definition attribute found — leave as-is.
            }

            $definition = html_entity_decode( $dm[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            $word_plain = wp_strip_all_tags( $word );

            return '<span class="bwp-istilah" tabindex="0" aria-label="'
                . esc_attr( $word_plain . ': ' . $definition ) . '">'
                . $word
                . '<span class="bwp-istilah__tip" role="tooltip" aria-hidden="true">'
                . esc_html( $definition )
                . '</span>'
                . '</span>';
        },
        $content
    );
}

/**
 * Extend wp_kses_allowed_html so the data-definition attribute on .bwp-istilah
 * spans survives WordPress's post content sanitiser for non-admin users.
 * Without this, data-definition would be stripped on save via the REST API.
 */
add_filter( 'wp_kses_allowed_html', 'book_wp_kses_allow_term_attrs', 10, 2 );

function book_wp_kses_allow_term_attrs( $tags, $context ) {
    if ( ! in_array( $context, array( 'post', 'user_description' ), true ) ) {
        return $tags;
    }
    if ( ! isset( $tags['span'] ) ) {
        $tags['span'] = array();
    }
    $tags['span']['class']           = true;
    $tags['span']['tabindex']        = true;
    $tags['span']['aria-label']      = true;
    $tags['span']['aria-hidden']     = true;
    $tags['span']['role']            = true;
    $tags['span']['data-definition'] = true;
    return $tags;
}

/* ═══════════════════════════════════════════════════════════════════════════
   SHORTCODE HANDLERS
   ═══════════════════════════════════════════════════════════════════════════ */

/* ── [quote source="..."] ──────────────────────────────────────────────── */

add_shortcode( 'bwp_quote', 'book_wp_quote_shortcode' );

function book_wp_quote_shortcode( $atts, $content = '' ) {
    $atts = shortcode_atts( array( 'source' => '' ), $atts, 'bwp_quote' );
    $html = '<blockquote class="bwp-petikan"><div class="bwp-petikan__text">' . wp_kses_post( $content ) . '</div>';
    if ( $atts['source'] ) {
        $html .= '<cite class="bwp-petikan__source">&mdash; ' . esc_html( $atts['source'] ) . '</cite>';
    }
    return $html . '</blockquote>';
}

/* ── [divider style="fleuron"] ─────────────────────────────────────────── */

add_shortcode( 'bwp_divider', 'book_wp_divider_shortcode' );

function book_wp_divider_shortcode( $atts ) {
    $atts   = shortcode_atts( array( 'style' => '' ), $atts, 'bwp_divider' );
    $chosen = $atts['style'] ?: get_option( 'book_wp_ornament_style', 'fleuron' );
    return book_wp_ornament( $chosen );
}

/* ── [term definition="..."]word[/term] ────────────────────────────────── */

add_shortcode( 'bwp_term', 'book_wp_term_shortcode' );

function book_wp_term_shortcode( $atts, $content = '' ) {
    $atts = shortcode_atts( array( 'definition' => '' ), $atts, 'bwp_term' );
    if ( ! $atts['definition'] || ! $content ) return $content;
    // aria-label exposes the definition to screen readers when the span is focused.
    // The visual tooltip (role=tooltip) is aria-hidden to avoid double-reading.
    // tabindex=0 makes the span keyboard-focusable without a mouse.
    return '<span class="bwp-istilah" tabindex="0" aria-label="'
        . esc_attr( wp_strip_all_tags( $content ) . ': ' . $atts['definition'] ) . '">'
        . wp_kses_post( $content )
        . '<span class="bwp-istilah__tip" role="tooltip" aria-hidden="true">' . esc_html( $atts['definition'] ) . '</span>'
        . '</span>';
}

/* ── [verse num="1"]...[/verse] ────────────────────────────────────────── */

add_shortcode( 'bwp_verse', 'book_wp_verse_shortcode' );

function book_wp_verse_shortcode( $atts, $content = '' ) {
    $atts = shortcode_atts( array( 'num' => '' ), $atts, 'bwp_verse' );
    $html = '<div class="bwp-bait">';
    $atts['num'] = intval( $atts['num'] );
    if ( $atts['num'] ) {
        $html .= '<span class="bwp-bait__num">' . esc_html( $atts['num'] ) . '</span>';
    }
    return $html . '<div class="bwp-bait__text">' . wp_kses_post( nl2br( trim( $content ) ) ) . '</div></div>';
}

/* ── [chapter num="1" title="..."]...[/chapter] ───────────────────────── */
/*                                                                           */
/* Bible-style chapter apparatus for Ratapan (Lamentations) form.           */
/* Auto-numbers every paragraph inside the chapter as a verse — no per-line */
/* shortcodes needed. Use [v num="n"] inside for manual verse markers.       */
/*                                                                           */
/* Usage:                                                                    */
/*   [chapter num="1" title="Dalam Kesunyian"]                              */
/*   First verse of lament.                                                  */
/*                                                                           */
/*   Second verse.                                                           */
/*   [/chapter]                                                              */

add_shortcode( 'bwp_chapter', 'book_wp_chapter_shortcode' );

function book_wp_chapter_shortcode( $atts, $content = '' ) {
    $atts = shortcode_atts( array(
        'num'         => '1',
        'title'       => '',
        'verse_start' => '1',   // start verse numbering from this number
    ), $atts, 'bwp_chapter' );
    $atts['num']         = max( 1, intval( $atts['num'] ) );
    $atts['verse_start'] = max( 1, intval( $atts['verse_start'] ) );

    // Process inner shortcodes, apply wpautop, strip empty <p> tags.
    $processed = wpautop( do_shortcode( $content ) );
    $processed = preg_replace( '/<p>(\s|&nbsp;|<br\s*\/?>)*<\/p>/i', '', $processed );
    $processed = trim( $processed );

    // If no manual [v] markers, auto-number every <p> as a verse.
    if ( false === strpos( $processed, 'bwp-vnum' ) ) {
        $v = intval( $atts['verse_start'] );
        $processed = preg_replace_callback(
            '/<p([ >])/',
            function ( $m ) use ( &$v ) {
                $vnum_html = '<span class="bwp-vnum" aria-label="'
                    . esc_attr( __( 'Verse', 'book-wp' ) . ' ' . $v )
                    . '" data-v="' . $v . '">' . $v . '</span>';
                $v++;
                return '<p' . $m[1] . $vnum_html;
            },
            $processed
        );
    }

    // Title above the content block (optional — small caps label)
    $title_html = $atts['title']
        ? '<span class="bwp-chapter__title">' . esc_html( $atts['title'] ) . '</span>'
        : '';

    // Chapter num floats left INSIDE the content div so verse 1 text wraps it.
    $num_html = '<span class="bwp-chapter__num" aria-label="'
        . esc_attr( __( 'Chapter', 'book-wp' ) . ' ' . $atts['num'] ) . '">'
        . esc_html( $atts['num'] ) . '</span>';

    return '<div class="bwp-chapter">'
        . $title_html
        . $num_html
        . '<div class="bwp-chapter__content">' . $processed . '</div>'
        . '</div>';
}

/* ── [v num="1"] — inline verse number marker ──────────────────────────── */
/*                                                                           */
/* For manual verse numbering within free-form content, or to number lines  */
/* that don't fall on paragraph boundaries.                                  */
/*                                                                           */
/* Usage: [v num="3"]This is verse three of the lament.                     */

add_shortcode( 'bwp_v', 'book_wp_vnum_shortcode' );

function book_wp_vnum_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'num' => '' ), $atts, 'bwp_v' );
    $num = intval( $atts['num'] );
    if ( ! $num ) return '';
    return '<span class="bwp-vnum" aria-label="'
        . esc_attr( __( 'Verse', 'book-wp' ) . ' ' . $num )
        . '" data-v="' . esc_attr( $num ) . '">'
        . esc_html( $num )
        . '</span>';
}

/* ═══════════════════════════════════════════════════════════════════════════
   FRIENDLY ALIASES — match the tags documented in Theme Options → Literary
   The bwp_* prefixed versions above are the canonical registrations.
   These aliases point to the same callbacks — no separate logic needed.
   ═══════════════════════════════════════════════════════════════════════════ */

add_shortcode( 'quote',   'book_wp_quote_shortcode' );
add_shortcode( 'divider', 'book_wp_divider_shortcode' );
add_shortcode( 'term',    'book_wp_term_shortcode' );
add_shortcode( 'verse',   'book_wp_verse_shortcode' );
add_shortcode( 'chapter', 'book_wp_chapter_shortcode' );
add_shortcode( 'v',       'book_wp_vnum_shortcode' );
