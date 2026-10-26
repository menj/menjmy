<?php
/**
 * Sasterawan — Navigation & Layout Helpers
 *
 * Related works sidebar, previous/next work navigation,
 * table-of-contents entry builder, featured image, and
 * the reading progress bar hook.
 *
 * Depends on: literary.php, actions.php (book_wp_hidden_tag_slugs)
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/* ── Related works ────────────────────────────────────────────────────── */

function book_wp_related_works( $post_id = 0, $max = 4 ) {
    if ( '1' !== get_option( 'book_wp_related_works', '1' ) ) return '';
    if ( ! $post_id ) $post_id = get_the_ID();

    $related      = array();
    $used         = array( $post_id );
    $hidden_slugs = book_wp_hidden_tag_slugs();

    // Fetch buffer: large enough to absorb hidden-post skips.
    // Uses max * 2 so that even if half the candidates are hidden there are
    // still enough to fill all slots without a second query.
    $fetch = max( $max * 2, $max + 4 );

    /**
     * Pre-fetches tags for a batch of posts and returns a lookup closure.
     * One wp_get_object_terms() call covers the entire batch — no N+1 queries.
     *
     * @param  WP_Post[] $posts
     * @return Closure   fn( WP_Post $p ): bool — true if post is hidden
     */
    $make_hidden_check = function( $posts ) use ( $hidden_slugs ) {
        if ( empty( $hidden_slugs ) ) {
            return function() { return false; };
        }
        $ids = wp_list_pluck( $posts, 'ID' );
        $terms = wp_get_object_terms( $ids, 'post_tag', array(
            'fields'                 => 'id=>slug',
            'object__in'             => $ids,
            'orderby'                => 'none',
        ) );
        // Build map: post_id → [ slug, … ]
        $map = array();
        if ( ! is_wp_error( $terms ) ) {
            // wp_get_object_terms with 'id=>slug' returns term slugs keyed by
            // term_id, not by post_id. We need object relationships instead.
            // Re-fetch with 'all' to get object_id.
            $terms = wp_get_object_terms( $ids, 'post_tag', array(
                'fields'     => 'all_with_object_id',
                'object__in' => $ids,
                'orderby'    => 'none',
            ) );
        }
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $t ) {
                $map[ $t->object_id ][] = $t->slug;
            }
        }
        return function( $p ) use ( $map, $hidden_slugs ) {
            if ( empty( $map[ $p->ID ] ) ) return false;
            return ! empty( array_intersect( $map[ $p->ID ], $hidden_slugs ) );
        };
    };

    // ── Pass 1: same source/collection ────────────────────────────────────
    $source = get_post_meta( $post_id, 'book_wp_source', true );
    if ( $source ) {
        $posts     = get_posts( array(
            'numberposts' => $fetch,
            'meta_key'    => 'book_wp_source',
            'meta_value'  => $source,
            'exclude'     => $used,
        ) );
        $is_hidden = $make_hidden_check( $posts );
        $filled    = count( $related );
        foreach ( $posts as $p ) {
            if ( $filled >= $max ) break;
            if ( $is_hidden( $p ) ) continue;
            $related[] = $p;
            $used[]    = $p->ID;
            $filled++;
        }
    }

    // ── Pass 2: same literary form ────────────────────────────────────────
    $filled = count( $related );
    if ( $filled < $max ) {
        $terms = get_the_terms( $post_id, 'literary_form' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $posts = get_posts( array(
                'numberposts' => $fetch,
                'tax_query'   => array( array(
                    'taxonomy' => 'literary_form',
                    'field'    => 'term_id',
                    'terms'    => $terms[0]->term_id,
                ) ),
                'exclude' => $used,
            ) );
            $is_hidden = $make_hidden_check( $posts );
            foreach ( $posts as $p ) {
                if ( $filled >= $max ) break;
                if ( $is_hidden( $p ) ) continue;
                $related[] = $p;
                $used[]    = $p->ID;
                $filled++;
            }
        }
    }

    if ( empty( $related ) ) return '';

    $html = '<aside class="bwp-related"><h3 class="bwp-related__heading">'
        . esc_html( __( 'Related Works', 'book-wp' ) )
        . '</h3><ul class="bwp-related__list">';

    foreach ( $related as $p ) {
        $fs = book_wp_get_form_slug( $p->ID );
        $yr = get_post_meta( $p->ID, 'book_wp_year', true );
        $html .= '<li class="bwp-related__item"><a href="' . esc_url( get_permalink( $p->ID ) ) . '">'
            . '<span class="bwp-related__title">' . esc_html( get_the_title( $p->ID ) ) . '</span>';
        if ( $fs ) $html .= ' <span class="bwp-badge bwp-badge--' . esc_attr( $fs )
            . ' bwp-badge--sm">' . esc_html( book_wp_form_label( $fs ) ) . '</span>';
        if ( $yr ) $html .= ' <span class="bwp-related__year">(' . esc_html( $yr ) . ')</span>';
        $html .= '</a></li>';
    }

    return $html . '</ul></aside>';
}

