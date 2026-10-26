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
    /* Book WP — Night-mode preload (runs synchronously before first paint).
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
        ?>
        <?php
        // Author name is always H2 across every page type.
        // H1 is owned by each template (anthology title, post title,
        // archive title, etc.). H2 is reserved for the author name here
        // in the site header, making it the consistent second-level
        // landmark in the document outline on all views.
        ?>
        <h2 class="bwp-site-header__title">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $bwp_author ); ?></a>
        </h2>

        <?php if ( has_nav_menu( 'primary' ) ) : ?>
        <nav class="bwp-nav" aria-label="<?php esc_attr_e( 'Primary Menu', 'book-wp' ); ?>">
            <?php wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_class'     => 'bwp-nav__list',
                'container'      => false,
                'depth'          => 2,
                'fallback_cb'    => false,
            ) ); ?>
        </nav>
        <?php endif; ?>

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
