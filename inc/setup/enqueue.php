<?php
/**
 * Sasterawan — Asset Enqueueing
 *
 * All wp_enqueue_style / wp_enqueue_script calls live here.
 * Never hardcode asset paths in templates — use these handles.
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'book_wp_enqueue_frontend' );

function book_wp_enqueue_frontend() {

    // Base stylesheet (style.css — theme header + minimal resets).
    wp_enqueue_style(
        'book-wp-style',
        get_stylesheet_uri(),
        array(),
        BOOK_WP_VERSION
    );

    $remote_fonts = '1' === get_option( 'book_wp_remote_fonts', '1' );
    $style_deps   = array( 'book-wp-style' );

    // UI fonts — Cinzel (display), EB Garamond (body), Jost (UI).
    // Registered here via wp_enqueue_style() so the browser can initiate
    // the request in parallel with frontend.css. A CSS @import inside
    // frontend.css would create a serialised fetch chain — the browser
    // cannot discover the font URL until frontend.css has fully downloaded
    // and been parsed. Loading here eliminates that chain. (CWV-01)
    //
    // Preconnect hints for fonts.googleapis.com and fonts.gstatic.com are
    // emitted only when remote fonts are enabled. Sites with strict privacy
    // requirements can disable all Google Fonts in Typography settings.
    if ( $remote_fonts ) {
        add_filter( 'wp_resource_hints', function ( $hints, $relation_type ) {
            if ( 'preconnect' === $relation_type ) {
                $hints[] = array( 'href' => 'https://fonts.googleapis.com' );
                $hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
            }
            return $hints;
        }, 9, 2 );

        wp_enqueue_style(
            'book-wp-ui-fonts',
            'https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500&family=EB+Garamond:ital,wght@0,400;0,500;1,400&family=Jost:wght@300;400;500;600&display=swap',
            array(),
            null
        );
        $style_deps[] = 'book-wp-ui-fonts';
    }

    // Primary design system.
    wp_enqueue_style(
        'book-wp-frontend',
        BOOK_WP_URI . '/assets/css/frontend.css',
        array( 'book-wp-style' ),
        BOOK_WP_VERSION
    );
    $style_deps[] = 'book-wp-frontend';

    // Classical Malay Literary theme layer — augments frontend.css, does not replace it.
    wp_enqueue_style(
        'book-wp-literary',
        BOOK_WP_URI . '/assets/css/literary.css',
        array( 'book-wp-frontend' ),
        BOOK_WP_VERSION
    );
    $style_deps[] = 'book-wp-literary';

    // Time-of-day theme CSS — all five palettes in a single file.
    // Previously: 5 separate files loaded unconditionally (5 HTTP requests).
    // Now: 1 file, 1 request. JS (time-theme.js) applies bwp-time-{period}
    // to <html> before first paint — the correct palette activates via CSS.
    wp_enqueue_style(
        'book-wp-times',
        BOOK_WP_URI . '/assets/css/times/times.css',
        array( 'book-wp-literary' ),
        BOOK_WP_VERSION
    );
    $style_deps[] = 'book-wp-times';

    // Literary form CSS — loaded only on single posts with a known form.
    if ( is_singular() ) {
        $form_slug = book_wp_get_form_slug();
        $form_files = array( 'syair', 'karmina', 'hikayat', 'puisi', 'cerpen', 'novel', 'nukilan', 'ratapan', 'esei', 'memoir' );
        if ( $form_slug && in_array( $form_slug, $form_files, true ) ) {
            wp_enqueue_style(
                'book-wp-form-' . $form_slug,
                BOOK_WP_URI . '/assets/css/forms/' . $form_slug . '.css',
                array( 'book-wp-times' ),
                BOOK_WP_VERSION
            );
            $style_deps[] = 'book-wp-form-' . $form_slug;

            // Classical drop cap fonts — loaded only on the two forms that use them.
            // Separate requests so only the needed font is fetched per page.
            // Skipped when a custom drop cap font is active — the @font-face
            // declaration in the dropcap stylesheet takes priority via
            // --bwp-dropcap-font, so loading the Google Font is wasted bytes.
            // Also skipped when remote fonts are disabled in Typography settings.
            $dc_font = get_option( 'book_wp_dropcap_font', 'default' );
            if ( $remote_fonts && 'default' === $dc_font ) {
                if ( 'hikayat' === $form_slug ) {
                    wp_enqueue_style(
                        'book-wp-imfell-sc',
                        'https://fonts.googleapis.com/css2?family=IM+Fell+English+SC&display=swap',
                        array(),
                        null
                    );
                    $style_deps[] = 'book-wp-imfell-sc';
                }
                if ( 'nukilan' === $form_slug ) {
                    wp_enqueue_style(
                        'book-wp-caslon-display',
                        'https://fonts.googleapis.com/css2?family=Libre+Caslon+Display&display=swap',
                        array(),
                        null
                    );
                    $style_deps[] = 'book-wp-caslon-display';
                }
            }

            // Custom drop cap font — loads the @font-face + --bwp-dropcap-font override.
            // Whitelist guards against path traversal — only known slugs accepted.
            $dc_font_whitelist = array( 'camelot', 'romantik', 'liturgisch', 'rediviva', 'gotische' );
            if ( in_array( $dc_font, $dc_font_whitelist, true ) ) {
                wp_enqueue_style(
                    'book-wp-dropcap-font',
                    BOOK_WP_URI . '/assets/css/dropcap/' . $dc_font . '.css',
                    array( 'book-wp-frontend' ),
                    BOOK_WP_VERSION
                );
                $style_deps[] = 'book-wp-dropcap-font';
            }
        }

        // Per-tag decorative CSS — loaded only when the post carries a tag
        // that has a corresponding stylesheet in assets/css/tags/.
        // Only whitelisted slugs are checked — no arbitrary file paths.
        $tag_css_slugs = array( 'erotika' );
        $post_tags     = get_the_tags();
        if ( $post_tags && ! is_wp_error( $post_tags ) ) {
            $post_tag_slugs = wp_list_pluck( $post_tags, 'slug' );
            foreach ( $tag_css_slugs as $tag_slug ) {
                if ( in_array( $tag_slug, $post_tag_slugs, true ) ) {
                    wp_enqueue_style(
                        'book-wp-tag-' . $tag_slug,
                        BOOK_WP_URI . '/assets/css/tags/' . $tag_slug . '.css',
                        array( 'book-wp-frontend' ),
                        BOOK_WP_VERSION
                    );
                    $style_deps[] = 'book-wp-tag-' . $tag_slug;
                }
            }
        }
    }

    // Time-theme JS — runs in <head> to prevent flash of wrong theme.
    // bookWP object is defined here first; frontend.js reads it at DOMContentLoaded.
    wp_enqueue_script(
        'book-wp-time-theme',
        BOOK_WP_URI . '/assets/js/time-theme.js',
        array(),
        BOOK_WP_VERSION,
        false
    );
    wp_localize_script( 'book-wp-time-theme', 'bookWP', array(
        'timeTheme'          => get_option( 'book_wp_time_theme',          'auto' ),
        'progressBar'        => get_option( 'book_wp_progress_bar',          '1' ),
        'keyboardNav'        => get_option( 'book_wp_keyboard_nav',           '1' ),
        'readingMemory'      => get_option( 'book_wp_reading_memory',         '1' ),
        'nightToggle'        => get_option( 'book_wp_night_toggle',           '1' ),
        'stanzaNumbers'      => get_option( 'book_wp_stanza_numbers',         '0' ),
        'archiveLayout'      => get_option( 'book_wp_archive_layout',      'list' ),
        'archivePagination'  => get_option( 'book_wp_archive_pagination', 'paged' ),
        'tocLayout'          => get_option( 'book_wp_toc_layout',          'list' ),
        'tocPagination'      => get_option( 'book_wp_toc_pagination',     'paged' ),
        'shareRow'           => get_option( 'book_wp_share_row',            '0' ),
        'backToTop'          => get_option( 'book_wp_back_to_top',          '1' ),
        /* translators: confirmation label shown briefly after the copy-link button is clicked */
        'copied'             => __( 'Copied!', 'book-wp' ),
    ) );

    // Optional colour scheme override.
    // Whitelist guards against path traversal — only known scheme slugs accepted.
    $scheme_whitelist = array( 'cream', 'dusk', 'night' );
    $scheme = get_option( 'book_wp_color_scheme', 'default' );
    if ( 'default' !== $scheme && in_array( $scheme, $scheme_whitelist, true ) ) {
        wp_enqueue_style(
            'book-wp-scheme',
            BOOK_WP_URI . '/assets/css/schemes/' . $scheme . '.css',
            array( 'book-wp-times' ),   // scheme loads after times — compound selectors require it
            BOOK_WP_VERSION
        );
        $style_deps[] = 'book-wp-scheme';
    }

    // Optional Google Font + local font-family override.
    // $gfonts acts as both the Google Fonts parameter map and the font whitelist.
    // Only slugs present in this array can produce a file path — no sanitize_file_name needed.
    $font = get_option( 'book_wp_font', 'georgia' );
    if ( 'georgia' !== $font ) {
        $gfonts = array(
            'vollkorn' => 'Vollkorn:ital,wght@0,400;0,700;1,400;1,700',
            'arvo'     => 'Arvo:ital,wght@0,400;0,700;1,400;1,700',
            'maven'    => 'Maven+Pro:wght@400;500;700;900',
            'ubuntu'   => 'Ubuntu:ital,wght@0,400;0,700;1,400;1,700',
            'amiri'    => 'Amiri:ital,wght@0,400;0,700;1,400;1,700',
            'lora'     => 'Lora:ital,wght@0,400;0,500;1,400;1,500',
        );

        if ( isset( $gfonts[ $font ] ) ) {
            if ( $remote_fonts ) {
                wp_enqueue_style(
                    'book-wp-gfont',
                    'https://fonts.googleapis.com/css2?family=' . $gfonts[ $font ] . '&display=swap',
                    array(),
                    null
                );
                $style_deps[] = 'book-wp-gfont';
            }

            // Local font CSS — slug already validated against $gfonts whitelist above.
            // In remote-fonts-off mode, this still sets the preferred font-family so
            // visitor-installed fonts can be used before falling back to Georgia.
            $local_font_path = BOOK_WP_DIR . '/assets/css/fonts/' . $font . '.css';
            if ( file_exists( $local_font_path ) ) {
                wp_enqueue_style(
                    'book-wp-font',
                    BOOK_WP_URI . '/assets/css/fonts/' . $font . '.css',
                    array( 'book-wp-frontend' ),
                    BOOK_WP_VERSION
                );
                $style_deps[] = 'book-wp-font';
            }
        }
    }

    book_wp_enqueue_custom_css_inline( $style_deps );

    // Main JS — loaded in footer.
    wp_enqueue_script(
        'book-wp-frontend',
        BOOK_WP_URI . '/assets/js/frontend.js',
        array(),
        BOOK_WP_VERSION,
        true
    );

    // Comment threading script — only when needed.
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // ── Vendor: Masonry + imagesLoaded + Infinite Scroll ─────────────────
    // Registered unconditionally so dependencies resolve correctly;
    // enqueued only on archive / tag pages when the relevant option is set.
    //
    // Masonry:        cascading grid layout for archive cards.
    // imagesLoaded:   gates Masonry init until all images are loaded —
    //                 prevents height miscalculation on cards with images.
    // Infinite Scroll: intercepts WordPress pagination links and appends
    //                 the next page without a full reload.

    wp_register_script(
        'book-wp-imagesloaded',
        BOOK_WP_URI . '/assets/js/vendor/imagesloaded.min.js',
        array(),
        '5.0.0',
        true
    );
    wp_register_script(
        'book-wp-masonry',
        BOOK_WP_URI . '/assets/js/vendor/masonry.min.js',
        array( 'book-wp-imagesloaded' ),
        '4.2.2',
        true
    );
    wp_register_script(
        'book-wp-infinite-scroll',
        BOOK_WP_URI . '/assets/js/vendor/infinite-scroll.min.js',
        array(),
        '5.0.0',
        true
    );

    // Only enqueue on archive and tag pages.
    if ( is_archive() || is_tag() ) {

        $archive_layout     = get_option( 'book_wp_archive_layout',     'list' );
        $archive_pagination = get_option( 'book_wp_archive_pagination', 'paged' );

        if ( 'masonry' === $archive_layout ) {
            wp_enqueue_script( 'book-wp-imagesloaded' );
            wp_enqueue_script( 'book-wp-masonry' );
        }

        if ( 'infinite' === $archive_pagination ) {
            wp_enqueue_script( 'book-wp-infinite-scroll' );
        }
    }

    // TOC page (index.php) — only when grouping is None, which is the only
    // mode where layout and pagination options are meaningful.
    if ( is_home() && 'none' === get_option( 'book_wp_toc_grouping', 'none' ) ) {

        $toc_layout     = get_option( 'book_wp_toc_layout',     'list' );
        $toc_pagination = get_option( 'book_wp_toc_pagination', 'paged' );

        if ( 'masonry' === $toc_layout ) {
            wp_enqueue_script( 'book-wp-imagesloaded' );
            wp_enqueue_script( 'book-wp-masonry' );
        }

        if ( 'infinite' === $toc_pagination ) {
            wp_enqueue_script( 'book-wp-infinite-scroll' );
        }
    }
}

