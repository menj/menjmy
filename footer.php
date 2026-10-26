<?php
defined( 'ABSPATH' ) || exit;
/**
 * Footer template
 *
 * Colophon-style footer. If a copyright string is set, it is shown alone
 * as the attribution line (it already contains anthology + author names).
 * If no copyright is set, anthology name and author are shown separately.
 *
 * No ornament — content templates own their closing ornaments.
 *
 * @package Book_WP
 */


$bwp_anthology = get_option( 'book_wp_anthology_name', '' );
$bwp_author    = get_option( 'book_wp_author_name', '' );
$bwp_author    = $bwp_author ? $bwp_author : get_bloginfo( 'name' );
$bwp_copyright = get_option( 'book_wp_copyright', '' );
?>

    <?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
    <aside class="bwp-footer-widgets">
        <?php dynamic_sidebar( 'footer-widgets' ); ?>
    </aside>
    <?php endif; ?>

    <footer class="bwp-site-footer" role="contentinfo">

        <div class="bwp-site-footer__inner">

            <?php if ( has_nav_menu( 'footer' ) ) : ?>
            <nav class="bwp-footer-nav" aria-label="<?php esc_attr_e( 'Footer Menu', 'book-wp' ); ?>">
                <?php wp_nav_menu( array(
                    'theme_location' => 'footer',
                    'menu_class'     => 'bwp-footer-nav__list',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ) ); ?>
            </nav>
            <?php endif; ?>

            <?php if ( has_custom_logo() ) : ?>
            <div class="bwp-site-footer__logo"><?php the_custom_logo(); ?></div>
            <?php endif; ?>

            <?php
            $bwp_social = book_wp_social_icons();
            if ( $bwp_social ) : ?>
            <div class="bwp-site-footer__social">
                <?php echo $bwp_social; // phpcs:ignore — fully escaped in book_wp_social_icons() ?>
            </div>
            <?php endif; ?>

            <div class="bwp-site-footer__colophon">

                <?php if ( ! empty( $bwp_anthology ) ) : ?>
                <p class="bwp-site-footer__anthology"><?php echo esc_html( $bwp_anthology ); ?></p>
                <?php endif; ?>

                <p class="bwp-site-footer__author"><?php echo esc_html( $bwp_author ); ?></p>

                <?php if ( ! empty( $bwp_copyright ) && '1' === get_option( 'book_wp_show_copyright', '1' ) ) : ?>
                <p class="bwp-site-footer__copyright"><?php echo wp_kses_post( $bwp_copyright ); ?></p>
                <?php endif; ?>

            </div><!-- .bwp-site-footer__colophon -->

            <p class="bwp-site-footer__credit">
                <a href="https://github.com/menj/book-wp">Book&nbsp;WP</a>
                <?php esc_html_e( 'by', 'book-wp' ); ?>
                <a href="https://github.com/menj">MENJ</a>
            </p>

        </div><!-- .bwp-site-footer__inner -->

    </footer>

<?php if ( '1' === get_option( 'book_wp_back_to_top', '1' ) ) : ?>
<button
    id="bwpBackToTop"
    class="bwp-back-to-top"
    aria-label="<?php esc_attr_e( 'Back to top', 'book-wp' ); ?>"
    hidden
>
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <polyline points="18 15 12 9 6 15"></polyline>
    </svg>
</button>
<?php endif; ?>

</div><!-- .bwp-page-wrap -->

<?php wp_footer(); ?>

</body>
</html>
