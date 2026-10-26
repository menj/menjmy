<?php defined( 'ABSPATH' ) || exit; ?>
<?php get_header(); ?>

<main id="bwp-main" class="bwp-main">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'bwp-single' ); ?> id="post-<?php the_ID(); ?>">

        <?php
        // "PETIKAN DARIPADA" — shown above the title only when the post is
        // in the hikayat form AND the citation toggle is checked in the meta box.
        $form_slug_single = book_wp_get_form_slug();
        if ( 'hikayat' === $form_slug_single
            && '1' === get_post_meta( get_the_ID(), 'book_wp_hikayat_is_citation', true ) ) :
        ?>
            <p class="bwp-citation-label"><?php esc_html_e( 'Cited from', 'book-wp' ); ?></p>
        <?php endif; ?>
        <h1 class="bwp-work__title"><?php the_title(); ?></h1>

        <?php if ( '1' === get_option( 'book_wp_show_analytics', '1' ) ) : ?>
            <?php echo book_wp_analytics_bar(); // phpcs:ignore ?>
        <?php endif; ?>

        <?php
        // Nota Penulis — shown between analytics bar and body content,
        // full width across the page wrap. All forms.
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

        <?php echo book_wp_colophon(); // phpcs:ignore ?>

        <?php echo book_wp_related_works(); // phpcs:ignore ?>

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
