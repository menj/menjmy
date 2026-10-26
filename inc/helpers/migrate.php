<?php
/**
 * Sasterawan — upgrade path from Book WP (3.5.x)
 *
 * The data model is unchanged: every option (book_wp_*), post meta key,
 * the literary_form taxonomy, menu locations (primary, footer) and widget
 * areas (sidebar-widgets, footer-widgets) keep their names, so content and
 * settings carry over untouched when the theme is upgraded in place.
 *
 * What WordPress stores per theme *folder* are the theme mods: custom logo,
 * header and background images, and the Customizer's Additional CSS. If the
 * theme is installed under a new folder (book-wp → menjmy) WordPress treats
 * it as a different theme and starts those from empty. Core maps nav menu
 * locations across the switch on its own; this helper carries the rest.
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/**
 * Legacy theme slugs whose mods may be inherited.
 *
 * @return string[]
 */
function book_wp_legacy_theme_slugs() {
    return (array) apply_filters( 'book_wp_legacy_theme_slugs', array( 'book-wp', 'book-wp-main', 'bookwp' ) );
}

/**
 * Theme mods worth carrying from the previous folder.
 *
 * @return string[]
 */
function book_wp_migratable_theme_mods() {
    return array(
        'custom_logo',
        'header_image',
        'header_image_data',
        'background_image',
        'background_color',
        'background_preset',
        'background_position_x',
        'background_position_y',
        'background_size',
        'background_repeat',
        'background_attachment',
        'nav_menu_locations',
    );
}

/**
 * Copy theme mods from a previous Book WP folder when the current folder has
 * none of its own. Runs once per activation; never overwrites a mod that is
 * already set on the new theme.
 *
 * @return string[] The mods that were copied.
 */
function book_wp_migrate_theme_mods() {
    $current = get_stylesheet();
    $copied  = array();

    foreach ( book_wp_legacy_theme_slugs() as $slug ) {
        if ( $slug === $current ) {
            continue;
        }
        $legacy = get_option( 'theme_mods_' . $slug );
        if ( ! is_array( $legacy ) || empty( $legacy ) ) {
            continue;
        }
        foreach ( book_wp_migratable_theme_mods() as $key ) {
            if ( ! array_key_exists( $key, $legacy ) ) {
                continue;
            }
            $existing = get_theme_mod( $key, null );
            if ( null !== $existing && '' !== $existing && array() !== $existing ) {
                continue; // the new theme already has its own value
            }
            set_theme_mod( $key, $legacy[ $key ] );
            $copied[] = $key;
        }
        if ( $copied ) {
            break; // one legacy source is enough
        }
    }

    // Additional CSS lives in a post named after the theme folder; copy the
    // text into a post for this folder rather than sharing the legacy post.
    foreach ( book_wp_legacy_theme_slugs() as $slug ) {
        if ( $slug === $current || in_array( 'custom_css', $copied, true ) ) continue;
        $legacy = get_option( 'theme_mods_' . $slug );
        if ( empty( $legacy['custom_css_post_id'] ) || ! function_exists( 'wp_get_custom_css_post' ) ) continue;
        if ( wp_get_custom_css_post( $current ) ) continue; // the new folder already has its own
        $legacy_post = wp_get_custom_css_post( $slug );
        if ( $legacy_post && '' !== trim( (string) $legacy_post->post_content ) ) {
            $result = wp_update_custom_css_post( $legacy_post->post_content, array( 'stylesheet' => $current ) );
            if ( $result && ! is_wp_error( $result ) ) $copied[] = 'custom_css';
        }
    }

    if ( $copied ) {
        update_option( 'book_wp_migrated_from', $current . '<' . implode( ',', $copied ), false );
    }
    return $copied;
}

/**
 * 4.0 ships Sabon Next LT as the body face. On the first run of 4.x (theme
 * activation, or the terms-version upgrade step after an in-place update) the
 * stored Font choice moves to it once, so an upgraded site reads in the new
 * face; the other choices stay available under Settings → Typography.
 *
 * @return bool True when the option was changed.
 */
function book_wp_migrate_body_font() {
    if ( get_option( 'book_wp_font_migrated_40' ) ) return false;
    update_option( 'book_wp_font_migrated_40', '1', false );
    if ( 'sabon' === get_option( 'book_wp_font', 'sabon' ) ) return false;
    update_option( 'book_wp_font', 'sabon' );
    return true;
}

