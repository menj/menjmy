<?php
/**
 * Sasterawan — Theme Support & Core Setup
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'book_wp_setup' );

function book_wp_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );

    // Post formats — exposes WordPress native format UI in the block editor
    // and enables format-based queries and feed filtering.
    // aside → annotations and short prose notes
    // image → illustrated works, cover reveals
    // The literary_form taxonomy remains the primary classification mechanism
    // for all full-form works. Only formats with a genuine literary use case
    // are declared — the full nine-format set is intentionally not used.
    add_theme_support( 'post-formats', array(
        'aside', 'image',
    ) );

    // Selective refresh for widgets — refreshes only the affected widget area
    // in the Customizer preview instead of reloading the full iframe.
    // Standard for any classic theme with a registered sidebar.
    add_theme_support( 'customize-selective-refresh-widgets' );

    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ) );
    add_theme_support( 'custom-logo', array(
        'height'      => 120,
        'width'       => 120,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'custom-background', array( 'default-color' => 'ffffff' ) );

    // Block editor support — classic theme mode (no FSE).
    // theme.json handles palette, typography and spacing tokens.
    // editor.css mirrors front-end typography inside the Gutenberg editor.
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor.css' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'responsive-embeds' );

    add_image_size( 'book-wp-cover', 800, 400, true );
    add_image_size( 'book-wp-thumb',  80,  80, true ); // TOC flat-list thumbnail

    global $content_width;
    if ( ! isset( $content_width ) ) {
        $content_width = 680;
    }

    load_theme_textdomain( 'book-wp', BOOK_WP_DIR . '/languages' );
}