/**
 * Attach user custom CSS to a synthetic style handle.
 *
 * The handle depends on every theme stylesheet enqueued for the current
 * request, so the inline CSS prints after scheme, form, tag, and font layers.
 *
 * @param string[] $dependencies Enqueued style handles that custom CSS should follow.
 */
function book_wp_enqueue_custom_css_inline( $dependencies ) {
    $css = get_option( 'book_wp_custom_css', '' );
    if ( '' === trim( (string) $css ) ) return;

    wp_register_style(
        'book-wp-custom',
        false,
        array_values( array_unique( $dependencies ) ),
        BOOK_WP_VERSION
    );
    wp_enqueue_style( 'book-wp-custom' );
    wp_add_inline_style( 'book-wp-custom', $css );
}

/* ── Block editor assets ────────────────────────────────────────────────── */

add_action( 'enqueue_block_editor_assets', 'book_wp_enqueue_block_editor_assets' );

function book_wp_enqueue_block_editor_assets() {
    // blocks.js — registers book-wp/chapter and any future blocks.
    // Depends on the core wp.* globals WordPress loads automatically in the editor.
    wp_enqueue_script(
        'book-wp-blocks',
        BOOK_WP_URI . '/assets/js/blocks.js',
        array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n', 'wp-rich-text' ),
        BOOK_WP_VERSION,
        true
    );
    // Enable JS i18n: WordPress loads the JSON translation file from languages/
    // when the locale is non-English. File naming: {textdomain}-{locale}-{script-handle-hash}.json
    wp_set_script_translations( 'book-wp-blocks', 'book-wp', BOOK_WP_DIR . '/languages' );

    // Load front-end design tokens into the editor canvas so the chapter
    // preview inherits the correct colours and fonts.
    wp_enqueue_style(
        'book-wp-blocks-editor',
        BOOK_WP_URI . '/assets/css/frontend.css',
        array( 'wp-edit-blocks' ),
        BOOK_WP_VERSION
    );
}
