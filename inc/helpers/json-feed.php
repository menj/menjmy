<?php
/**
 * JSON Feed v1.1
 *
 * Registers a feed at /feed/json (rewrite: ?feed=json).
 * Spec: https://www.jsonfeed.org/version/1.1/
 *
 * The feed is gated by the book_wp_json_feed option (default: 1).
 * A <link> autodiscovery tag is added to <head> on the front page only when
 * the feed is enabled.
 *
 * Output fields per item:
 *   id, url, title, content_html, summary, date_published, date_modified,
 *   authors, tags (thematic only — hidden tags excluded),
 *   _book_wp (extension object: literary_form, word_count, reading_time_min)
 *
 * No external dependencies. Hooked after WP core init.
 *
 * @package Book_WP
 * @since   3.4.59
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Registration ─────────────────────────────────────────────────────── */

add_action( 'init', 'book_wp_register_json_feed' );

function book_wp_register_json_feed() {
    if ( '1' !== get_option( 'book_wp_json_feed', '1' ) ) return;
    add_feed( 'json', 'book_wp_json_feed_render' );
}

/* ── Autodiscovery <link> in <head> ───────────────────────────────────── */

add_action( 'wp_head', 'book_wp_json_feed_link_tag', 2 );

function book_wp_json_feed_link_tag() {
    if ( '1' !== get_option( 'book_wp_json_feed', '1' ) ) return;
    if ( ! is_front_page() && ! is_home() ) return;
    $site_name = esc_attr( get_bloginfo( 'name' ) );
    $feed_url  = esc_url( get_feed_link( 'json' ) );
    echo '<link rel="alternate" type="application/feed+json" title="' . $site_name . '" href="' . $feed_url . '" />' . "\n";
}

/* ── Feed renderer ────────────────────────────────────────────────────── */

/**
 * Render the JSON Feed response.
 * Sets Content-Type header, builds the feed array, encodes and outputs it.
 */
function book_wp_json_feed_render() {
    // Headers — must be sent before any output.
    // WordPress may have already started output buffering; status() handles that.
    if ( ! headers_sent() ) {
        header( 'Content-Type: application/feed+json; charset=' . get_bloginfo( 'charset' ) );
    }

    $posts_per_feed = (int) get_option( 'posts_per_rss', 10 );
    $author_name    = get_option( 'book_wp_author_name', '' );
    $anthology_name = get_option( 'book_wp_anthology_name', get_bloginfo( 'name' ) );
    $hidden_ids     = book_wp_hidden_tag_ids();   // from inc/hooks/actions.php

    /* ── Feed-level metadata ─────────────────────────────────────────── */

    $feed = array(
        'version'       => 'https://jsonfeed.org/version/1.1',
        'title'         => $anthology_name ?: get_bloginfo( 'name' ),
        'home_page_url' => home_url( '/' ),
        'feed_url'      => get_feed_link( 'json' ),
        'description'   => get_bloginfo( 'description' ),
        'language'      => get_bloginfo( 'language' ), // e.g. "ms-MY"
        'items'         => array(),
    );

    if ( $author_name ) {
        $feed['authors'] = array(
            array( 'name' => $author_name ),
        );
    }

    /* ── Query ───────────────────────────────────────────────────────── */

    $query = new WP_Query( array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => $posts_per_feed,
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,   // skip SQL_CALC_FOUND_ROWS — feeds never paginate
    ) );

    /* ── Items ───────────────────────────────────────────────────────── */

    while ( $query->have_posts() ) {
        $query->the_post();
        $post_id = get_the_ID();

        // --- description / summary ------------------------------------------
        $summary = trim( (string) get_post_meta( $post_id, 'book_wp_toc_excerpt', true ) );
        if ( ! $summary ) {
            $summary = wp_trim_words(
                wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ),
                30,
                "\xe2\x80\xa6"
            );
        }

        // --- content_html ---------------------------------------------------
        // apply_filters('the_content') ensures shortcodes and blocks expand.
        $content = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );

        // --- tags (thematic only) -------------------------------------------
        $all_tags = get_the_tags( $post_id );
        $tag_names = array();
        if ( $all_tags && ! is_wp_error( $all_tags ) ) {
            foreach ( $all_tags as $tag ) {
                if ( ! in_array( $tag->term_id, $hidden_ids, true ) ) {
                    $tag_names[] = $tag->name;
                }
            }
        }

        // --- authors --------------------------------------------------------
        $item_authors = array();
        $display_name = get_the_author();
        if ( $display_name ) {
            $item_authors[] = array( 'name' => $display_name );
        }

        // --- image ----------------------------------------------------------
        $image_url = '';
        if ( has_post_thumbnail( $post_id ) ) {
            $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'book-wp-cover' );
            if ( $img ) $image_url = $img[0];
        }

        // --- literary extension object --------------------------------------
        $form_label   = book_wp_get_form_label( $post_id );
        $word_count   = (int) book_wp_word_count( $post_id );
        $reading_time = (int) book_wp_reading_time_minutes( $post_id );

        // --- assemble item --------------------------------------------------
        $item = array(
            'id'               => get_permalink( $post_id ),
            'url'              => get_permalink( $post_id ),
            'title'            => get_the_title( $post_id ),
            'content_html'     => $content,
            'summary'          => $summary,
            'date_published'   => get_post_time( 'c', true, $post_id ),  // ISO 8601 UTC
            'date_modified'    => get_post_modified_time( 'c', true, $post_id ),
            'authors'          => $item_authors,
        );

        if ( $image_url )    $item['image']   = $image_url;
        if ( $tag_names )    $item['tags']    = $tag_names;
        if ( $form_label || $word_count ) {
            $ext = array();
            if ( $form_label )   $ext['literary_form']    = $form_label;
            if ( $word_count )   $ext['word_count']        = $word_count;
            if ( $reading_time ) $ext['reading_time_min']  = $reading_time;
            $item['_book_wp'] = $ext;
        }

        $feed['items'][] = $item;
    }

    wp_reset_postdata();

    // JSON_UNESCAPED_UNICODE: readable Malay/Arabic text (no \uXXXX escapes).
    // JSON_UNESCAPED_SLASHES: cleaner URLs.
    // JSON_PRETTY_PRINT: human-readable; 1–2 KB overhead at most.
    echo wp_json_encode( $feed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
}