/* ── Previous / Next work navigation ─────────────────────────────────── */

function book_wp_work_navigation() {
    $hidden_slugs = book_wp_hidden_tag_slugs();

    /**
     * Walk backward or forward through adjacent posts, skipping hidden ones.
     * Uses get_adjacent_post() in a loop rather than get_previous/next_post()
     * once, so hidden posts are transparently stepped over.
     *
     * @param  bool $previous True for previous, false for next.
     * @return WP_Post|null
     */
    $find_adjacent = function( $previous ) use ( $hidden_slugs ) {
        $excluded = array();
        $limit    = 10; // guard against pathological all-hidden archives
        for ( $i = 0; $i < $limit; $i++ ) {
            $post = get_adjacent_post( false, implode( ',', $excluded ), $previous );
            if ( ! $post || is_wp_error( $post ) ) return null;
            if ( empty( $hidden_slugs ) ) return $post;
            $tags = wp_get_post_tags( $post->ID, array( 'fields' => 'slugs' ) );
            if ( empty( array_intersect( $tags, $hidden_slugs ) ) ) return $post;
            // This adjacent post is hidden — exclude it and try the next one.
            $excluded[] = $post->ID;
        }
        return null;
    };

    $prev = $find_adjacent( true );
    $next = $find_adjacent( false );

    if ( ! $prev && ! $next ) return '';

    $html = '<nav class="bwp-work-nav" aria-label="' . esc_attr__( 'Work navigation', 'book-wp' ) . '">';

    if ( $prev ) {
        $html .= '<a href="' . esc_url( get_permalink( $prev->ID ) ) . '"'
            . ' class="bwp-work-nav__link bwp-work-nav__link--prev" rel="prev">'
            . '<span class="bwp-work-nav__arrow" aria-hidden="true">&larr;</span>'
            . '<span class="bwp-work-nav__label">' . esc_html( __( 'Previous Work', 'book-wp' ) ) . '</span>'
            . '<span class="bwp-work-nav__title">' . esc_html( get_the_title( $prev->ID ) ) . '</span>'
            . '</a>';
    }
    if ( $next ) {
        $html .= '<a href="' . esc_url( get_permalink( $next->ID ) ) . '"'
            . ' class="bwp-work-nav__link bwp-work-nav__link--next" rel="next">'
            . '<span class="bwp-work-nav__arrow" aria-hidden="true">&rarr;</span>'
            . '<span class="bwp-work-nav__label">' . esc_html( __( 'Next Work', 'book-wp' ) ) . '</span>'
            . '<span class="bwp-work-nav__title">' . esc_html( get_the_title( $next->ID ) ) . '</span>'
            . '</a>';
    }

    return $html . '</nav>';
}

/* ── Table of contents entry ──────────────────────────────────────────── */

