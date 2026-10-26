<?php
defined( 'ABSPATH' ) || exit;
/**
 * Template Name: Colophon Page
 *
 * A dedicated bibliographic / publishing information page.
 * Traditional in literary publishing to document provenance.
 *
 * @package Book_WP
 */
?>
<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'bwp-colophon-page' ); ?> id="page-<?php the_ID(); ?>">
        <header class="bwp-work__opening bwp-opening bwp-opening--page">
            <div class="bwp-opening__top">
                <p class="bwp-opening__kicker"><?php esc_html_e( 'Kolofon', 'book-wp' ); ?></p>
                <?php $bwp_jawi_page = get_option( 'book_wp_jawi_name', '' ); if ( $bwp_jawi_page ) : ?>
                    <span class="bwp-opening__seal" aria-hidden="true" dir="rtl" lang="ms-Arab"><?php echo esc_html( $bwp_jawi_page ); ?></span>
                <?php endif; ?>
            </div>
            <h1 class="bwp-work__title"><?php the_title(); ?></h1>
        </header>
        <div class="bwp-work__layout">
        <div class="bwp-work__main">
        <div class="bwp-work__content bwp-colophon-page__content">
            <?php the_content(); ?>
        </div>
        <?php edit_post_link( esc_html( __( 'Edit', 'book-wp' ) ), '<p class="bwp-edit-link">', '</p>' ); ?>
        </div>
        <div class="bwp-work__aside"><?php echo book_wp_author_card(); // phpcs:ignore ?></div>
        </div>
    </article>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
