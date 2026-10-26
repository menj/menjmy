<?php defined( 'ABSPATH' ) || exit; ?>
<?php get_header(); ?>

<main id="bwp-main" class="bwp-main">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'bwp-page' ); ?> id="page-<?php the_ID(); ?>">
        <header class="bwp-work__opening bwp-work__opening--page">
            <h1 class="bwp-work__title"><?php the_title(); ?></h1>
        </header>
        <div class="bwp-work__content">
            <?php the_content(); ?>
        </div>
        <?php edit_post_link( esc_html( __( 'Edit', 'book-wp' ) ), '<p class="bwp-edit-link">', '</p>' ); ?>
    </article>

<?php endwhile; endif; ?>

</main><!-- #bwp-main -->

<?php get_footer(); ?>