function book_wp_toc_entry( $post, $show_badge = true, $show_thumb = false ) {
    $year    = get_post_meta( $post->ID, 'book_wp_year',        true );
    $excerpt = get_post_meta( $post->ID, 'book_wp_toc_excerpt', true );
    $slug    = book_wp_get_form_slug( $post->ID );

    $has_thumb = $show_thumb && has_post_thumbnail( $post->ID );
    $item_class = 'bwp-toc__item' . ( $has_thumb ? ' bwp-toc__item--has-thumb' : '' );

    $html  = '<li class="' . esc_attr( $item_class ) . '">';
    $html .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="bwp-toc__link">';

    if ( $has_thumb ) {
        $html .= get_the_post_thumbnail( $post->ID, 'book-wp-thumb', array(
            'class'    => 'bwp-toc__thumb',
            'loading'  => 'lazy',
            // decoding="async" offloads image decoding to a background thread,
            // preventing short main-thread stalls as lazy images load during
            // scroll on large TOC pages. (CWV-07)
            'decoding' => 'async',
            'alt'      => esc_attr( get_the_title( $post->ID ) ),
        ) );
        $html .= '<span class="bwp-toc__body">';
    }

    $html .= '<span class="bwp-toc__title">' . esc_html( get_the_title( $post->ID ) ) . '</span>';

    if ( $show_badge && $slug ) {
        $html .= ' <span class="bwp-badge bwp-badge--' . esc_attr( $slug ) . ' bwp-badge--sm">'
            . esc_html( book_wp_form_label( $slug ) ) . '</span>';
    }

    if ( 'category' !== get_option( 'book_wp_toc_grouping', 'none' ) ) {
        $cats = get_the_category( $post->ID );
        if ( $cats && ! is_wp_error( $cats ) && 'uncategorized' !== $cats[0]->slug ) {
            $html .= ' <span class="bwp-toc__cat">' . esc_html( $cats[0]->name ) . '</span>';
        }
    }

    if ( $year ) $html .= ' <span class="bwp-toc__year">' . esc_html( $year ) . '</span>';

    if ( $has_thumb ) {
        $html .= '</span>'; // .bwp-toc__body
    }

    $html .= '</a>';
    if ( $excerpt ) $html .= '<span class="bwp-toc__excerpt">' . esc_html( $excerpt ) . '</span>';

    return $html . '</li>';
}

/* ── Featured image ───────────────────────────────────────────────────── */

function book_wp_featured_image( $post_id = 0 ) {
    if ( '1' !== get_option( 'book_wp_show_featured', '1' ) ) return '';
    if ( ! $post_id ) $post_id = get_the_ID();
    if ( ! has_post_thumbnail( $post_id ) ) return '';
    $caption = get_the_post_thumbnail_caption( $post_id );
    $html = '<figure class="bwp-featured-image">'
        . get_the_post_thumbnail( $post_id, 'book-wp-cover', array(
            'class'         => 'bwp-featured-image__img',
            'loading'       => 'eager',
            'fetchpriority' => 'high',
        ) );
    if ( $caption ) $html .= '<figcaption class="bwp-featured-image__caption">'
        . esc_html( $caption ) . '</figcaption>';
    return $html . '</figure>';
}

/* ── Reading progress bar ─────────────────────────────────────────────── */

add_action( 'wp_body_open', 'book_wp_progress_bar_markup' );

function book_wp_progress_bar_markup() {
    if ( ! is_single() || '1' !== get_option( 'book_wp_progress_bar', '1' ) ) return;
    echo '<div class="bwp-progress" id="bwpProgress" role="progressbar"'
        . ' aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">'
        . '<div class="bwp-progress__bar"></div></div>';
}

/* ── Share row ────────────────────────────────────────────────────────── */

/**
 * Render the share row (icon-only buttons: Copy link, X, Facebook, WhatsApp,
 * Threads, Pinterest, Mastodon, Email).
 * Gated by book_wp_share_row option (default: off).
 * No third-party SDKs or remote scripts are loaded.
 * Labels removed — icon only; each button carries an aria-label for accessibility.
 *
 * @param int $post_id Post ID. Defaults to current post.
 * @return string HTML string or empty string.
 */
/**
 * Build hashtag data for the share row from the post's public tags.
 *
 * Returns an array:
 *   'x'      — comma-separated slugs for X's &hashtags= parameter.
 *              Hyphens are stripped because X treats '-' as a tag terminator.
 *   'inline' — rawurlencoded ' #slug1 #slug2 …' string (leading space included)
 *              for direct concatenation onto an already-encoded $share_text.
 *              Empty string when there are no public tags.
 *   'empty'  — true when the post has no public tags.
 *
 * Rules:
 *   • Tags in book_wp_hidden_tag_slugs() are excluded (internal TOC control tags).
 *   • Capped at 3 tags to stay within X's 280-character budget and keep
 *     share text readable on all platforms.
 *   • Slug is used (not display name) — WordPress ensures slugs are ASCII-safe.
 *
 * @param int $post_id Post ID.
 * @return array{ x: string, inline: string, empty: bool }
 */
