<?php defined( 'ABSPATH' ) || exit; ?>
<?php get_header(); ?>

<main id="bwp-main" class="bwp-main">

    <?php $anthology = get_option( 'book_wp_anthology_name', '' ); ?>
    <div class="bwp-book-title">
        <?php if ( ! empty( $anthology ) ) : ?>
            <h1 class="bwp-book-title__name"><?php echo esc_html( $anthology ); ?></h1>
        <?php endif; ?>
        <div class="bwp-book-title__desc"><?php bloginfo( 'description' ); ?></div>
    </div>

    <?php
    $front_blurb = get_option( 'book_wp_front_blurb', '' );
    if ( ! empty( $front_blurb ) ) :
    ?>
        <div class="bwp-front-blurb">
            <?php echo wp_kses_post( nl2br( $front_blurb ) ); ?>
        </div>
    <?php endif; ?>

    <h3 class="bwp-toc-header"><?php echo esc_html( __( 'Table of Contents', 'book-wp' ) ); ?></h3>

    <?php
    $grouping    = get_option( 'book_wp_toc_grouping', 'none' );
    $show_badges = ( '1' === get_option( 'book_wp_show_badges', '1' ) );
    // TOC query — cached via transient to avoid a full table scan on every load.
    // Invalidated automatically on any post save/publish/trash (see actions.php).
    $all_posts = get_transient( 'book_wp_toc_posts' );
    if ( false === $all_posts ) {
        $all_posts = get_posts( array(
            'numberposts' => -1,
            'orderby'     => 'date',
            'order'       => 'ASC',
            'post_status' => 'publish',
        ) );
        set_transient( 'book_wp_toc_posts', $all_posts, HOUR_IN_SECONDS * 12 );
    }

    // Filter out posts carrying any hidden-trigger tag before any grouping runs.
    // Uses one batched wp_get_object_terms() call for the entire post set,
    // eliminating the N+1 per-post wp_get_post_tags() that previously ran
    // inside array_filter(). Follows the same pattern as navigation.php.
    $hidden_slugs = book_wp_hidden_tag_slugs();
    if ( ! empty( $hidden_slugs ) && ! empty( $all_posts ) ) {
        $all_ids = wp_list_pluck( $all_posts, 'ID' );
        $terms   = wp_get_object_terms( $all_ids, 'post_tag', array(
            'fields'     => 'all_with_object_id',
            'object__in' => $all_ids,
            'orderby'    => 'none',
        ) );
        // Build map: post_id => [ slug, ... ]
        $hidden_tag_map = array();
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $t ) {
                $hidden_tag_map[ $t->object_id ][] = $t->slug;
            }
        }
        $all_posts = array_values( array_filter( $all_posts, function( $p ) use ( $hidden_slugs, $hidden_tag_map ) {
            if ( empty( $hidden_tag_map[ $p->ID ] ) ) return true;
            return empty( array_intersect( $hidden_tag_map[ $p->ID ], $hidden_slugs ) );
        } ) );
    }

    /* ── Group by Literary Form ──────────────────────────────── */

    if ( 'form' === $grouping ) :
        $grouped   = array();
        $ungrouped = array();

        foreach ( $all_posts as $p ) {
            $terms = get_the_terms( $p->ID, 'literary_form' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                $slug = $terms[0]->slug;
                if ( ! isset( $grouped[ $slug ] ) ) {
                    $grouped[ $slug ] = array(
                        'label' => book_wp_form_label( $slug ),
                        'posts' => array(),
                    );
                }
                $grouped[ $slug ]['posts'][] = $p;
            } else {
                $ungrouped[] = $p;
            }
        }

        foreach ( $grouped as $slug => $group ) :
    ?>
            <div class="bwp-toc-group">
                <h4 class="bwp-toc-group__heading">
                    <span class="bwp-toc-group__label"><?php echo esc_html( $group['label'] ); ?></span>
                    <span class="bwp-toc-group__count"><?php echo (int) count( $group['posts'] ); ?></span>
                </h4>
                <ul class="bwp-toc">
                    <?php foreach ( $group['posts'] as $p ) : ?>
                        <?php echo book_wp_toc_entry( $p, false ); // phpcs:ignore ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <?php if ( ! empty( $ungrouped ) ) : ?>
            <div class="bwp-toc-group">
                <h4 class="bwp-toc-group__heading">
                    <span class="bwp-toc-group__label"><?php echo esc_html( __( 'All Works', 'book-wp' ) ); ?></span>
                    <span class="bwp-toc-group__count"><?php echo (int) count( $ungrouped ); ?></span>
                </h4>
                <ul class="bwp-toc">
                    <?php foreach ( $ungrouped as $p ) : ?>
                        <?php echo book_wp_toc_entry( $p, false ); // phpcs:ignore ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    <?php

    /* ── Group by Category ───────────────────────────────────── */

    elseif ( 'category' === $grouping ) :
        $categories = get_categories( array( 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC' ) );
        $categorised_ids = array();

        foreach ( $categories as $cat ) :
            // Skip the default "Uncategorized" — handled separately below.
            if ( 'uncategorized' === $cat->slug ) continue;

            $cat_posts = array();
            foreach ( $all_posts as $p ) {
                if ( in_category( $cat->term_id, $p ) ) {
                    $cat_posts[]       = $p;
                    $categorised_ids[] = $p->ID;
                }
            }
            if ( empty( $cat_posts ) ) continue;
    ?>
            <div class="bwp-toc-group">
                <h4 class="bwp-toc-group__heading">
                    <span class="bwp-toc-group__label"><?php echo esc_html( $cat->name ); ?></span>
                    <span class="bwp-toc-group__count"><?php echo (int) count( $cat_posts ); ?></span>
                </h4>
                <?php if ( ! empty( $cat->description ) ) : ?>
                    <p class="bwp-toc-group__desc"><?php echo esc_html( $cat->description ); ?></p>
                <?php endif; ?>
                <ul class="bwp-toc">
                    <?php foreach ( $cat_posts as $p ) : ?>
                        <?php echo book_wp_toc_entry( $p, $show_badges ); // phpcs:ignore ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <?php
        // Works that only belong to the default "Uncategorized" category.
        $uncategorised = array();
        foreach ( $all_posts as $p ) {
            if ( ! in_array( $p->ID, $categorised_ids, true ) ) {
                $uncategorised[] = $p;
            }
        }
        if ( ! empty( $uncategorised ) ) :
        ?>
            <div class="bwp-toc-group">
                <h4 class="bwp-toc-group__heading">
                    <span class="bwp-toc-group__label"><?php echo esc_html( __( 'Uncategorised', 'book-wp' ) ); ?></span>
                    <span class="bwp-toc-group__count"><?php echo (int) count( $uncategorised ); ?></span>
                </h4>
                <ul class="bwp-toc">
                    <?php foreach ( $uncategorised as $p ) : ?>
                        <?php echo book_wp_toc_entry( $p, $show_badges ); // phpcs:ignore ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    <?php

    /* ── Group by Tag ────────────────────────────────────────── */

    elseif ( 'tag' === $grouping ) :

        // Build a map: tag_slug → [ label, posts[] ]
        // A post appears under every thematic tag it carries (no deduplication).
        $tag_groups = array();
        $untagged   = array();

        // One batched wp_get_object_terms() call for tag-grouping display.
        // This is separate from the hidden-tag filter batch because it runs
        // regardless of whether hidden_slugs is set, and needs full term objects.
        $all_ids_for_grouping = wp_list_pluck( $all_posts, 'ID' );
        $grouping_raw = wp_get_object_terms( $all_ids_for_grouping, 'post_tag', array(
            'fields'     => 'all_with_object_id',
            'object__in' => $all_ids_for_grouping,
            'orderby'    => 'none',
        ) );
        // Build map: post_id => [ WP_Term, ... ]
        $grouping_terms_map = array();
        if ( ! is_wp_error( $grouping_raw ) ) {
            foreach ( $grouping_raw as $t ) {
                $grouping_terms_map[ $t->object_id ][] = $t;
            }
        }

        foreach ( $all_posts as $p ) {
            $post_tags = isset( $grouping_terms_map[ $p->ID ] ) ? $grouping_terms_map[ $p->ID ] : array();
            // Strip hidden/trigger tags from display grouping.
            $thematic = array_filter( $post_tags, function( $t ) use ( $hidden_slugs ) {
                return ! in_array( $t->slug, $hidden_slugs, true );
            } );

            if ( empty( $thematic ) ) {
                $untagged[] = $p;
            } else {
                foreach ( $thematic as $tag ) {
                    if ( ! isset( $tag_groups[ $tag->slug ] ) ) {
                        $tag_groups[ $tag->slug ] = array(
                            'label' => $tag->name,
                            'posts' => array(),
                        );
                    }
                    $tag_groups[ $tag->slug ]['posts'][] = $p;
                }
            }
        }
        // Sort groups alphabetically by tag name.
        uasort( $tag_groups, function( $a, $b ) {
            return strcmp( $a['label'], $b['label'] );
        } );

        foreach ( $tag_groups as $slug => $group ) :
    ?>
            <div class="bwp-toc-group">
                <h4 class="bwp-toc-group__heading">
                    <span class="bwp-toc-group__label"><?php echo esc_html( $group['label'] ); ?></span>
                    <span class="bwp-toc-group__count"><?php echo (int) count( $group['posts'] ); ?></span>
                </h4>
                <ul class="bwp-toc">
                    <?php foreach ( $group['posts'] as $p ) : ?>
                        <?php echo book_wp_toc_entry( $p, $show_badges ); // phpcs:ignore ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <?php if ( ! empty( $untagged ) ) : ?>
            <div class="bwp-toc-group">
                <h4 class="bwp-toc-group__heading">
                    <span class="bwp-toc-group__label"><?php echo esc_html( __( 'Untagged', 'book-wp' ) ); ?></span>
                    <span class="bwp-toc-group__count"><?php echo (int) count( $untagged ); ?></span>
                </h4>
                <ul class="bwp-toc">
                    <?php foreach ( $untagged as $p ) : ?>
                        <?php echo book_wp_toc_entry( $p, false ); // phpcs:ignore ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    <?php

    /* ── No grouping ─────────────────────────────────────────── */

    else :
        $toc_layout = get_option( 'book_wp_toc_layout',     'list' );
        $toc_paging = get_option( 'book_wp_toc_pagination', 'paged' );

        if ( 'infinite' === $toc_paging ) :
            // Paginated query — Infinite Scroll intercepts the next-page link,
            // so posts must be delivered in pages rather than all at once.
            // Hidden-tag exclusion moved to post__not_in so it works at DB level.
            $exclude_ids = array();
            if ( ! empty( $hidden_slugs ) ) {
                $excl = new WP_Query( array(
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'post_status'    => 'publish',
                    'tax_query'      => array( array(
                        'taxonomy' => 'post_tag',
                        'field'    => 'slug',
                        'terms'    => $hidden_slugs,
                    ) ),
                ) );
                $exclude_ids = $excl->posts;
                wp_reset_postdata();
            }

            $paged     = max( 1, (int) get_query_var( 'paged' ) );
            $toc_query = new WP_Query( array(
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'ASC',
                'paged'          => $paged,
                'posts_per_page' => (int) get_option( 'posts_per_page', 10 ),
                'post__not_in'   => $exclude_ids,
            ) );
            ?>
            <ul class="bwp-toc" id="bwp-toc-flat">
                <?php while ( $toc_query->have_posts() ) : $toc_query->the_post(); ?>
                    <?php echo book_wp_toc_entry( get_post(), $show_badges, true ); // phpcs:ignore ?>
                <?php endwhile; wp_reset_postdata(); ?>
            </ul>
            <?php
            // Pagination nav — IS intercepts .page-numbers.next from this output.
            $max = $toc_query->max_num_pages;
            if ( $max > 1 ) :
                echo paginate_links( array( // phpcs:ignore
                    'base'      => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
                    'format'    => '?paged=%#%',
                    'current'   => $paged,
                    'total'     => $max,
                    'prev_text' => '<span aria-hidden="true">&larr;</span>'
                    . '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'book-wp' ) . '</span>',
                    // W-08 / SC 2.4.4 — bare arrow entity has no accessible name
                    'next_text' => '<span aria-hidden="true">&rarr;</span>'
                    . '<span class="screen-reader-text">' . esc_html__( 'Next page', 'book-wp' ) . '</span>',
                    // W-08 / SC 2.4.4
                    'type'      => 'plain',
                ) );
            endif;

        else :
            // Non-paginated path: all posts already in $all_posts (transient-cached).
            // Grid / Masonry layout is handled entirely by CSS body classes.
            ?>
            <ul class="bwp-toc" id="bwp-toc-flat">
                <?php foreach ( $all_posts as $p ) : ?>
                    <?php echo book_wp_toc_entry( $p, $show_badges, true ); // phpcs:ignore ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

    <?php echo book_wp_ornament( 'fleuron' ); // phpcs:ignore ?>

</main><!-- #bwp-main -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
