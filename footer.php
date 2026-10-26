<?php
defined( 'ABSPATH' ) || exit;
/**
 * Footer template — full-bleed nila band.
 *
 * 1. Sign-off: one italic line (+ optional second line) from settings.
 * 2. Lockup row: brand mark, anthology name, Jawi name | footer menu pills.
 * 3. Hairline row: copyright · social glyphs · theme credit.
 *
 * The .bwp-page-wrap opened in header.php closes before the footer so the
 * band can run edge to edge; its inner container shares the page gutters.
 *
 * @package Book_WP
 */

$bwp_anthology = get_option( 'book_wp_anthology_name', '' );
$bwp_anthology = $bwp_anthology ? $bwp_anthology : get_bloginfo( 'name' );
$bwp_author    = get_option( 'book_wp_author_name', '' );
$bwp_author    = $bwp_author ? $bwp_author : get_bloginfo( 'name' );
$bwp_copyright = get_option( 'book_wp_copyright', '' );
$bwp_jawi      = get_option( 'book_wp_jawi_name', '' );
$bwp_quote     = get_option( 'book_wp_footer_quote', book_wp_default_footer_quote() );
$bwp_quote_sub = get_option( 'book_wp_footer_quote_sub', book_wp_default_footer_quote_sub() );
$bwp_tagline   = get_bloginfo( 'description' );
$bwp_portrait  = get_option( 'book_wp_author_portrait', BOOK_WP_URI . '/assets/img/brand/portrait.png' );
?>

    <?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
    <aside class="bwp-footer-widgets">
        <?php dynamic_sidebar( 'footer-widgets' ); ?>
    </aside>
    <?php endif; ?>

</div><!-- .bwp-page-wrap -->

    <footer class="bwp-site-footer" role="contentinfo">
        <div class="bwp-site-footer__inner">

            <div class="bwp-site-footer__top">
                <?php if ( $bwp_quote ) : ?>
                <div class="bwp-site-footer__sign">
                    <p class="bwp-site-footer__lead"><?php echo esc_html( $bwp_quote ); ?></p>
                    <?php if ( $bwp_quote_sub ) : ?>
                    <p class="bwp-site-footer__sub"><?php echo esc_html( $bwp_quote_sub ); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="bwp-site-footer__identity">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'thumbnail', false, array( 'class' => 'bwp-site-footer__mark', 'alt' => '' ) ); ?>
                    <?php else : ?>
                        <img class="bwp-site-footer__mark" src="<?php echo esc_url( BOOK_WP_URI . '/assets/img/brand/mark.svg' ); ?>" alt="" width="52" height="52" loading="lazy" decoding="async">
                    <?php endif; ?>
                    <?php if ( $bwp_jawi ) : ?>
                    <span class="bwp-site-footer__jawi" dir="rtl" lang="ms-Arab"><?php echo esc_html( $bwp_jawi ); ?></span>
                    <?php endif; ?>
                    <a class="bwp-site-footer__anthology" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( $bwp_anthology ); ?></a>
                    <?php if ( $bwp_tagline ) : ?>
                    <span class="bwp-site-footer__tagline"><?php echo esc_html( $bwp_tagline ); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bwp-site-footer__row">
                <div class="bwp-site-footer__lockup">
                    <?php if ( $bwp_portrait ) : ?>
                        <span class="bwp-portrait bwp-portrait--sm"><img src="<?php echo esc_url( $bwp_portrait ); ?>" alt="" width="56" height="56" loading="lazy" decoding="async"></span>
                    <?php else : ?>
                        <span class="bwp-site-footer__initial" aria-hidden="true"><?php echo esc_html( mb_substr( $bwp_author, 0, 1 ) ); ?></span>
                    <?php endif; ?>
                    <span class="bwp-site-footer__names">
                        <span class="bwp-site-footer__author"><?php echo esc_html( $bwp_author ); ?></span>
                        <span class="bwp-site-footer__role"><?php esc_html_e( 'Penulis & penyusun', 'book-wp' ); ?></span>
                    </span>
                </div>

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
            </div>

            <div class="bwp-site-footer__bottom">
                <p class="bwp-site-footer__copyright">
                    <?php if ( ! empty( $bwp_copyright ) && '1' === get_option( 'book_wp_show_copyright', '1' ) ) : ?>
                        <?php echo wp_kses_post( $bwp_copyright ); ?>
                    <?php else : ?>
                        &copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . $bwp_author ); ?> &middot; <?php esc_html_e( 'Hakcipta terpelihara', 'book-wp' ); ?>
                    <?php endif; ?>
                </p>

                <?php
                $bwp_social = book_wp_social_icons();
                if ( $bwp_social ) : ?>
                <div class="bwp-site-footer__social">
                    <?php echo $bwp_social; // phpcs:ignore — fully escaped in book_wp_social_icons() ?>
                </div>
                <?php endif; ?>

                <p class="bwp-site-footer__credit">
                    <a href="https://github.com/menj/menjmy">Sasterawan</a> <?php esc_html_e( 'by', 'book-wp' ); ?> <a href="https://github.com/menj">MENJ</a>
                    <?php $bwp_attr = book_wp_social_attribution(); if ( $bwp_attr ) echo ' &middot; ' . $bwp_attr; // phpcs:ignore — escaped in helper ?>
                </p>
            </div>

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

<?php wp_footer(); ?>

</body>
</html>
