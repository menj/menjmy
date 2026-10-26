<?php defined( 'ABSPATH' ) || exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    <?php if ( '1' === get_option( 'book_wp_night_toggle', '1' ) ) : ?>
    <script>
    /* Sasterawan — Night-mode preload (runs synchronously before first paint).
       Reads saved preference from localStorage. If none is set, falls back to
       the OS prefers-color-scheme media query. Adds .bwp-night to <html> so
       CSS applies before <body> is painted, eliminating the flash-of-light-theme
       that occurred when the class was applied only on DOMContentLoaded.

       Execution order: time-theme.js (enqueued in wp_head) runs BEFORE this
       script. If the saved night toggle is stale (activated on a previous date
       and current period is daytime), time-theme.js clears localStorage to '0'
       before this script reads it — so no .bwp-night class is applied and
       there is no flash of dark mode on an expired preference.               */
    (function(){try{
        var k='bwp_night_mode', saved=localStorage.getItem(k);
        if(saved==='1'||(saved===null&&window.matchMedia('(prefers-color-scheme:dark)').matches)){
            document.documentElement.classList.add('bwp-night');
        }
    }catch(e){}}());
    </script>
    <?php endif; ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="bwp-skip-link" href="#bwp-main"><?php esc_html_e( 'Skip to content', 'book-wp' ); ?></a>

<div class="bwp-page-wrap">


    <header class="bwp-site-header" role="banner">

        <?php
        $bwp_author = get_option( 'book_wp_author_name', '' );
        $bwp_author = $bwp_author ? $bwp_author : get_bloginfo( 'name' );
        // Author name stays the H2 landmark on every view; the H1 belongs to
        // each template (anthology title, work title, archive title).
        ?>
        <?php
        $bwp_site_name = get_option( 'book_wp_anthology_name', '' );
        $bwp_site_name = $bwp_site_name ? $bwp_site_name : get_bloginfo( 'name' );
        ?>
        <a class="bwp-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
            <?php if ( has_custom_logo() ) : ?>
                <?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'thumbnail', false, array( 'class' => 'bwp-brand__mark', 'alt' => '' ) ); ?>
            <?php else : ?>
                <img class="bwp-brand__mark" src="<?php echo esc_url( BOOK_WP_URI . '/assets/img/brand/mark.svg' ); ?>" alt="" width="46" height="46" decoding="async">
            <?php endif; ?>
            <span class="bwp-brand__text">
                <h2 class="bwp-site-header__title"><?php echo esc_html( $bwp_site_name ); ?></h2>
                <?php if ( get_bloginfo( 'description' ) ) : ?>
                <span class="bwp-site-header__tagline"><?php bloginfo( 'description' ); ?></span>
                <?php endif; ?>
            </span>
        </a>

        <?php if ( has_nav_menu( 'primary' ) ) : ?>
        <button class="bwp-nav-toggle" type="button" aria-expanded="false" aria-controls="bwp-nav">
            <span class="bwp-nav-toggle__label"><?php esc_html_e( 'Menu', 'book-wp' ); ?></span>
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
        <nav class="bwp-nav" id="bwp-nav" aria-label="<?php esc_attr_e( 'Primary Menu', 'book-wp' ); ?>">
            <?php wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_class'     => 'bwp-nav__list',
                'container'      => false,
                'depth'          => 2,
                'fallback_cb'    => false,
            ) ); ?>
        </nav>
        <?php endif; ?>

        <span class="bwp-site-header__band" aria-hidden="true"></span>

        <?php if ( is_single() ) : ?>
            <?php
            $chapter_blurb = '';
            $cats = get_the_category();
            if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
                foreach ( $cats as $cat ) {
                    $desc = term_description( $cat->term_id, 'category' );
                    if ( ! empty( $desc ) ) {
                        $chapter_blurb = $desc;
                        break;
                    }
                }
            }
            ?>
            <?php if ( ! empty( $chapter_blurb ) ) : ?>
                <div class="bwp-chapter-blurb">
                    <?php echo wp_kses_post( $chapter_blurb ); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </header>
