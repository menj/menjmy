<?php
/**
 * Sasterawan — Comments Template
 *
 * Renders both the comment list and the comment form with theme-native
 * markup and CSS classes. WordPress core output (wp_list_comments,
 * comment_form) is fully customised via callback arguments so no WordPress
 * default styles bleed through.
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;
if ( post_password_required() ) return;
?>

<section id="comments" class="bwp-comments" aria-label="<?php esc_attr_e( 'Comments', 'book-wp' ); ?>">

<?php /* ── Comment list ──────────────────────────────────────────── */ ?>

<?php if ( have_comments() ) : ?>

    <h3 class="bwp-comments__heading">
        <?php
        printf(
            /* translators: %s: number of comments */
            esc_html( _n( '%s Comment', '%s Comments', get_comments_number(), 'book-wp' ) ),
            '<span class="bwp-comments__count">' . number_format_i18n( get_comments_number() ) . '</span>'
        );
        ?>
    </h3>

    <?php echo book_wp_ornament( 'dash' ); // phpcs:ignore ?>

    <ol class="bwp-comment-list">
        <?php
        wp_list_comments( array(
            'style'             => 'ol',
            'short_ping'        => true,
            'avatar_size'       => 48,
            'callback'          => 'book_wp_comment_callback',
            'end-callback'      => 'book_wp_comment_end_callback',
        ) );
        ?>
    </ol>

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
        <nav class="bwp-comment-nav" aria-label="<?php esc_attr_e( 'Comment navigation', 'book-wp' ); ?>">
            <span class="bwp-comment-nav__prev"><?php previous_comments_link( '&larr; ' . __( 'Older Comments', 'book-wp' ) ); ?></span>
            <span class="bwp-comment-nav__next"><?php next_comments_link( __( 'Newer Comments', 'book-wp' ) . ' &rarr;' ); ?></span>
        </nav>
    <?php endif; ?>

<?php endif; ?>

<?php /* ── Comment form ──────────────────────────────────────────── */ ?>

<?php
comment_form( array(
    'title_reply'          => __( 'Leave a Comment', 'book-wp' ),
    'title_reply_to'       => __( 'Reply to %s', 'book-wp' ),
    'cancel_reply_link'    => __( 'Cancel', 'book-wp' ),
    'label_submit'         => __( 'Post Comment', 'book-wp' ),
    'class_submit'         => 'bwp-comment-submit',
    'class_form'           => 'bwp-comment-form',
    'comment_field'        =>
        '<div class="bwp-cf-field bwp-cf-field--full">'
        . '<label class="bwp-cf-label" for="comment">' . __( 'Comment', 'book-wp' ) . ' <span class="bwp-cf-required" aria-hidden="true">*</span></label>'
        . '<textarea id="comment" name="comment" class="bwp-cf-textarea" rows="7" required aria-required="true" placeholder="'
        . esc_attr__( 'Leave your words here…', 'book-wp' ) . '"></textarea>'
        . '</div>',
    'fields' => array(
        'author' =>
            '<div class="bwp-cf-row">'
            . '<div class="bwp-cf-field">'
            . '<label class="bwp-cf-label" for="author">' . __( 'Name', 'book-wp' ) . ' <span class="bwp-cf-required" aria-hidden="true">*</span></label>'
            . '<input id="author" name="author" type="text" class="bwp-cf-input" required aria-required="true" autocomplete="name" value="' . esc_attr( isset( $_COOKIE['comment_author_' . COOKIEHASH] ) ? $_COOKIE['comment_author_' . COOKIEHASH] : '' ) . '">'  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
            . '</div>',
        'email' =>
            '<div class="bwp-cf-field">'
            . '<label class="bwp-cf-label" for="email">' . __( 'Email', 'book-wp' ) . ' <span class="bwp-cf-required" aria-hidden="true">*</span></label>'
            . '<input id="email" name="email" type="email" class="bwp-cf-input" required aria-required="true" autocomplete="email" value="' . esc_attr( isset( $_COOKIE['comment_author_email_' . COOKIEHASH] ) ? $_COOKIE['comment_author_email_' . COOKIEHASH] : '' ) . '">'  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
            . '</div>'
            . '</div>',  // close .bwp-cf-row
        'url'     => '',   // omit URL field — not needed for a literary anthology
        'cookies' =>
            '<div class="bwp-cf-field bwp-cf-field--cookies">'
            . '<label class="bwp-cf-cookie-label">'
            . '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . ( isset( $_COOKIE['comment_author_' . COOKIEHASH] ) ? ' checked' : '' ) . '>'  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
            . ' ' . __( 'Save my name and email in this browser for the next time I comment.', 'book-wp' )
            . '</label>'
            . '</div>',
    ),
    // W-05 / SC 3.3.2: The asterisk symbol in the note was aria-hidden, so AT read
    // "required fields are marked with" and stopped — syntactically incomplete.
    // Fix: aria-hidden removed from the note's asterisk only so AT reads the full
    // sentence. The asterisks on individual labels remain aria-hidden (redundant to
    // aria-required="true" which is the programmatic signal).
    'comment_notes_before' => '<p class="bwp-cf-note">' . __( 'Required fields are marked with', 'book-wp' ) . ' <span class="bwp-cf-required">*</span></p>',
    'comment_notes_after'  => '',
) );
?>

</section><!-- #comments -->

<?php
/* ── Comment list callback ──────────────────────────────────────────── */

/**
 * Render a single comment with theme-native markup.
 *
 * @param WP_Comment $comment
 * @param array      $args
 * @param int        $depth
 */
function book_wp_comment_callback( $comment, $args, $depth ) {
    $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'bwp-comment', $comment ); ?>>

        <article class="bwp-comment__body">

            <header class="bwp-comment__header">
                <div class="bwp-comment__avatar">
                    <?php echo get_avatar( $comment, $args['avatar_size'], '', '', array( 'class' => 'bwp-comment__avatar-img' ) ); ?>
                </div>
                <div class="bwp-comment__meta">
                    <span class="bwp-comment__author"><?php comment_author(); ?></span>
                    <a class="bwp-comment__date" href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
                        <time datetime="<?php comment_time( 'c' ); ?>">
                            <?php
                            printf(
                                /* translators: 1: date, 2: time */
                                esc_html__( '%1$s at %2$s', 'book-wp' ),
                                get_comment_date( '', $comment ),
                                get_comment_time( '', false, false, $comment )
                            );
                            ?>
                        </time>
                    </a>
                    <?php if ( '0' === $comment->comment_approved ) : ?>
                        <em class="bwp-comment__pending"><?php esc_html_e( 'Your comment is awaiting moderation.', 'book-wp' ); ?></em>
                    <?php endif; ?>
                </div>
                <div class="bwp-comment__actions">
                    <?php
                    comment_reply_link( array_merge( $args, array(
                        'add_below' => 'comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'before'    => '<span class="bwp-comment__reply">',
                        'after'     => '</span>',
                        'reply_text'=> __( 'Reply', 'book-wp' ),
                    ) ) );
                    edit_comment_link( __( 'Edit', 'book-wp' ), '<span class="bwp-comment__edit">', '</span>' );
                    ?>
                </div>
            </header>

            <div class="bwp-comment__content">
                <?php comment_text(); ?>
            </div>

        </article>
    <?php
    // Note: closing tag printed by book_wp_comment_end_callback()
}

/**
 * Print the closing tag for a comment list item.
 *
 * @param WP_Comment $comment
 * @param array      $args
 * @param int        $depth
 */
function book_wp_comment_end_callback( $comment, $args, $depth ) {
    $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
    echo '</' . $tag . '>';
}
