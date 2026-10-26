<?php
defined( 'ABSPATH' ) || exit;
/**
 * Template Name: Puisi — Generic Verse
 * Template Post Type: post
 *
 * For verse that does not fit any named literary form in the
 * taxonomy (syair, karmina, puisi, ratapan, etc.). Use this
 * template on posts assigned to no literary_form term, or
 * on posts where the verse form is too uncommon to categorise.
 *
 * Deliberately spare: no form-specific CSS is loaded, no
 * structured colophon apparatus. Just the poem, generous
 * whitespace, and the essentials.
 *
 * To use: In the WordPress editor, set Page Attributes →
 * Template to "Puisi — Generic Verse".
 *
 * @package Book_WP
 */

get_header(); ?>

<article <?php post_class( 'bwp-single bwp-single--generic-verse' ); ?> id="post-<?php the_ID(); ?>">

    <h1 class="bwp-work__title bwp-gv__title"><?php the_title(); ?></h1>

    <?php if ( '1' === get_option( 'book_wp_show_analytics', '1' ) ) : ?>
        <?php echo book_wp_analytics_bar(); // phpcs:ignore ?>
    <?php endif; ?>

    <?php echo book_wp_dedication(); // phpcs:ignore ?>

    <?php echo book_wp_epigraph(); // phpcs:ignore ?>

    <div class="bwp-work__content bwp-gv__content">
        <?php the_content(); ?>
    </div>

    <?php
    /* Minimal colophon — date, location, source, notes only.
       No form-specific apparatus since the form is unclassified. */
    $post_id = get_the_ID();
    $date    = get_post_meta( $post_id, 'book_wp_date_composed', true );
    $loc     = get_post_meta( $post_id, 'book_wp_location',      true );
    $year    = get_post_meta( $post_id, 'book_wp_year',          true );
    $source  = get_post_meta( $post_id, 'book_wp_source',        true );
    $lang    = get_post_meta( $post_id, 'book_wp_original_lang', true );
    $notes   = get_post_meta( $post_id, 'book_wp_notes',         true );

    if ( $date || $loc || $year || $source || $lang || $notes ) : ?>
    <footer class="bwp-colophon bwp-gv__colophon">
        <?php echo book_wp_ornament(); // phpcs:ignore ?>

        <?php if ( $date && $loc ) : ?>
            <p class="bwp-colophon__line bwp-colophon__line--dateline"><?php echo esc_html( $loc . ', ' . $date ); ?></p>
        <?php elseif ( $date ) : ?>
            <p class="bwp-colophon__line"><?php echo esc_html( $date ); ?></p>
        <?php elseif ( $loc ) : ?>
            <p class="bwp-colophon__line"><?php echo esc_html( $loc ); ?></p>
        <?php elseif ( $year ) : ?>
            <p class="bwp-colophon__line"><?php echo esc_html( $year ); ?></p>
        <?php endif; ?>

        <?php if ( $source ) : ?>
            <p class="bwp-colophon__line"><em><?php echo esc_html( $source ); ?></em></p>
        <?php endif; ?>
        <?php if ( $lang ) : ?>
            <p class="bwp-colophon__line"><?php echo esc_html( __( 'Translated from', 'book-wp' ) . ': ' . $lang ); ?></p>
        <?php endif; ?>
        <?php if ( $notes ) : ?>
            <div class="bwp-colophon__notes">
                <p class="bwp-colophon__notes-heading"><?php echo esc_html( __( "Author's Note", 'book-wp' ) ); ?></p>
                <div class="bwp-colophon__notes-text"><?php echo wp_kses_post( nl2br( $notes ) ); ?></div>
            </div>
        <?php endif; ?>
    </footer>
    <?php endif; ?>

    <?php echo book_wp_related_works(); // phpcs:ignore ?>

    <?php if ( '1' === get_option( 'book_wp_show_nav', '1' ) ) : ?>
        <?php echo book_wp_work_navigation(); // phpcs:ignore ?>
    <?php endif; ?>

</article>

<style>
/* ── Generic verse: inline styles scoped to this template ── */

.bwp-single--generic-verse .bwp-gv__title {
    font-family: var(--bwp-font-display, 'Cinzel', Georgia, serif);
    font-size: 1.8em;
    font-weight: 400;
    font-style: italic;
    text-align: left;
    letter-spacing: .02em;
    line-height: 1.2;
    color: var(--bwp-heading);
    margin-bottom: .9em;
}

.bwp-single--generic-verse .bwp-gv__content {
    max-width: 30em;
    margin-left: auto;
    margin-right: auto;
    font-size: 1.05em;
    line-height: 1.95;
    text-align: left;
    }

.bwp-single--generic-verse .bwp-gv__content p {
    text-indent: 0 !important;
    margin-bottom: 0;
    }
.bwp-single--generic-verse .bwp-gv__content p + p {
    margin-top: 1.8em;
}

/* No drop cap on unclassified verse */
.bwp-single--generic-verse .bwp-gv__content > p:first-of-type::first-letter {
    all: unset;
}

.bwp-single--generic-verse .bwp-gv__colophon {
    font-style: italic;
    opacity: .8;
}

@media screen and (min-width: 52em) {
    .bwp-single--generic-verse .bwp-gv__title   { font-size: 2.2em; }
    .bwp-single--generic-verse .bwp-gv__content { font-size: 1.1em; line-height: 2.0; }
}

@media print {
    .bwp-single--generic-verse .bwp-gv__content { line-height: 1.8; }
}
</style>

<?php if ( 'show' === get_option( 'book_wp_comments', 'show' ) && ( comments_open() || get_comments_number() ) ) : ?>
    <div class="bwp-comments">
        <?php comments_template(); ?>
    </div>
<?php endif; ?>

<?php get_footer(); ?>
