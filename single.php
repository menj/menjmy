<?php defined( 'ABSPATH' ) || exit; ?>
<?php get_header(); ?>

<main id="bwp-main" class="bwp-main">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'bwp-single' ); ?> id="post-<?php the_ID(); ?>">

        <?php
        $form_slug_single = book_wp_get_form_slug();
        $jawi_form        = book_wp_form_jawi( $form_slug_single );
        ?>
        <header class="bwp-work__opening bwp-opening">
            <div class="bwp-opening__top">
                <?php $bwp_kicker = book_wp_header_section_label(); if ( $bwp_kicker ) : ?>
                    <p class="bwp-opening__kicker"><?php echo esc_html( $bwp_kicker ); ?></p>
                <?php endif; ?>
                <?php if ( $jawi_form ) : ?>
                    <span class="bwp-opening__seal" aria-hidden="true" dir="rtl" lang="ms-Arab"><?php echo esc_html( $jawi_form ); ?></span>
                <?php endif; ?>
            </div>
            <h1 class="bwp-work__title"><?php the_title(); ?></h1>
            <div class="bwp-work__meta">
                <?php
                // "PETIKAN DARIPADA" — shown only when the post is a hikayat
                // AND the citation toggle is checked in the meta box.
                if ( 'hikayat' === $form_slug_single
                    && '1' === get_post_meta( get_the_ID(), 'book_wp_hikayat_is_citation', true ) ) :
                ?>
                    <p class="bwp-citation-label"><?php esc_html_e( 'Cited from', 'book-wp' ); ?></p>
                <?php endif; ?>
                <?php if ( '1' === get_option( 'book_wp_show_analytics', '1' ) ) : ?>
                    <?php echo book_wp_analytics_bar(); // phpcs:ignore ?>
                <?php endif; ?>
            </div>
        </header>

        <div class="bwp-work__layout">
        <div class="bwp-work__main">
        <?php
        // Nota Penulis — shown between analytics bar and body content. All forms.
        $bwp_notes = get_post_meta( get_the_ID(), 'book_wp_notes', true );
        if ( $bwp_notes ) :
        ?>
        <div class="bwp-author-note">
            <p class="bwp-author-note__heading"><?php esc_html_e( "Author's Note", 'book-wp' ); ?></p>
            <div class="bwp-author-note__text"><?php echo wp_kses_post( nl2br( $bwp_notes ) ); ?></div>
        </div>
        <?php endif; ?>

        <?php echo book_wp_dedication(); // phpcs:ignore ?>

        <?php echo book_wp_epigraph(); // phpcs:ignore ?>

        <?php echo book_wp_terjemahan_block(); // phpcs:ignore ?>

        <?php echo book_wp_featured_image(); // phpcs:ignore ?>

        <div class="bwp-work__content">
            <?php the_content(); ?>
            <?php wp_link_pages( array(
                'before'      => '<nav class="bwp-page-links" aria-label="' . esc_attr__( 'Page navigation', 'book-wp' ) . '">',
                'after'       => '</nav>',
                'link_before' => '<span>',
                'link_after'  => '</span>',
            ) ); ?>
        </div>

        <?php
        // Tags — displayed between content and colophon, trigger tags suppressed.
        if ( '1' === get_option( 'book_wp_show_tags', '1' ) ) :
            $hidden_slugs = book_wp_hidden_tag_slugs();
            $post_tags    = get_the_tags();
            if ( $post_tags && ! is_wp_error( $post_tags ) ) :
                $thematic = array_filter( $post_tags, function( $t ) use ( $hidden_slugs ) {
                    return ! in_array( $t->slug, $hidden_slugs, true );
                } );
                if ( ! empty( $thematic ) ) :
        ?>
        <div class="bwp-tags" aria-label="<?php esc_attr_e( 'Tags', 'book-wp' ); ?>">
            <?php foreach ( $thematic as $tag ) : ?>
                <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"
                   class="bwp-tag"
                   rel="tag"><?php echo esc_html( $tag->name ); ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; endif; endif; ?>

        <?php echo book_wp_share_row(); // phpcs:ignore ?>
        </div><!-- .bwp-work__main -->

        <?php
        // Apparatus — colophon and related works. Beside the text on wide
        // screens, below it on tablet and phone.
        $bwp_aside = book_wp_colophon() . book_wp_related_works();
        if ( trim( $bwp_aside ) ) :
        ?>
        <aside class="bwp-work__aside" aria-label="<?php esc_attr_e( 'About this work', 'book-wp' ); ?>">
            <?php echo $bwp_aside; // phpcs:ignore ?>
        </aside>
        <?php endif; ?>
        </div><!-- .bwp-work__layout -->

        <?php
        $bwp_nav_output = '';
        if ( '1' === get_option( 'book_wp_show_nav', '1' ) ) {
            $bwp_nav_output = book_wp_work_navigation();
            echo $bwp_nav_output; // phpcs:ignore
        }
        // When no nav renders (disabled or no adjacent posts), suppress the
        // large top margin on the comments section via a body class hook.
        if ( ! $bwp_nav_output ) {
            add_filter( 'body_class', function( $classes ) {
                $classes[] = 'bwp-no-work-nav';
                return $classes;
            } );
        }
        ?>

    </article>

    <?php if ( 'show' === get_option( 'book_wp_comments', 'show' ) && ( comments_open() || get_comments_number() ) ) : ?>
        <div class="bwp-comments">
            <?php comments_template(); ?>
        </div>
    <?php endif; ?>

<?php endwhile; endif; ?>

</main><!-- #bwp-main -->

<?php get_footer(); ?>
