<?php defined( 'ABSPATH' ) || exit; ?>
<?php get_header(); ?>

<main id="bwp-main" class="bwp-main">

<div class="bwp-404">
    <?php echo book_wp_ornament( 'asterism' ); // phpcs:ignore ?>
    <!-- h1 — this is the sole heading on the 404 page; heading hierarchy must start at 1. (W-06 / SC 2.4.2, SC 1.3.1) -->
    <h1 class="bwp-404__title"><?php echo esc_html( __( 'Page Not Found', 'book-wp' ) ); ?></h1>
    <p class="bwp-404__desc"><?php echo esc_html( __( 'Sorry, the page you are looking for does not exist.', 'book-wp' ) ); ?></p>
    <?php echo book_wp_ornament( 'dash' ); // phpcs:ignore ?>
    <?php get_search_form(); ?>
</div>

</main><!-- #bwp-main -->

<?php get_footer(); ?>
