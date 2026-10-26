<?php
defined( 'ABSPATH' ) || exit;
/**
 * Template Name: Foreword
 *
 * A literary foreword / kata pengantar page with special typography.
 *
 * @package Book_WP
 */
?>
<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'bwp-foreword' ); ?> id="page-<?php the_ID(); ?>">

        <?php echo book_wp_ornament( 'leaf' ); // phpcs:ignore ?>

        <h1 class="bwp-work__title"><?php echo esc_html( __( 'Foreword', 'book-wp' ) ); ?></h1>

        <hr class="bwp-rule">

        <div class="bwp-work__content bwp-foreword__content">
            <?php the_content(); ?>
        </div>

        <?php echo book_wp_ornament( 'fleuron' ); // phpcs:ignore ?>

        <?php edit_post_link( esc_html( __( 'Edit', 'book-wp' ) ), '<p class="bwp-edit-link">', '</p>' ); ?>

    </article>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