function book_wp_share_hashtags( $post_id ) {
    static $cache = array();
    if ( isset( $cache[ $post_id ] ) ) return $cache[ $post_id ];

    $tags = get_the_tags( $post_id );
    if ( ! $tags || is_wp_error( $tags ) ) {
        return $cache[ $post_id ] = array( 'x' => '', 'inline' => '', 'empty' => true );
    }

    $hidden  = book_wp_hidden_tag_slugs();
    $slugs   = array();

    foreach ( $tags as $tag ) {
        if ( in_array( $tag->slug, $hidden, true ) ) continue;
        $slugs[] = $tag->slug;
        if ( count( $slugs ) >= 3 ) break;
    }

    if ( empty( $slugs ) ) {
        return $cache[ $post_id ] = array( 'x' => '', 'inline' => '', 'empty' => true );
    }

    // X: hyphens removed (X treats '-' as a tag terminator in the hashtags param).
    $x_slugs = array_map( function( $s ) { return str_replace( '-', '', $s ); }, $slugs );

    // Inline (Threads, Mastodon): rawurlencode the token string so it can be
    // concatenated directly onto an already-encoded $share_text string.
    $inline_raw = implode( ' ', array_map( function( $s ) { return '#' . $s; }, $slugs ) );

    return $cache[ $post_id ] = array(
        'x'      => implode( ',', $x_slugs ),
        'inline' => rawurlencode( ' ' . $inline_raw ),
        'empty'  => false,
    );
}

