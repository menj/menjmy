<?php defined( 'ABSPATH' ) || exit; ?>
<?php get_header(); ?>

<main id="bwp-main" class="bwp-main">

<div class="bwp-archive-header bwp-opening">
    <div class="bwp-opening__top">
        <?php
        $bwp_tax_label = '';
        $bwp_tax_obj   = get_queried_object();
        if ( $bwp_tax_obj && isset( $bwp_tax_obj->taxonomy ) ) {
            $bwp_tax_label = ( 'literary_form' === $bwp_tax_obj->taxonomy ) ? __( 'Bentuk', 'book-wp' ) : __( 'Jalur tema', 'book-wp' );
        } elseif ( is_date() ) {
            $bwp_tax_label = __( 'Arkib', 'book-wp' );
        } elseif ( is_author() ) {
            $bwp_tax_label = __( 'Penulis', 'book-wp' );
        }
        if ( $bwp_tax_label ) : ?>
            <p class="bwp-archive-header__taxlabel bwp-opening__kicker"><?php echo esc_html( $bwp_tax_label ); ?></p>
        <?php endif; ?>
        <?php
        $bwp_seal = ( $bwp_tax_obj && isset( $bwp_tax_obj->taxonomy ) && 'literary_form' === $bwp_tax_obj->taxonomy ) ? book_wp_form_jawi( $bwp_tax_obj->slug ) : get_option( 'book_wp_jawi_name', '' );
        if ( $bwp_seal ) : ?>
            <span class="bwp-opening__seal" aria-hidden="true" dir="rtl" lang="ms-Arab"><?php echo esc_html( $bwp_seal ); ?></span>
        <?php endif; ?>
    </div>

    <h1 class="bwp-archive-header__title"><?php the_archive_title(); ?></h1>

    <?php
    // Show description for literary_form or category archives.
    $term = get_queried_object();
    if ( $term && isset( $term->taxonomy ) ) :
        $desc = '';
        if ( 'literary_form' === $term->taxonomy ) {
            $desc = book_wp_form_description( $term->slug );
        } elseif ( 'category' === $term->taxonomy && ! empty( $term->description ) ) {
            $desc = $term->description;
        }
        if ( $desc ) :
    ?>
        <p class="bwp-archive-header__desc"><?php echo esc_html( $desc ); ?></p>
    <?php endif; endif; ?>

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
