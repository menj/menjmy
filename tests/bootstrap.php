<?php
/**
 * Book WP — PHPUnit Bootstrap
 *
 * Provides lightweight stubs for WordPress core functions so the
 * helper modules under inc/helpers/ can be loaded and tested in
 * isolation without a full WordPress installation.
 *
 * Usage:  phpunit -c config/phpunit.xml
 *
 * @package Book_WP\Tests
 */

/* ── Global mock registries ───────────────────────────────────────────── */

/**
 * Post fields registry:  $GLOBALS['_bwp_post_fields'][ $post_id ][ $field ]
 * Post meta registry:    $GLOBALS['_bwp_post_meta'][ $post_id ][ $key ]
 * Post terms registry:   $GLOBALS['_bwp_post_terms'][ $post_id ][ $taxonomy ]
 * Options registry:      $GLOBALS['_bwp_options'][ $name ]
 * Post tags registry:    $GLOBALS['_bwp_post_tags'][ $post_id ]  (array of tag objects)
 * Category registry:     $GLOBALS['_bwp_categories'][ $post_id ] (array of cat objects)
 * Current post ID:       $GLOBALS['_bwp_current_id']
 */

$GLOBALS['_bwp_post_fields'] = array();
$GLOBALS['_bwp_post_meta']   = array();
$GLOBALS['_bwp_post_terms']  = array();
$GLOBALS['_bwp_options']     = array();
$GLOBALS['_bwp_post_tags']   = array();
$GLOBALS['_bwp_categories']  = array();
$GLOBALS['_bwp_current_id']  = 0;

/**
 * Reset all mock registries between tests.
 */
function bwp_reset_mocks() {
    $GLOBALS['_bwp_post_fields'] = array();
    $GLOBALS['_bwp_post_meta']   = array();
    $GLOBALS['_bwp_post_terms']  = array();
    $GLOBALS['_bwp_options']     = array();
    $GLOBALS['_bwp_post_tags']   = array();
    $GLOBALS['_bwp_categories']  = array();
    $GLOBALS['_bwp_current_id']  = 0;
}

/* ── Stub helpers for setting up test fixtures ─────────────────────── */

function bwp_set_post_field( $post_id, $field, $value ) {
    $GLOBALS['_bwp_post_fields'][ $post_id ][ $field ] = $value;
}

function bwp_set_post_meta( $post_id, $key, $value ) {
    $GLOBALS['_bwp_post_meta'][ $post_id ][ $key ] = $value;
}

function bwp_set_post_terms( $post_id, $taxonomy, $terms ) {
    $GLOBALS['_bwp_post_terms'][ $post_id ][ $taxonomy ] = $terms;
}

function bwp_set_option( $name, $value ) {
    $GLOBALS['_bwp_options'][ $name ] = $value;
}

function bwp_set_post_tags( $post_id, $tags ) {
    $GLOBALS['_bwp_post_tags'][ $post_id ] = $tags;
}

function bwp_set_categories( $post_id, $cats ) {
    $GLOBALS['_bwp_categories'][ $post_id ] = $cats;
}

function bwp_set_current_id( $id ) {
    $GLOBALS['_bwp_current_id'] = $id;
}

/**
 * Make a mock taxonomy term object.
 */
function bwp_make_term( $slug, $name = '', $term_id = 0 ) {
    $t = new stdClass();
    $t->slug    = $slug;
    $t->name    = $name ?: ucfirst( $slug );
    $t->term_id = $term_id ?: crc32( $slug ) & 0x7FFFFFFF;
    return $t;
}

/**
 * Make a mock category object.
 */
function bwp_make_category( $slug, $name = '' ) {
    $c = new stdClass();
    $c->slug = $slug;
    $c->name = $name ?: ucfirst( $slug );
    return $c;
}

/**
 * Make a mock tag object.
 */
function bwp_make_tag( $slug, $name = '' ) {
    return bwp_make_term( $slug, $name );
}

/**
 * Make a mock WP_Post-like object.
 */
function bwp_make_post( $id, $title = '', $content = '' ) {
    $p = new stdClass();
    $p->ID           = $id;
    $p->post_title   = $title ?: "Post $id";
    $p->post_content = $content;
    if ( $content ) {
        bwp_set_post_field( $id, 'post_content', $content );
    }
    return $p;
}

