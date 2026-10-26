<?php defined( 'ABSPATH' ) || exit; ?>
<?php get_header(); ?>

<main id="bwp-main" class="bwp-main">

<div class="bwp-archive-header bwp-opening bwp-archive-header--tag">
    <div class="bwp-opening__top">
        <p class="bwp-archive-header__taxlabel bwp-opening__kicker"><?php esc_html_e( 'Tagged with', 'book-wp' ); ?></p>
        <?php $bwp_seal = get_option( 'book_wp_jawi_name', '' ); if ( $bwp_seal ) : ?><span class="bwp-opening__seal" aria-hidden="true" dir="rtl" lang="ms-Arab"><?php echo esc_html( $bwp_seal ); ?></span><?php endif; ?>
    </div>

    <h1 class="bwp-archive-header__title">
        <span class="bwp-tag bwp-tag--lg"><?php single_tag_title(); ?></span>
    </h1>

    <?php
    $tag = get_queried_object();
    if ( $tag && ! empty( $tag->description ) ) :
    ?>
        <p class="bwp-archive-header__desc"><?php echo esc_html( $tag->description ); ?></p>
    <?php endif; ?>

</div>

<?php if ( have_posts() ) : ?>
    <ul class="bwp-archive-list">
        <?php while ( have_posts() ) : the_post(); ?>
            <li class="bwp-archive-list__item">
                <a href="<?php the_permalink(); ?>" class="bwp-archive-list__title"><?php the_title(); ?></a>
                <div class="bwp-archive-list__meta">
                    <?php
                    $form_slug = book_wp_get_form_slug();
                    if ( $form_slug ) :
                    ?>
                        <span class="bwp-badge bwp-badge--<?php echo esc_attr( $form_slug ); ?> bwp-badge--sm"><?php echo esc_html( book_wp_get_form_label() ); ?></span>
                    <?php endif; ?>

                    <span><?php echo esc_html( book_wp_reading_time() ); ?></span>

                    <?php $year = get_post_meta( get_the_ID(), 'book_wp_year', true ); ?>
                    <?php if ( $year ) : ?>
                        <span>&middot; <?php echo esc_html( $year ); ?></span>
                    <?php endif; ?>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>

    <?php the_posts_pagination( array(
        'prev_text' => '<span aria-hidden="true">&larr;</span>'
                    . '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'book-wp' ) . '</span>',
                    // W-08 / SC 2.4.4 — bare arrow entity has no accessible name
        'next_text' => '<span aria-hidden="true">&rarr;</span>'
                    . '<span class="screen-reader-text">' . esc_html__( 'Next page', 'book-wp' ) . '</span>',
                    // W-08 / SC 2.4.4
    ) ); ?>

<?php else : ?>
    <div class="bwp-empty-state">
        <?php echo book_wp_ornament( 'asterism' ); // phpcs:ignore ?>
        <p><?php echo esc_html( __( 'No works found.', 'book-wp' ) ); ?></p>
    </div>
<?php endif; ?>

</main><!-- #bwp-main -->

<?php get_footer(); ?>