function book_wp_share_row( $post_id = 0 ) {
    if ( '1' !== get_option( 'book_wp_share_row', '0' ) ) return '';
    if ( ! $post_id ) $post_id = get_the_ID();

    $permalink  = get_permalink( $post_id );
    $title      = get_the_title( $post_id );
    $url_enc    = rawurlencode( $permalink );
    $title_enc  = rawurlencode( $title );

    $hashtags   = book_wp_share_hashtags( $post_id );

    // share_text: title + URL (+ inline hashtags for platforms that take them in the text param).
    // $hashtags['inline'] is pre-encoded, so concatenate directly onto the encoded base.
    $share_text_base   = $title . ' ' . $permalink;
    $share_text        = rawurlencode( $share_text_base );
    $share_text_tagged = $share_text . $hashtags['inline'];

    $html  = '<div class="bwp-share" aria-label="' . esc_attr__( 'Share this work', 'book-wp' ) . '">';
    $html .= '<span class="bwp-share__label">' . esc_html__( 'Share', 'book-wp' ) . '</span>';

    // ── Copy link ──────────────────────────────────────────────────────────
    $html .= '<button type="button"'
        . ' class="bwp-share__btn bwp-share__copy"'
        . ' data-bwp-copy="' . esc_attr( $permalink ) . '"'
        . ' aria-label="' . esc_attr__( 'Copy link to this work', 'book-wp' ) . '">';
    $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
        . ' stroke-width="2" aria-hidden="true">'
        . '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>'
        . '<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>'
        . '</svg>';
    $html .= '</button>';

    // ── X (Twitter) ────────────────────────────────────────────────────────
    $x_url = 'https://x.com/intent/tweet?url=' . $url_enc . '&text=' . $title_enc;
    if ( ! $hashtags['empty'] ) $x_url .= '&hashtags=' . $hashtags['x'];
    $html .= '<a href="' . esc_url( $x_url ) . '"'
        . ' class="bwp-share__btn"'
        . ' target="_blank" rel="noopener noreferrer"'
        . ' aria-label="' . esc_attr__( 'Share on X (Twitter)', 'book-wp' ) . '">';
    $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">'
        . '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744'
        . 'l7.737-8.835L1.254 2.25H8.08l4.265 5.638L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'
        . '</svg>';
    $html .= '</a>';

    // ── Facebook ───────────────────────────────────────────────────────────
    $html .= '<a href="' . esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . $url_enc ) . '"'
        . ' class="bwp-share__btn"'
        . ' target="_blank" rel="noopener noreferrer"'
        . ' aria-label="' . esc_attr__( 'Share on Facebook', 'book-wp' ) . '">';
    $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">'
        . '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125'
        . ' 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235'
        . ' 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385'
        . 'C19.612 23.027 24 18.062 24 12.073z"/>'
        . '</svg>';
    $html .= '</a>';

    // ── WhatsApp ───────────────────────────────────────────────────────────
    $html .= '<a href="' . esc_url( 'https://wa.me/?text=' . $share_text ) . '"'
        . ' class="bwp-share__btn"'
        . ' target="_blank" rel="noopener noreferrer"'
        . ' aria-label="' . esc_attr__( 'Share via WhatsApp', 'book-wp' ) . '">';
    $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">'
        . '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15'
        . '-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475'
        . '-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52'
        . '.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207'
        . '-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372'
        . '-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2'
        . ' 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719'
        . ' 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004'
        . 'a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0'
        . ' 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825'
        . ' 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815'
        . ' 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24'
        . 'l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893'
        . 'a11.821 11.821 0 00-3.48-8.413z"/>'
        . '</svg>';
    $html .= '</a>';

    // ── Threads ────────────────────────────────────────────────────────────
    $html .= '<a href="' . esc_url( 'https://www.threads.net/intent/post?text=' . $share_text_tagged ) . '"'
        . ' class="bwp-share__btn"'
        . ' target="_blank" rel="noopener noreferrer"'
        . ' aria-label="' . esc_attr__( 'Share on Threads', 'book-wp' ) . '">';
    $html .= '<svg width="16" height="16" viewBox="0 0 800 800" fill="currentColor" aria-hidden="true"><g transform="translate(0,800) scale(0.1,-0.1)"><path d="M3810 7254 c-548 -49 -943 -160 -1310 -367 -320 -181 -601 -438 -812 -742 -406 -585 -609 -1348 -610 -2290 0 -497 56 -934 172 -1339 66 -229 123 -377 224 -581 366 -738 965 -1208 1781 -1394 422 -96 1001 -109 1450 -31 548 94 974 310 1351 685 192 191 287 319 393 532 131 261 191 518 191 813 0 685 -353 1240 -987 1550 l-112 55 -6 55 c-24 238 -49 366 -105 533 -181 539 -572 855 -1150 926 -108 14 -332 14 -438 1 -333 -42 -623 -182 -851 -411 -71 -71 -171 -195 -171 -212 0 -7 468 -327 479 -327 5 0 17 12 26 27 55 83 173 183 282 237 156 78 270 100 497 93 199 -5 282 -23 415 -88 66 -33 103 -60 166 -123 89 -88 138 -166 184 -293 28 -77 67 -231 60 -237 -2 -2 -44 2 -93 10 -255 38 -680 42 -916 8 -609 -86 -1016 -396 -1148 -874 -23 -86 -26 -112 -26 -270 1 -157 3 -185 27 -275 83 -310 288 -551 602 -708 445 -222 1073 -190 1482 75 333 217 553 595 639 1098 9 52 17 97 20 99 7 8 169 -117 232 -179 153 -153 242 -319 287 -540 26 -123 24 -345 -3 -470 -63 -291 -209 -532 -452 -746 -392 -345 -848 -495 -1510 -498 -675 -2 -1207 171 -1610 526 -219 193 -408 467 -535 773 -210 505 -302 1188 -250 1860 40 531 141 937 322 1300 110 219 207 358 367 523 320 329 733 522 1281 598 181 25 638 26 810 1 321 -47 536 -108 775 -223 519 -250 868 -671 1080 -1303 31 -91 54 -146 62 -145 11 1 475 124 531 140 24 7 22 18 -24 162 -157 484 -374 854 -688 1175 -522 533 -1211 801 -2091 812 -135 1 -265 1 -290 -1z m790 -3495 c110 -10 338 -43 345 -51 5 -4 -21 -203 -36 -278 -40 -207 -132 -409 -240 -524 -102 -109 -206 -169 -369 -213 -96 -26 -407 -26 -505 1 -141 37 -252 95 -332 174 -176 171 -182 457 -15 643 119 133 337 223 602 249 117 11 423 11 550 -1z"/></g></svg>';
    $html .= '</a>';

    // ── Pinterest ──────────────────────────────────────────────────────────
    $html .= '<a href="' . esc_url( 'https://pinterest.com/pin/create/button/?url=' . $url_enc . '&description=' . $title_enc ) . '"'
        . ' class="bwp-share__btn"'
        . ' target="_blank" rel="noopener noreferrer"'
        . ' aria-label="' . esc_attr__( 'Share on Pinterest', 'book-wp' ) . '">';
    $html .= '<svg width="16" height="16" viewBox="0 0 54 55" fill="currentColor" aria-hidden="true"><g transform="translate(0,55) scale(0.1,-0.1)"><path d="M183 559 c-186 -102 -170 -489 20 -489 2 0 7 15 10 33 3 17 8 49 11 71 4 21 10 46 15 55 5 9 7 40 6 69 -2 58 16 91 49 86 43 -6 44 -65 2 -140 -5 -9 38 -40 52 -39 26 2 72 39 72 58 0 9 4 18 8 21 9 6 -1 107 -13 126 -29 47 -149 41 -195 -9 -27 -30 -33 -95 -12 -129 15 -22 4 -52 -19 -52 -66 0 -54 214 13 236 10 3 18 9 18 14 0 17 132 36 155 22 6 -4 15 -7 22 -8 22 -4 73 -45 73 -60 0 -8 5 -14 11 -14 5 0 8 -4 4 -9 -3 -5 -1 -12 4 -15 12 -8 11 -98 -2 -113 -5 -7 -6 -13 -3 -13 14 0 -29 -61 -53 -77 -36 -23 -97 -28 -125 -10 -15 11 -23 12 -24 4 -2 -29 -14 -72 -27 -98 -19 -35 -14 -38 70 -36 361 10 359 546 -2 553 -65 2 -77 -2 -140 -37z"/></g></svg>';
    $html .= '</a>';

    // ── Mastodon ───────────────────────────────────────────────────────────
    // Uses toot.kytta.dev — a lightweight universal Mastodon share redirector
    // that prompts the user for their instance. No API key required.
    $html .= '<a href="' . esc_url( 'https://toot.kytta.dev/?text=' . $share_text_tagged ) . '"'
        . ' class="bwp-share__btn"'
        . ' target="_blank" rel="noopener noreferrer"'
        . ' aria-label="' . esc_attr__( 'Share on Mastodon', 'book-wp' ) . '">';
    $html .= '<svg width="16" height="16" viewBox="0 0 11000 12000" fill="currentColor" aria-hidden="true"><path d="M5500 11994 c-41 -2 -183 -8 -315 -14 -847 -38 -1662 -146 -2269 -301 -200 -51 -506 -152 -648 -212 -534 -231 -1074 -810 -1381 -1483 -225 -492 -345 -1018 -378 -1659 -14 -268 -6 -2270 10 -2650 37 -853 97 -1462 197 -2010 304 -1663 973 -2686 2076 -3174 238 -105 494 -183 908 -275 1155 -256 2263 -284 3262 -81 399 81 830 216 1060 331 l67 34 -5 97 c-3 54 -14 272 -23 485 -10 213 -20 390 -22 393 -3 2 -102 -20 -221 -50 -588 -149 -1257 -228 -1773 -211 -823 28 -1236 96 -1582 261 -192 91 -322 186 -451 330 -188 208 -310 502 -356 855 -22 167 -32 370 -18 370 6 0 80 -16 164 -36 557 -130 1172 -219 1828 -265 250 -17 985 -23 1265 -10 609 28 1398 114 1800 196 452 92 921 267 1320 493 124 70 380 242 485 327 363 290 621 622 724 930 78 234 177 999 226 1745 45 692 63 1620 40 1985 -42 636 -168 1157 -400 1642 -172 360 -374 646 -650 923 -246 246 -471 408 -713 510 -610 258 -1586 436 -2772 505 -249 15 -1261 28 -1455 19z m-880 -2048 c311 -62 578 -191 795 -386 162 -145 222 -228 430 -583 82 -141 153 -257 156 -257 4 0 73 114 155 253 184 316 225 375 343 499 227 239 516 396 861 468 147 31 429 39 593 16 401 -56 749 -226 1017 -495 277 -279 428 -590 500 -1032 69 -426 80 -1079 54 -3226 l-6 -493 -609 0 -608 0 -4 1613 c-3 1604 -3 1612 -24 1705 -12 51 -29 116 -39 145 -73 226 -236 420 -427 511 -118 57 -185 71 -332 70 -113 -1 -141 -4 -210 -27 -262 -87 -461 -296 -565 -592 -85 -242 -90 -322 -90 -1357 l0 -868 -610 0 -610 0 0 913 c0 965 -3 1021 -48 1197 -85 325 -281 576 -531 681 -284 119 -599 54 -821 -170 -150 -152 -236 -337 -269 -584 -8 -53 -11 -596 -11 -1658 l0 -1579 -613 0 -614 0 -6 803 c-14 1643 -8 2291 23 2637 52 565 178 910 454 1232 256 299 645 505 1081 573 123 19 468 14 585 -9z"/></svg>';
    $html .= '</a>';

    // ── Email ──────────────────────────────────────────────────────────────
    $html .= '<a href="' . esc_url( 'mailto:?subject=' . $title_enc . '&body=' . $url_enc ) . '"'
        . ' class="bwp-share__btn"'
        . ' aria-label="' . esc_attr__( 'Share via Email', 'book-wp' ) . '">';
    $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
        . ' stroke-width="2" aria-hidden="true">'
        . '<rect x="2" y="4" width="20" height="16" rx="2"/>'
        . '<polyline points="2,4 12,13 22,4"/>'
        . '</svg>';
    $html .= '</a>';

    $html .= '</div>';
    return $html;
}