/* ── WordPress core function stubs ────────────────────────────────── */

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', '/tmp/fake-wp/' );
}

function get_the_ID() {
    return $GLOBALS['_bwp_current_id'];
}

function get_post_field( $field, $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    return $GLOBALS['_bwp_post_fields'][ $post_id ][ $field ] ?? '';
}

function get_post_meta( $post_id, $key = '', $single = false ) {
    if ( $key ) {
        return $GLOBALS['_bwp_post_meta'][ $post_id ][ $key ] ?? '';
    }
    return $GLOBALS['_bwp_post_meta'][ $post_id ] ?? array();
}

function get_the_terms( $post_id, $taxonomy ) {
    $terms = $GLOBALS['_bwp_post_terms'][ $post_id ][ $taxonomy ] ?? null;
    return ! empty( $terms ) ? $terms : false;
}

function get_option( $name, $default = false ) {
    return array_key_exists( $name, $GLOBALS['_bwp_options'] )
        ? $GLOBALS['_bwp_options'][ $name ]
        : $default;
}

function get_the_tags( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $tags = $GLOBALS['_bwp_post_tags'][ $post_id ] ?? null;
    return ! empty( $tags ) ? $tags : false;
}

function get_the_category( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    return $GLOBALS['_bwp_categories'][ $post_id ] ?? array();
}

function wp_strip_all_tags( $string, $remove_breaks = false ) {
    return trim( strip_tags( $string ) );
}

function wp_kses_post( $data ) {
    return $data; // pass-through in tests
}

function esc_html( $text ) {
    return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

function esc_attr( $text ) {
    return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

function esc_url( $url ) {
    return filter_var( $url, FILTER_SANITIZE_URL ) ?: '';
}

function __( $text, $domain = 'default' ) {
    return $text;
}

function esc_html__( $text, $domain = 'default' ) {
    return esc_html( $text );
}

function esc_attr__( $text, $domain = 'default' ) {
    return esc_attr( $text );
}

function is_wp_error( $thing ) {
    return false;
}

function nl2br_compat( $string ) {
    return nl2br( $string );
}

function get_permalink( $post_id = 0 ) {
    return "https://example.com/?p={$post_id}";
}

function get_the_title( $post_id = 0 ) {
    return $GLOBALS['_bwp_post_fields'][ $post_id ]['post_title']
        ?? "Post {$post_id}";
}

function has_post_thumbnail( $post_id = 0 ) {
    return false;
}

function get_the_post_thumbnail( $post_id = 0, $size = '', $attr = array() ) {
    return '';
}

function get_the_post_thumbnail_caption( $post_id = 0 ) {
    return '';
}

function is_single() {
    return true;
}

function is_archive() {
    return false;
}

function get_adjacent_post( $same_term = false, $excluded = '', $previous = true ) {
    return null;
}

function wp_get_post_tags( $post_id, $args = array() ) {
    return array();
}

function get_posts( $args = array() ) {
    return array();
}

function wp_list_pluck( $list, $field ) {
    return array_map( function( $item ) use ( $field ) {
        return is_object( $item ) ? $item->$field : $item[ $field ];
    }, $list );
}

function wp_get_object_terms( $ids, $tax, $args = array() ) {
    return array();
}

function get_feed_link( $feed = '' ) {
    return 'https://example.com/feed/';
}

function sanitize_key( $key ) {
    return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $key ) );
}

/**
 * Stub for book_wp_hidden_tag_slugs() — defined in actions.php.
 * Tests can override via $GLOBALS['_bwp_hidden_tag_slugs'].
 */
$GLOBALS['_bwp_hidden_tag_slugs'] = array();

if ( ! function_exists( 'book_wp_hidden_tag_slugs' ) ) {
    function book_wp_hidden_tag_slugs() {
        return $GLOBALS['_bwp_hidden_tag_slugs'];
    }
}

/* ── Load the helpers under test ──────────────────────────────────── */

$base = dirname( __DIR__ ) . '/inc/helpers/';

require_once $base . 'literary.php';
require_once $base . 'analytics.php';
require_once $base . 'colophon.php';
require_once $base . 'navigation.php';
