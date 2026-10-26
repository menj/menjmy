<?php
/**
 * Book WP — Navigation Menus
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'book_wp_register_menus' );

function book_wp_register_menus() {
    register_nav_menus( array(
        'primary' => __( 'Primary Menu',    'book-wp' ),
        'footer'  => __( 'Footer Menu',     'book-wp' ),
    ) );
}
