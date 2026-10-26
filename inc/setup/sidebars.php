<?php
/**
 * Book WP — Widget Areas (Sidebars)
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

add_action( 'widgets_init', 'book_wp_register_sidebars' );

function book_wp_register_sidebars() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'book-wp' ),
        'id'            => 'sidebar-widgets',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer', 'book-wp' ),
        'id'            => 'footer-widgets',
        'before_widget' => '<div id="%1$s" class="bwp-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="bwp-footer-widget__title">',
        'after_title'   => '</h3>',
    ) );
}
