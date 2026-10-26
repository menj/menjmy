<?php defined( 'ABSPATH' ) || exit; ?>
<?php get_header(); ?>

<main id="bwp-main" class="bwp-main">

<div class="bwp-404 bwp-opening">
    <div class="bwp-opening__top">
        <p class="bwp-opening__kicker"><?php esc_html_e( 'Ralat 404', 'book-wp' ); ?></p>
        <?php $bwp_jawi_404 = get_option( 'book_wp_jawi_name', '' ); if ( $bwp_jawi_404 ) : ?>
            <span class="bwp-opening__seal" aria-hidden="true" dir="rtl" lang="ms-Arab"><?php echo esc_html( $bwp_jawi_404 ); ?></span>
        <?php endif; ?>
    </div>
    <!-- h1 — the sole heading on the 404 page; hierarchy starts at 1 (SC 2.4.2, SC 1.3.1) -->
    <h1 class="bwp-404__title"><?php echo esc_html( __( 'Page Not Found', 'book-wp' ) ); ?></h1>
    <p class="bwp-404__desc"><?php echo esc_html( __( 'Sorry, the page you are looking for does not exist.', 'book-wp' ) ); ?></p>
    <?php get_search_form(); ?>
</div>

</main><!-- #bwp-main -->

<?php get_footer(); ?>
