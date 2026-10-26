<?php
/**
 * Sasterawan — Admin Settings
 *
 * Design system: modern_settings_ui v1.0.0
 * Wrapper: .theme-settings-root  |  Each tab has its own <form>.
 *
 * Tabs: General · Typography · Literary · Reading · Advanced
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/* ── Register all settings ─────────────────────────────────────────────── */

add_action( 'admin_init', 'book_wp_register_settings' );

function book_wp_register_settings() {
    $settings = array(
        'book_wp_author_name'     => 'sanitize_text_field',
        'book_wp_author_portrait' => 'esc_url_raw',
        'book_wp_show_hero_image' => 'book_wp_cb',
        'book_wp_hero_image'      => 'esc_url_raw',
        'book_wp_anthology_name'  => 'sanitize_text_field',
        'book_wp_jawi_name'       => 'sanitize_text_field',
        'book_wp_jawa_name'       => 'sanitize_text_field',
        'book_wp_footer_quote'    => 'sanitize_textarea_field',
        'book_wp_footer_quote_sub' => 'sanitize_text_field',
        'book_wp_show_featured_work' => 'book_wp_cb',
        'book_wp_featured_work_id'   => 'absint',
        'book_wp_copyright'       => 'wp_kses_post',
        'book_wp_show_copyright'    => 'book_wp_cb',
        'book_wp_toc_grouping'      => 'sanitize_text_field',
        'book_wp_show_badges'       => 'book_wp_cb',
        'book_wp_archive_layout'    => 'sanitize_text_field',
        'book_wp_archive_pagination'=> 'sanitize_text_field',
        'book_wp_toc_layout'        => 'sanitize_text_field',
        'book_wp_toc_pagination'    => 'sanitize_text_field',
        'book_wp_font'            => 'sanitize_key',
        'book_wp_remote_fonts'    => 'book_wp_cb',
        'book_wp_color_scheme'    => 'sanitize_key',
        'book_wp_time_theme'      => 'sanitize_text_field',
        'book_wp_drop_cap_cerpen'    => 'book_wp_cb',
        'book_wp_drop_cap_novel'     => 'book_wp_cb',
        'book_wp_drop_cap_hikayat'   => 'book_wp_cb',
        'book_wp_drop_cap_memoir'    => 'book_wp_cb',
        'book_wp_drop_cap_esei'      => 'book_wp_cb',
        'book_wp_drop_cap_nukilan'   => 'book_wp_cb',
        'book_wp_dropcap_font'       => 'sanitize_key',
        'book_wp_poetry_align'       => 'sanitize_text_field',
        'book_wp_stanza_spacing'     => 'sanitize_key',
        'book_wp_poetry_line_height' => 'sanitize_key',
        'book_wp_ornament_style'  => 'sanitize_text_field',
        'book_wp_show_featured'   => 'book_wp_cb',
        'book_wp_related_works'   => 'book_wp_cb',
        'book_wp_stanza_numbers'  => 'book_wp_cb',
        'book_wp_show_analytics'  => 'book_wp_cb',
        'book_wp_show_tags'       => 'book_wp_cb',
        'book_wp_hidden_tags'     => 'book_wp_sanitize_tag_ids',
        'book_wp_progress_bar'    => 'book_wp_cb',
        'book_wp_keyboard_nav'    => 'book_wp_cb',
        'book_wp_reading_memory'  => 'book_wp_cb',
        'book_wp_night_toggle'    => 'book_wp_cb',
        'book_wp_comments'        => 'sanitize_text_field',
        'book_wp_social_twitter'   => 'esc_url_raw',
        'book_wp_social_viaf' => 'esc_url_raw',
        'book_wp_social_academia' => 'esc_url_raw',
        'book_wp_social_wikidata' => 'esc_url_raw',
        'book_wp_social_substack' => 'esc_url_raw',
        'book_wp_social_whatsapp' => 'esc_url_raw',
        'book_wp_social_telegram' => 'esc_url_raw',
        'book_wp_social_tiktok' => 'esc_url_raw',
        'book_wp_social_bluesky' => 'esc_url_raw',
        'book_wp_social_facebook'  => 'esc_url_raw',
        'book_wp_social_instagram' => 'esc_url_raw',
        'book_wp_social_threads'   => 'esc_url_raw',
        'book_wp_social_mastodon'  => 'esc_url_raw',
        'book_wp_social_linkedin'  => 'esc_url_raw',
        'book_wp_social_youtube'   => 'esc_url_raw',
        'book_wp_social_medium'    => 'esc_url_raw',
        'book_wp_social_wikipedia' => 'esc_url_raw',
        'book_wp_social_github'    => 'esc_url_raw',
        'book_wp_social_goodreads' => 'esc_url_raw',
        'book_wp_social_worldcat'  => 'esc_url_raw',
        'book_wp_social_loc'       => 'esc_url_raw',
        'book_wp_social_nlb'       => 'esc_url_raw',
        'book_wp_social_pnm'       => 'esc_url_raw',
        'book_wp_social_orcid'     => 'esc_url_raw',
        'book_wp_social_isni'      => 'esc_url_raw',
        'book_wp_social_rss'       => 'esc_url_raw',
        'book_wp_structured_data'   => 'book_wp_cb',
        'book_wp_social_meta_tags'  => 'book_wp_cb',
        'book_wp_json_feed'         => 'book_wp_cb',
        'book_wp_form_permalinks'   => 'book_wp_cb',
        'book_wp_custom_css'        => 'wp_strip_all_tags',
    );
    foreach ( $settings as $name => $cb ) {
        register_setting( 'book_wp_settings', $name, array( 'sanitize_callback' => $cb ) );
    }
}

function book_wp_cb( $v ) { return ( '1' === (string) $v ) ? '1' : '0'; }

/**
 * Sanitize a comma-separated list of tag slugs.
 * Each slug is run through sanitize_key() — rejects anything that isn't a
 * lowercase alphanumeric slug with hyphens.
 *
 * @param string $v Raw input value.
 * @return string Sanitized comma-separated slug list.
 */
function book_wp_sanitize_tag_slugs( $v ) {
    if ( empty( $v ) ) return '';
    $slugs = array_filter( array_map( 'sanitize_key', explode( ',', $v ) ) );
    return implode( ',', $slugs );
}

/**
 * Sanitize a comma-separated list of tag IDs.
 * Each value is cast to a positive integer and validated against the post_tag
 * taxonomy — invalid or non-existent term IDs are silently dropped.
 *
 * @param string $v Raw input value (comma-separated term IDs).
 * @return string Validated comma-separated term ID list.
 */
function book_wp_sanitize_tag_ids( $v ) {
    if ( empty( $v ) ) return '';
    $ids   = array_values( array_filter( array_map( 'absint', explode( ',', $v ) ) ) );
    $valid = array();
    foreach ( $ids as $id ) {
        if ( term_exists( $id, 'post_tag' ) ) {
            $valid[] = $id;
        }
    }
    return implode( ',', $valid );
}

/* ── Per-tab save handler ──────────────────────────────────────────────── */

add_action( 'admin_post_book_wp_save_tab', 'book_wp_handle_tab_save' );

function book_wp_handle_tab_save() {
    if ( ! current_user_can( 'edit_theme_options' ) ) wp_die( 'Unauthorised' );

    $tab = isset( $_POST['bwp_tab'] ) ? sanitize_key( $_POST['bwp_tab'] ) : 'general';
    check_admin_referer( 'book_wp_save_' . $tab );

    $tab_fields = array(
        'general' => array(
            'book_wp_author_name'    => 'sanitize_text_field',
            'book_wp_author_portrait' => 'esc_url_raw',
            'book_wp_show_hero_image' => 'book_wp_cb',
            'book_wp_hero_image'     => 'esc_url_raw',
            'book_wp_anthology_name' => 'sanitize_text_field',
            'book_wp_jawi_name'      => 'sanitize_text_field',
            'book_wp_jawa_name'      => 'sanitize_text_field',
            'book_wp_footer_quote'   => 'sanitize_textarea_field',
            'book_wp_footer_quote_sub' => 'sanitize_text_field',
            'book_wp_show_featured_work'  => 'book_wp_cb',
            'book_wp_featured_work_id'    => 'absint',
            'book_wp_copyright'           => 'wp_kses_post',
            'book_wp_show_copyright'      => 'book_wp_cb',
            'book_wp_toc_grouping'        => 'sanitize_text_field',
            'book_wp_show_badges'         => 'book_wp_cb',
            'book_wp_archive_layout'      => 'sanitize_text_field',
            'book_wp_archive_pagination'  => 'sanitize_text_field',
            'book_wp_toc_layout'          => 'sanitize_text_field',
            'book_wp_toc_pagination'      => 'sanitize_text_field',
        ),
        'typography' => array(
            'book_wp_font'             => 'sanitize_key',
            'book_wp_remote_fonts'     => 'book_wp_cb',
            'book_wp_color_scheme'     => 'sanitize_key',
            'book_wp_time_theme'       => 'sanitize_text_field',
            'book_wp_drop_cap_cerpen'    => 'book_wp_cb',
            'book_wp_drop_cap_novel'     => 'book_wp_cb',
            'book_wp_drop_cap_hikayat'   => 'book_wp_cb',
            'book_wp_drop_cap_memoir'    => 'book_wp_cb',
            'book_wp_drop_cap_esei'      => 'book_wp_cb',
            'book_wp_drop_cap_nukilan'   => 'book_wp_cb',
            'book_wp_dropcap_font'       => 'sanitize_key',
            'book_wp_poetry_align'       => 'sanitize_text_field',
            'book_wp_stanza_spacing'     => 'sanitize_key',
            'book_wp_poetry_line_height' => 'sanitize_key',
        ),
        'literary' => array(
            'book_wp_ornament_style' => 'sanitize_text_field',
            'book_wp_show_featured'  => 'book_wp_cb',
            'book_wp_related_works'  => 'book_wp_cb',
            'book_wp_stanza_numbers' => 'book_wp_cb',
            'book_wp_show_analytics' => 'book_wp_cb',
            'book_wp_show_tags'      => 'book_wp_cb',
            'book_wp_hidden_tags'    => 'book_wp_sanitize_tag_ids',
        ),
        'reading' => array(
            'book_wp_progress_bar'   => 'book_wp_cb',
            'book_wp_keyboard_nav'   => 'book_wp_cb',
            'book_wp_reading_memory' => 'book_wp_cb',
            'book_wp_night_toggle'   => 'book_wp_cb',
            'book_wp_share_row'      => 'book_wp_cb',
            'book_wp_back_to_top'    => 'book_wp_cb',
            'book_wp_comments'       => 'sanitize_text_field',
        ),
        'advanced' => array(
            'book_wp_structured_data'  => 'book_wp_cb',
            'book_wp_social_meta_tags' => 'book_wp_cb',
            'book_wp_json_feed'        => 'book_wp_cb',
            'book_wp_form_permalinks'  => 'book_wp_cb',
            'book_wp_custom_css'       => 'wp_strip_all_tags',
        ),
        'social' => array(
            'book_wp_social_twitter'   => 'esc_url_raw',
            'book_wp_social_viaf' => 'esc_url_raw',
            'book_wp_social_academia' => 'esc_url_raw',
            'book_wp_social_wikidata' => 'esc_url_raw',
            'book_wp_social_substack' => 'esc_url_raw',
            'book_wp_social_whatsapp' => 'esc_url_raw',
            'book_wp_social_telegram' => 'esc_url_raw',
            'book_wp_social_tiktok' => 'esc_url_raw',
            'book_wp_social_bluesky' => 'esc_url_raw',
            'book_wp_social_facebook'  => 'esc_url_raw',
            'book_wp_social_instagram' => 'esc_url_raw',
            'book_wp_social_threads'   => 'esc_url_raw',
            'book_wp_social_mastodon'  => 'esc_url_raw',
            'book_wp_social_linkedin'  => 'esc_url_raw',
            'book_wp_social_youtube'   => 'esc_url_raw',
            'book_wp_social_medium'    => 'esc_url_raw',
            'book_wp_social_wikipedia' => 'esc_url_raw',
            'book_wp_social_github'    => 'esc_url_raw',
            'book_wp_social_goodreads' => 'esc_url_raw',
            'book_wp_social_worldcat'  => 'esc_url_raw',
            'book_wp_social_loc'       => 'esc_url_raw',
            'book_wp_social_nlb'       => 'esc_url_raw',
            'book_wp_social_pnm'       => 'esc_url_raw',
            'book_wp_social_orcid'     => 'esc_url_raw',
            'book_wp_social_isni'      => 'esc_url_raw',
            'book_wp_social_rss'       => 'esc_url_raw',
        ),
    );

    if ( ! isset( $tab_fields[ $tab ] ) ) {
        wp_safe_redirect( admin_url( 'themes.php?page=book-wp-settings&tab=' . $tab ) );
        exit;
    }

    foreach ( $tab_fields[ $tab ] as $option => $sanitise ) {
        $raw = isset( $_POST[ $option ] ) ? wp_unslash( $_POST[ $option ] ) : '0';
        update_option( $option, call_user_func( $sanitise, $raw ) );
    }

    wp_safe_redirect( admin_url( 'themes.php?page=book-wp-settings&tab=' . $tab . '&settings-updated=true' ) );
    exit;
}

/* ── Admin assets ──────────────────────────────────────────────────────── */

add_action( 'admin_enqueue_scripts', 'book_wp_admin_assets' );

function book_wp_admin_assets( $hook ) {
    if ( 'appearance_page_book-wp-settings' === $hook ) {
        wp_enqueue_style( 'book-wp-admin', BOOK_WP_URI . '/assets/css/admin.css', array(), BOOK_WP_VERSION );
        wp_enqueue_script( 'book-wp-admin-settings', BOOK_WP_URI . '/assets/js/admin-settings.js', array(), BOOK_WP_VERSION, true );

        // Drop cap font previews — load all @font-face declarations so the
        // visual picker cards render each typeface in the Typography tab.
        wp_enqueue_style( 'book-wp-dc-camelot',     BOOK_WP_URI . '/assets/css/dropcap/camelot.css',     array(), BOOK_WP_VERSION );
        wp_enqueue_style( 'book-wp-dc-romantik',    BOOK_WP_URI . '/assets/css/dropcap/romantik.css',    array(), BOOK_WP_VERSION );
        wp_enqueue_style( 'book-wp-dc-liturgisch',  BOOK_WP_URI . '/assets/css/dropcap/liturgisch.css',  array(), BOOK_WP_VERSION );
        wp_enqueue_style( 'book-wp-dc-rediviva',    BOOK_WP_URI . '/assets/css/dropcap/rediviva.css',    array(), BOOK_WP_VERSION );
        wp_enqueue_style( 'book-wp-dc-gotische',    BOOK_WP_URI . '/assets/css/dropcap/gotische.css',    array(), BOOK_WP_VERSION );
        return;
    }
    if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        wp_enqueue_style( 'book-wp-admin', BOOK_WP_URI . '/assets/css/admin.css', array(), BOOK_WP_VERSION );
    }
}

/* ── Menu ──────────────────────────────────────────────────────────────── */

add_action( 'admin_menu', 'book_wp_menu' );

function book_wp_menu() {
    add_theme_page( 'Sasterawan Settings', 'Sasterawan', 'edit_theme_options', 'book-wp-settings', 'book_wp_settings_page' );
}

/* ── SVG icons ─────────────────────────────────────────────────────────── */

function bwp_icon( $name ) {
    $icons = array(
        'book'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
        'settings'=> '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
        'type'    => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg>',
        'feather' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="15"/></svg>',
        'eye'     => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
        'code'    => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
        'check'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>',
        'save'    => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>',
        'share'   => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>',
    );
    return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/* ── Settings page ─────────────────────────────────────────────────────── */

function book_wp_settings_page() {
    if ( ! current_user_can( 'edit_theme_options' ) ) return;

    $tabs = array(
        'general'    => array( 'label' => 'General',    'icon' => 'settings' ),
        'typography' => array( 'label' => 'Typography', 'icon' => 'type' ),
        'literary'   => array( 'label' => 'Literary',   'icon' => 'feather' ),
        'reading'    => array( 'label' => 'Reading',    'icon' => 'eye' ),
        'advanced'   => array( 'label' => 'Advanced',   'icon' => 'code'  ),
        'social'     => array( 'label' => 'Social',     'icon' => 'share' ),
    );

    $current = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
    if ( ! isset( $tabs[ $current ] ) ) $current = 'general';
    $updated = isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'];
    ?>
    <div class="theme-settings-root">

        <div class="bwp-header" role="banner">
            <div class="bwp-header__icon" aria-hidden="true">
                <?php echo bwp_icon( 'book' ); ?>
            </div>
            <div class="bwp-header__body">
                <h1 class="bwp-header__title">Sasterawan</h1>
                <p class="bwp-header__desc"><?php esc_html_e( 'Tema antologi sastera Melayu untuk menj.my', 'book-wp' ); ?></p>
            </div>
            <span class="bwp-header__badge">v<?php echo esc_html( BOOK_WP_VERSION ); ?></span>
        </div>

        <?php if ( $updated ) : ?>
            <div class="bwp-notice" role="status" aria-live="polite">
                <?php echo bwp_icon( 'check' ); ?>
                <?php esc_html_e( 'Settings saved.', 'book-wp' ); ?>
            </div>
        <?php endif; ?>

        <!-- Navigation uses plain link pattern — server-rendered tabs do not implement
     the ARIA tablist/tabpanel/tab widget; using role="tablist" without aria-controls
     and role="tabpanel" creates a broken ARIA pattern. (W-02 / SC 4.1.2) -->
<nav class="bwp-tabs" aria-label="<?php esc_attr_e( 'Settings tabs', 'book-wp' ); ?>">
            <?php foreach ( $tabs as $slug => $tab ) :
                $url   = admin_url( 'themes.php?page=book-wp-settings&tab=' . $slug );
                $active = ( $current === $slug );
            ?>
                <a href="<?php echo esc_url( $url ); ?>"
                   class="bwp-tab<?php echo esc_attr( $active ? ' bwp-tab--active' : '' ) ?>"
                   <?php if ( $active ) echo 'aria-current="page"'; ?>
                   aria-label="<?php echo esc_attr( $tab['label'] ); ?>">
                    <?php echo bwp_icon( $tab['icon'] ); ?>
                    <?php echo esc_html( $tab['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <form method="post"
              action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
              aria-label="<?php echo esc_attr( $tabs[ $current ]['label'] ); ?> settings">
            <input type="hidden" name="action"  value="book_wp_save_tab">
            <input type="hidden" name="bwp_tab" value="<?php echo esc_attr( $current ); ?>">
            <?php wp_nonce_field( 'book_wp_save_' . $current ); ?>

            <div class="bwp-card">
                <?php
                switch ( $current ) {
                    case 'general':    bwp_tab_general();    break;
                    case 'typography': bwp_tab_typography(); break;
                    case 'literary':   bwp_tab_literary();   break;
                    case 'reading':    bwp_tab_reading();    break;
                    case 'advanced':   bwp_tab_advanced();   break;
                    case 'social':     bwp_tab_social();     break;
                }
                ?>
            </div>

            <div class="bwp-form-footer">
                <span class="bwp-form-footer__credit">
                    Developed by <a href="https://github.com/menj" target="_blank" rel="noopener noreferrer">MENJ</a>
                    &middot; <a href="https://github.com/menj/book-wp" target="_blank" rel="noopener noreferrer">GitHub</a>
                </span>
                <button type="submit" class="bwp-btn-save">
                    <?php echo bwp_icon( 'save' ); ?>
                    <?php printf( esc_html__( 'Save %s', 'book-wp' ), esc_html( $tabs[ $current ]['label'] ) ); ?>
                </button>
            </div>
        </form>

    </div><!-- .theme-settings-root -->
    <?php
}

/* ══════════════════════════════════════
   HELPERS
   ══════════════════════════════════════ */

/**
 * Toggle switch row.
 * Uses input → label sibling relationship so CSS can style the track.
 */
function bwp_toggle( $name, $label, $desc = '', $default = '0' ) {
    $val = get_option( $name, $default );
    $id  = 'bwp_' . $name;
    ?>
    <div class="bwp-toggle-row">
        <input type="checkbox"
               id="<?php echo esc_attr( $id ); ?>"
               name="<?php echo esc_attr( $name ); ?>"
               value="1"
               <?php checked( $val, '1' ); ?>>
        <label for="<?php echo esc_attr( $id ); ?>" class="bwp-toggle-track" aria-hidden="true"></label>
        <div class="bwp-toggle-body">
            <label for="<?php echo esc_attr( $id ); ?>" class="bwp-toggle-name"><?php echo esc_html( $label ); ?></label>
            <?php if ( $desc ) : ?>
                <span class="bwp-toggle-desc"><?php echo wp_kses_post( $desc ); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/* ══════════════════════════════════════
   TAB 1 — General
   ══════════════════════════════════════ */

function bwp_tab_general() {
    $grp = get_option( 'book_wp_toc_grouping', 'none' );
    ?>
    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Collection', 'book-wp' ); ?></p>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_author_name"><?php esc_html_e( 'Author Name', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <input type="text" name="book_wp_author_name" id="book_wp_author_name"
                       value="<?php echo esc_attr( get_option( 'book_wp_author_name', '' ) ); ?>">
            </div>
            <span class="bwp-field__hint"><?php esc_html_e( 'Shown on the front-page cover, in the footer lockup and in structured data. The header carries the anthology name.', 'book-wp' ); ?></span>
        </div>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_author_portrait"><?php esc_html_e( 'Author Portrait', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <input type="url" name="book_wp_author_portrait" id="book_wp_author_portrait" placeholder="https://"
                       value="<?php echo esc_attr( get_option( 'book_wp_author_portrait', '' ) ); ?>">
                <p class="bwp-field__hint"><?php esc_html_e( 'Media Library URL of a head-and-shoulders photo. The theme renders it as a nila-and-kapas duotone medallion in the footer and on the author page; a plain photo on a light background works best.', 'book-wp' ); ?></p>
            </div>
        </div>

        <?php bwp_toggle( 'book_wp_show_hero_image', __( 'Front-page cover photograph', 'book-wp' ), __( 'Set the anthology title over a photograph on the front page. Off, the title sits on plain paper.', 'book-wp' ), '1' ); ?>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_hero_image"><?php esc_html_e( 'Cover Photograph', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <input type="url" name="book_wp_hero_image" id="book_wp_hero_image" placeholder="https://"
                       value="<?php echo esc_attr( get_option( 'book_wp_hero_image', '' ) ); ?>">
                <p class="bwp-field__hint"><?php esc_html_e( 'Media Library URL of a wide photograph (about 2.4:1, at least 2000px wide). Leave empty for the bundled underwater photograph by David Boca (Pexels).', 'book-wp' ); ?></p>
            </div>
        </div>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_anthology_name"><?php esc_html_e( 'Anthology Name', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <input type="text" name="book_wp_anthology_name" id="book_wp_anthology_name"
                       value="<?php echo esc_attr( get_option( 'book_wp_anthology_name', '' ) ); ?>">
            </div>
            <span class="bwp-field__hint"><?php esc_html_e( 'Displayed prominently on the front page as the collection title.', 'book-wp' ); ?></span>
        </div>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_jawi_name"><?php esc_html_e( 'Anthology Name in Jawi', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <input type="text" dir="rtl" lang="ms-Arab" name="book_wp_jawi_name" id="book_wp_jawi_name"
                       value="<?php echo esc_attr( get_option( 'book_wp_jawi_name', '' ) ); ?>">
            </div>
            <span class="bwp-field__hint"><?php esc_html_e( 'Shown beside the brandmark in the header, as the seal on title pages and in the footer.', 'book-wp' ); ?></span>
        </div>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_jawa_name"><?php esc_html_e( 'Anthology Name in Aksara Jawa', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <input type="text" lang="jv-Java" name="book_wp_jawa_name" id="book_wp_jawa_name"
                       placeholder="<?php echo esc_attr( book_wp_default_jawa_name() ); ?>"
                       value="<?php echo esc_attr( get_option( 'book_wp_jawa_name', '' ) ); ?>">
            </div>
            <span class="bwp-field__hint"><?php esc_html_e( 'A Javanese translation of the title in Javanese script, shown in the footer. Empty uses the bundled "Sangisoring Lumahan".', 'book-wp' ); ?></span>
        </div>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_featured_work_id"><?php esc_html_e( 'Featured Work', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <input type="number" min="0" name="book_wp_featured_work_id" id="book_wp_featured_work_id"
                       value="<?php echo esc_attr( get_option( 'book_wp_featured_work_id', 0 ) ); ?>">
                <p class="bwp-field__hint"><?php esc_html_e( 'Post ID to open the front page with. Leave 0 to show the newest work. The block can be hidden with the toggle below.', 'book-wp' ); ?></p>
            </div>
        </div>

        <?php bwp_toggle( 'book_wp_show_featured_work', __( 'Karya Terbaharu block', 'book-wp' ), __( 'Show the newest (or featured) work as an opening page above the table of contents.', 'book-wp' ), '1' ); ?>

        <div class="bwp-field bwp-field--full">
            <label class="bwp-field__label" for="book_wp_footer_quote"><?php esc_html_e( 'Footer Sign-off', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <input type="text" name="book_wp_footer_quote" id="book_wp_footer_quote"
                       value="<?php echo esc_attr( get_option( 'book_wp_footer_quote', book_wp_default_footer_quote() ) ); ?>">
                <p class="bwp-field__hint"><?php esc_html_e( 'One italic line that closes every page, set once in the Mukadimah voice Leave blank to hide the sign-off.', 'book-wp' ); ?></p>
            </div>
        </div>

        <div class="bwp-field bwp-field--full">
            <label class="bwp-field__label" for="book_wp_footer_quote_sub"><?php esc_html_e( 'Sign-off Second Line', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <input type="text" name="book_wp_footer_quote_sub" id="book_wp_footer_quote_sub"
                       value="<?php echo esc_attr( get_option( 'book_wp_footer_quote_sub', book_wp_default_footer_quote_sub() ) ); ?>">
                <p class="bwp-field__hint"><?php esc_html_e( 'Smaller supporting line beneath the sign-off. Optional.', 'book-wp' ); ?></p>
            </div>
        </div>

            <span class="bwp-field__hint"><?php echo wp_kses( __( 'Introductory text displayed above the Table of Contents. HTML is supported — e.g. <code>&lt;em&gt;, &lt;strong&gt;, &lt;a href=""&gt;</code>.', 'book-wp' ), array( 'code' => array() ) ); ?></span>
        </div>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_copyright"><?php esc_html_e( 'Copyright', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <textarea name="book_wp_copyright" id="book_wp_copyright" rows="2"><?php echo esc_textarea( get_option( 'book_wp_copyright', '' ) ); ?></textarea>
            </div>
            <span class="bwp-field__hint"><?php echo wp_kses( __( 'Displayed in the site footer. HTML is supported — e.g. <code>&amp;copy; 2026 &lt;strong&gt;My Anthology&lt;/strong&gt;</code>.', 'book-wp' ), array( 'code' => array() ) ); ?></span>
        </div>

        <div class="bwp-field">
            <?php
            $show_cr = get_option( 'book_wp_show_copyright', '1' );
            ?>
            <label class="bwp-field__label"><?php esc_html_e( 'Show Copyright', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <label class="bwp-toggle">
                    <input type="checkbox" name="book_wp_show_copyright" value="1" <?php checked( $show_cr, '1' ); ?>>
                    <span class="bwp-toggle-slider"></span>
                    <span class="bwp-toggle-desc"><?php esc_html_e( 'Show the copyright line in the site footer.', 'book-wp' ); ?></span>
                </label>
            </div>
        </div>
    </div>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Table of Contents', 'book-wp' ); ?></p>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_toc_grouping"><?php esc_html_e( 'Grouping', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <select name="book_wp_toc_grouping" id="book_wp_toc_grouping">
                    <option value="none"     <?php selected( $grp, 'none' ); ?>><?php esc_html_e( 'No grouping — single list', 'book-wp' ); ?></option>
                    <option value="form"     <?php selected( $grp, 'form' ); ?>><?php esc_html_e( 'By Literary Form', 'book-wp' ); ?></option>
                    <option value="category" <?php selected( $grp, 'category' ); ?>><?php esc_html_e( 'By Category', 'book-wp' ); ?></option>
                    <option value="tag"      <?php selected( $grp, 'tag' ); ?>><?php esc_html_e( 'By Tag', 'book-wp' ); ?></option>
                </select>
            </div>
        </div>

        <?php bwp_toggle( 'book_wp_show_badges', __( 'Literary Form Badges', 'book-wp' ), __( 'Show coloured form badges beside titles in the TOC.', 'book-wp' ) ); ?>

        <?php
        $toc_layout = get_option( 'book_wp_toc_layout',     'list' );
        $toc_paging = get_option( 'book_wp_toc_pagination', 'paged' );
        ?>
        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_toc_layout"><?php esc_html_e( 'TOC Layout', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <select name="book_wp_toc_layout" id="book_wp_toc_layout">
                    <option value="list"    <?php selected( $toc_layout, 'list' ); ?>><?php esc_html_e( 'List',    'book-wp' ); ?></option>
                    <option value="grid"    <?php selected( $toc_layout, 'grid' ); ?>><?php esc_html_e( 'Grid',    'book-wp' ); ?></option>
                    <option value="masonry" <?php selected( $toc_layout, 'masonry' ); ?>><?php esc_html_e( 'Masonry', 'book-wp' ); ?></option>
                </select>
                <span class="bwp-field__hint"><?php esc_html_e( 'Applies only when Grouping is set to None. List: single column. Grid: equal-width CSS columns. Masonry: cascading columns.', 'book-wp' ); ?></span>
            </div>
        </div>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_toc_pagination"><?php esc_html_e( 'TOC Pagination', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <select name="book_wp_toc_pagination" id="book_wp_toc_pagination">
                    <option value="paged"    <?php selected( $toc_paging, 'paged' ); ?>><?php esc_html_e( 'Page links',      'book-wp' ); ?></option>
                    <option value="infinite" <?php selected( $toc_paging, 'infinite' ); ?>><?php esc_html_e( 'Infinite scroll', 'book-wp' ); ?></option>
                </select>
                <span class="bwp-field__hint"><?php esc_html_e( 'Applies only when Grouping is set to None. Infinite scroll loads the next page automatically as the reader scrolls. Uses WordPress reading settings for posts per page.', 'book-wp' ); ?></span>
            </div>
        </div>
    </div>

    <div class="bwp-section">
        <div class="bwp-info">
            <p><?php echo wp_kses( __( '<strong>Site Title vs Author Name</strong> — Set your WordPress <em>Site Title</em> (Settings → General) to the <strong>anthology name</strong> — it controls the browser tab, RSS feed, and SEO title. The Author Name above appears in the masthead on every page.', 'book-wp' ), array( 'strong' => array(), 'em' => array() ) ); ?></p>
            <p><?php echo wp_kses( __( '<strong>Front-end language</strong> follows your WordPress Site Language setting (Settings → General → Site Language). The theme ships with Bahasa Melayu, English, Spanish, and French translations.', 'book-wp' ), array( 'strong' => array() ) ); ?></p>
            <p><?php echo wp_kses( __( '<strong>TOC grouping</strong> — Group by Literary Form to organise puisi, cerpen, esei etc. into separate sections. Group by Category to follow your WordPress category structure. Group by Tag to organise by thematic keywords — a post appears under every tag it carries. Grouping headings use the category, form, or tag name with a work count badge.', 'book-wp' ), array( 'strong' => array() ) ); ?></p>
        </div>
    </div>

    <?php
    /* ── Archive section ───────────────────────────── */
    $arch_layout = get_option( 'book_wp_archive_layout',     'list' );
    $arch_paging = get_option( 'book_wp_archive_pagination', 'paged' );
    ?>
    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Archive Pages', 'book-wp' ); ?></p>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_archive_layout"><?php esc_html_e( 'Layout', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <select name="book_wp_archive_layout" id="book_wp_archive_layout">
                    <option value="list"    <?php selected( $arch_layout, 'list' ); ?>><?php esc_html_e( 'List',    'book-wp' ); ?></option>
                    <option value="grid"    <?php selected( $arch_layout, 'grid' ); ?>><?php esc_html_e( 'Grid',    'book-wp' ); ?></option>
                    <option value="masonry" <?php selected( $arch_layout, 'masonry' ); ?>><?php esc_html_e( 'Masonry', 'book-wp' ); ?></option>
                </select>
                <span class="bwp-field__hint"><?php esc_html_e( 'List: single-column. Grid: equal-width CSS columns. Masonry: cascading columns — best when cards have varying heights (e.g. with featured images).', 'book-wp' ); ?></span>
            </div>
        </div>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_archive_pagination"><?php esc_html_e( 'Pagination', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <select name="book_wp_archive_pagination" id="book_wp_archive_pagination">
                    <option value="paged"    <?php selected( $arch_paging, 'paged' ); ?>><?php esc_html_e( 'Page links', 'book-wp' ); ?></option>
                    <option value="infinite" <?php selected( $arch_paging, 'infinite' ); ?>><?php esc_html_e( 'Infinite scroll', 'book-wp' ); ?></option>
                </select>
                <span class="bwp-field__hint"><?php esc_html_e( 'Page links: standard WordPress previous / next. Infinite scroll: next page loads automatically as the reader scrolls down — applies to literary form, category, and tag archives.', 'book-wp' ); ?></span>
            </div>
        </div>
    </div>
    <?php
}

/* ══════════════════════════════════════
   TAB 2 — Typography
   ══════════════════════════════════════ */

function bwp_tab_typography() {
    $font       = get_option( 'book_wp_font',         'georgia' );
    $scheme     = get_option( 'book_wp_color_scheme',  'default' );
    $time_theme = get_option( 'book_wp_time_theme',    'auto' );
    $align      = get_option( 'book_wp_poetry_align',  'left' );

    $fonts = array(
        'georgia' => array( 'name' => 'Georgia', 'sub' => 'Default', 'sample' => 'Gg' ),
        'lora'    => array( 'name' => 'Lora',    'sub' => 'Serif',   'sample' => 'Gg' ),
        'vollkorn'=> array( 'name' => 'Vollkorn','sub' => 'Serif',   'sample' => 'Gg' ),
        'arvo'    => array( 'name' => 'Arvo',    'sub' => 'Slab',    'sample' => 'Gg' ),
        'amiri'   => array( 'name' => 'Amiri',   'sub' => 'Arabic',  'sample' => 'Gg' ),
        'maven'   => array( 'name' => 'Maven',   'sub' => 'Sans',    'sample' => 'Gg' ),
        'ubuntu'  => array( 'name' => 'Ubuntu',  'sub' => 'Sans',    'sample' => 'Gg' ),
    );

    $schemes = array(
        'default' => array( 'name' => 'Kapas',   'sub' => 'Nila on cotton', 'bg' => '#f6f1e4', 'fg' => '#1a1f3a' ),
        'cream'   => array( 'name' => 'Cream',   'sub' => 'Aged paper', 'bg' => '#faf5dc', 'fg' => '#332d26' ),
        'dusk'    => array( 'name' => 'Dusk',    'sub' => 'Warm grey',  'bg' => '#e8e4de', 'fg' => '#2c2825' ),
        'night'   => array( 'name' => 'Malam',   'sub' => 'Indigo night', 'bg' => '#0f1430', 'fg' => '#ece6d6' ),
    );

    $times = array(
        'auto'      => array( 'name' => 'Auto',      'sub' => 'Visitor clock',  'glyph' => '🕐' ),
        'dawn'      => array( 'name' => 'Dawn',      'sub' => '04:00–06:59',    'glyph' => '🌅' ),
        'morning'   => array( 'name' => 'Morning',   'sub' => '07:00–11:59',    'glyph' => '🌤' ),
        'afternoon' => array( 'name' => 'Afternoon', 'sub' => '12:00–16:59',    'glyph' => '☀️' ),
        'dusk'      => array( 'name' => 'Dusk',      'sub' => '17:00–20:59',    'glyph' => '🌆' ),
        'night'     => array( 'name' => 'Night',     'sub' => '21:00–03:59',    'glyph' => '🌙' ),
    );
    ?>
    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Font Family', 'book-wp' ); ?></p>
        <div class="bwp-vs-grid">
            <?php foreach ( $fonts as $val => $f ) : ?>
                <label class="bwp-vs-item">
                    <input type="radio" name="book_wp_font" value="<?php echo esc_attr( $val ); ?>" <?php checked( $font, $val ); ?>>
                    <span class="bwp-vs-card">
                        <span class="bwp-vs-preview" style="font-family:<?php echo esc_attr( $f['name'] ); ?>,Georgia,serif; font-size:18px; color:#333;"><?php echo esc_html( $f['sample'] ); ?></span>
                        <span class="bwp-vs-name"><?php echo esc_html( $f['name'] ); ?></span>
                        <span class="bwp-vs-sub"><?php echo esc_html( $f['sub'] ); ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
        <?php bwp_toggle( 'book_wp_remote_fonts', __( 'Load Google Fonts', 'book-wp' ), __( 'Loads Cinzel, EB Garamond, Jost, and selected optional fonts from Google Fonts. Disable for a no-remote-fonts privacy mode; browser/system fallbacks will be used instead.', 'book-wp' ), '1' ); ?>
    </div>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Reading Palette', 'book-wp' ); ?></p>
        <p class="description" style="margin:.2rem 0 1.25rem; font-size:.82rem; color:var(--theme-text-muted); line-height:1.5;"><?php echo wp_kses( __( 'These two controls work together — choose both independently and they combine. <strong>Colour Scheme</strong> sets the paper: background, surface, and border tones. <strong>Time of Day</strong> sets the ink: text colour, link colour, and accent mood. Your chosen paper holds across all time periods; the ink shifts with the light.', 'book-wp' ), array( 'strong' => array() ) ); ?></p>

        <p class="bwp-section__sublabel" style="font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.07em; color:var(--theme-text-muted); margin:0 0 .55rem;"><?php esc_html_e( 'Paper — Colour Scheme', 'book-wp' ); ?></p>
        <div class="bwp-vs-grid">
            <?php foreach ( $schemes as $val => $s ) : ?>
                <label class="bwp-vs-item">
                    <input type="radio" name="book_wp_color_scheme" value="<?php echo esc_attr( $val ); ?>" <?php checked( $scheme, $val ); ?>>
                    <span class="bwp-vs-card">
                        <span class="bwp-swatch" style="background:<?php echo esc_attr( $s['bg'] ); ?>; border-color:rgba(0,0,0,0.1);">
                            <span style="display:block;width:50%;height:100%;background:<?php echo esc_attr( $s['fg'] ); ?>; border-radius:1px 0 0 1px;"></span>
                        </span>
                        <span class="bwp-vs-name"><?php echo esc_html( $s['name'] ); ?></span>
                        <span class="bwp-vs-sub"><?php echo esc_html( $s['sub'] ); ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>

        <p class="bwp-section__sublabel" style="font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.07em; color:var(--theme-text-muted); margin:1.4rem 0 .55rem;"><?php esc_html_e( 'Ink — Time of Day', 'book-wp' ); ?></p>
        <div class="bwp-vs-grid bwp-vs-grid--wide">
            <?php foreach ( $times as $val => $t ) : ?>
                <label class="bwp-vs-item">
                    <input type="radio" name="book_wp_time_theme" value="<?php echo esc_attr( $val ); ?>" <?php checked( $time_theme, $val ); ?>>
                    <span class="bwp-vs-card">
                        <span class="bwp-vs-preview" style="font-size:20px;"><?php echo esc_html( $t['glyph'] ); ?></span>
                        <span class="bwp-vs-name"><?php echo esc_html( $t['name'] ); ?></span>
                        <span class="bwp-vs-sub"><?php echo esc_html( $t['sub'] ); ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
        <p style="margin:.625rem 0 0; font-size:.72rem; color:var(--theme-text-muted);"><?php esc_html_e( 'Auto detects each visitor\'s local time and shifts the ink mood accordingly. Pin to a specific period to lock it for all visitors.', 'book-wp' ); ?></p>
    </div>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Text Options', 'book-wp' ); ?></p>

        <div class="bwp-field">
            <label class="bwp-field__label"><?php esc_html_e( 'Drop Cap — Per Form', 'book-wp' ); ?></label>
            <span class="description" style="display:block; margin-bottom:.6rem;"><?php esc_html_e( 'Apply a decorative large initial letter to the first paragraph. Enable per prose form. Poetry forms are never affected.', 'book-wp' ); ?></span>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem 1.5rem;">
                <?php
                $drop_cap_forms = array(
                    'cerpen'  => array( 'label' => 'Cerpen',  'default' => '1' ),
                    'novel'   => array( 'label' => 'Novel',   'default' => '1' ),
                    'hikayat' => array( 'label' => 'Hikayat', 'default' => '1' ),
                    'memoir'  => array( 'label' => 'Memoir',  'default' => '1' ),
                    'esei'    => array( 'label' => 'Esei',    'default' => '0' ),
                    'nukilan' => array( 'label' => 'Nukilan', 'default' => '0' ),
                );
                foreach ( $drop_cap_forms as $slug => $f ) :
                    $key = 'book_wp_drop_cap_' . $slug;
                    $val = get_option( $key, $f['default'] );
                ?>
                <label style="display:flex; align-items:center; gap:.4rem; font-weight:400; cursor:pointer;">
                    <input type="checkbox" name="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $val, '1' ); ?>>
                    <?php echo esc_html( $f['label'] ); ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bwp-field" style="margin-top:1.25rem;">
            <label class="bwp-field__label"><?php esc_html_e( 'Drop Cap Font', 'book-wp' ); ?></label>
            <span class="description" style="display:block; margin-bottom:.6rem;"><?php esc_html_e( 'Choose a decorative typeface for the drop cap initial letter. "Default" uses each form\'s built-in font (Cinzel, IM Fell English SC, etc.).', 'book-wp' ); ?></span>
            <?php
            $dc_font = get_option( 'book_wp_dropcap_font', 'default' );
            $dc_fonts = array(
                'default'     => array( 'name' => 'Default',       'sub' => 'Per-form',       'sample' => 'A',  'family' => "'Cinzel', Georgia, serif" ),
                'camelot'     => array( 'name' => 'Camelot',       'sub' => 'Decorative',     'sample' => 'A',  'family' => "'Camelot', Georgia, serif" ),
                'romantik'    => array( 'name' => 'Romantik',      'sub' => 'Calligraphic',   'sample' => 'A',  'family' => "'Romantik', Georgia, serif" ),
                'liturgisch'  => array( 'name' => 'Liturgisch',    'sub' => 'Ecclesiastical', 'sample' => 'A',  'family' => "'Liturgisch', Georgia, serif" ),
                'rediviva'    => array( 'name' => 'Rediviva',      'sub' => 'Ornamental',     'sample' => 'A',  'family' => "'Rediviva', Georgia, serif" ),
                'gotische'    => array( 'name' => 'Gotische',      'sub' => 'Gothic',         'sample' => 'A',  'family' => "'Gotische', Georgia, serif" ),
            );
            ?>
            <div class="bwp-vs-grid">
                <?php foreach ( $dc_fonts as $val => $f ) : ?>
                    <label class="bwp-vs-item">
                        <input type="radio" name="book_wp_dropcap_font" value="<?php echo esc_attr( $val ); ?>" <?php checked( $dc_font, $val ); ?>>
                        <span class="bwp-vs-card">
                            <span class="bwp-vs-preview bwp-dc-preview" style="font-family:<?php echo esc_attr( $f['family'] ); ?>; font-size:32px; line-height:1;"><?php echo esc_html( $f['sample'] ); ?></span>
                            <span class="bwp-vs-name"><?php echo esc_html( $f['name'] ); ?></span>
                            <span class="bwp-vs-sub"><?php echo esc_html( $f['sub'] ); ?></span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bwp-field" style="margin-top:1rem;">
            <label class="bwp-field__label" for="book_wp_poetry_align"><?php esc_html_e( 'Poetry Alignment', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <select name="book_wp_poetry_align" id="book_wp_poetry_align">
                    <option value="left"   <?php selected( $align, 'left' ); ?>><?php esc_html_e( 'Left-aligned', 'book-wp' ); ?></option>
                    <option value="center" <?php selected( $align, 'center' ); ?>><?php esc_html_e( 'Centred', 'book-wp' ); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Poetry Options', 'book-wp' ); ?></p>
        <span class="description" style="display:block; margin-bottom:1rem;"><?php esc_html_e( 'Controls for all verse forms: puisi, syair, karmina, ratapan. Poetry is always ragged-right and never hyphenated regardless of these settings.', 'book-wp' ); ?></span>

        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_stanza_spacing"><?php esc_html_e( 'Stanza Spacing', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <select name="book_wp_stanza_spacing" id="book_wp_stanza_spacing">
                    <option value="tight"       <?php selected( get_option( 'book_wp_stanza_spacing', 'comfortable' ), 'tight' ); ?>><?php esc_html_e( 'Tight — compact, stanzas close together', 'book-wp' ); ?></option>
                    <option value="comfortable" <?php selected( get_option( 'book_wp_stanza_spacing', 'comfortable' ), 'comfortable' ); ?>><?php esc_html_e( 'Comfortable — balanced breath between stanzas', 'book-wp' ); ?></option>
                    <option value="spacious"    <?php selected( get_option( 'book_wp_stanza_spacing', 'comfortable' ), 'spacious' ); ?>><?php esc_html_e( 'Spacious — generous pause, meditative pace', 'book-wp' ); ?></option>
                </select>
            </div>
            <span class="bwp-field__hint"><?php esc_html_e( 'The gap between stanzas is where a poem breathes. Default: Comfortable.', 'book-wp' ); ?></span>
        </div>

        <div class="bwp-field" style="margin-top:1rem;">
            <label class="bwp-field__label" for="book_wp_poetry_line_height"><?php esc_html_e( 'Verse Line Height', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <select name="book_wp_poetry_line_height" id="book_wp_poetry_line_height">
                    <option value="tight"  <?php selected( get_option( 'book_wp_poetry_line_height', 'normal' ), 'tight' ); ?>><?php esc_html_e( 'Tight — 1.55, tightly-wound stanza', 'book-wp' ); ?></option>
                    <option value="normal" <?php selected( get_option( 'book_wp_poetry_line_height', 'normal' ), 'normal' ); ?>><?php esc_html_e( 'Normal — 1.65, recommended for most verse', 'book-wp' ); ?></option>
                    <option value="loose"  <?php selected( get_option( 'book_wp_poetry_line_height', 'normal' ), 'loose' ); ?>><?php esc_html_e( 'Loose — 1.80, open and airy', 'book-wp' ); ?></option>
                </select>
            </div>
            <span class="bwp-field__hint"><?php esc_html_e( 'Independent from prose line height. Verse lines are shorter — they need less leading. Default: Normal (1.65).', 'book-wp' ); ?></span>
        </div>
    </div>
    <?php
}

/* ══════════════════════════════════════
   TAB 3 — Literary
   ══════════════════════════════════════ */

function bwp_tab_literary() {
    $orn = get_option( 'book_wp_ornament_style', 'fleuron' );

    // Ornament SVG glyphs (scaled down for preview)
    $orn_glyphs = array(
        'fleuron'  => '<svg viewBox="0 0 64 24"><path d="M32 4C24 4 16 8 16 12c0 4 8 8 16 8s16-4 16-8c0-4-8-8-16-8zm0 4c6 0 12 2 12 4s-6 4-12 4-12-2-12-4 6-4 12-4zM8 12s4-2 8 0M56 12s-4-2-8 0" fill="none" stroke-width="1.2"/></svg>',
        'asterism' => '<svg viewBox="0 0 80 24"><text x="4" y="19" font-size="18" font-family="serif">*</text><text x="30" y="19" font-size="18" font-family="serif">*</text><text x="56" y="19" font-size="18" font-family="serif">*</text></svg>',
        'dinkus'   => '<svg viewBox="0 0 80 24"><circle cx="16" cy="12" r="2.5"/><circle cx="40" cy="12" r="2.5"/><circle cx="64" cy="12" r="2.5"/></svg>',
        'dash'     => '<svg viewBox="0 0 64 24"><line x1="8" y1="12" x2="56" y2="12" stroke-width="1" fill="none"/></svg>',
        'leaf'     => '<svg viewBox="0 0 48 24"><path d="M24 4C12 4 6 10 8 18c6-4 12-5 16 2 4-7 10-6 16-2 2-8-4-14-16-14z"/><line x1="24" y1="20" x2="24" y2="4" stroke-width=".8" fill="none" opacity=".4"/></svg>',
        'flower'   => '<svg viewBox="0 0 48 24"><circle cx="24" cy="12" r="3"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(0 24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(60 24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(120 24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(180 24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(240 24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(300 24 12)"/></svg>',
        'diamond'  => '<svg viewBox="0 0 80 24"><polygon points="24,4 30,12 24,20 18,12"/><line x1="32" y1="12" x2="48" y2="12" stroke-width="1" fill="none" opacity=".4"/><line x1="0" y1="12" x2="16" y2="12" stroke-width="1" fill="none" opacity=".4"/><polygon points="56,4 62,12 56,20 50,12"/></svg>',
        'none'     => '<svg viewBox="0 0 40 24"><line x1="8" y1="8" x2="32" y2="16" stroke-width="1.5" opacity=".2"/><line x1="32" y1="8" x2="8" y2="16" stroke-width="1.5" opacity=".2"/></svg>',
    );
    $orn_names = array(
        'fleuron' => 'Fleuron', 'asterism' => 'Asterism', 'dinkus' => 'Dinkus',
        'dash'    => 'Dash',    'leaf'     => 'Leaf',     'flower' => 'Flower',
        'diamond' => 'Diamond', 'none'     => 'None',
    );
    ?>
    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Section Ornament', 'book-wp' ); ?></p>
        <p style="font-size:.75rem; color:var(--theme-text-muted); margin:0 0 .75rem;"><?php echo wp_kses( __( 'Used in the TOC between groups, at the end of works, in the colophon, and by the <code>[divider]</code> shortcode. Can be overridden per-work in the Literary Work Details meta box.', 'book-wp' ), array( 'code' => array() ) ); ?></p>
        <div class="bwp-vs-grid">
            <?php foreach ( $orn_glyphs as $val => $glyph ) : ?>
                <label class="bwp-vs-item">
                    <input type="radio" name="book_wp_ornament_style" value="<?php echo esc_attr( $val ); ?>" <?php checked( $orn, $val ); ?>>
                    <span class="bwp-vs-card">
                        <span class="bwp-vs-preview"><?php echo $glyph; // phpcs:ignore — trusted SVG ?></span>
                        <span class="bwp-vs-name"><?php echo esc_html( $orn_names[ $val ] ); ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Content Display', 'book-wp' ); ?></p>
        <?php bwp_toggle( 'book_wp_show_featured',  __( 'Featured Image', 'book-wp' ), __( 'Display the post thumbnail on single work pages.', 'book-wp' ), '1' ); ?>
        <?php bwp_toggle( 'book_wp_related_works',  __( 'Related Works', 'book-wp' ), __( 'Show a related works section at the bottom of each work.', 'book-wp' ), '1' ); ?>
        <?php bwp_toggle( 'book_wp_stanza_numbers', __( 'Stanza Numbers', 'book-wp' ), __( 'Add sequential numbering to stanzas in poetry works.', 'book-wp' ), '0' ); ?>
        <?php bwp_toggle( 'book_wp_show_analytics', __( 'Reading Analytics', 'book-wp' ), __( 'Display reading time, word count, line count.', 'book-wp' ), '1' ); ?>
        <?php bwp_toggle( 'book_wp_show_tags',      __( 'Tags', 'book-wp' ),            __( 'Show post tags on single work pages, between the analytics bar and colophon.', 'book-wp' ), '1' ); ?>
    </div>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Tag Settings', 'book-wp' ); ?></p>

        <?php
        // ── Hidden Tags multi-select ─────────────────────────────────────
        $all_tags  = get_tags( array( 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ) );
        // Stored as comma-separated term IDs since v3.4.54.
        $saved_ids = array_values( array_filter( array_map( 'absint', explode( ',', get_option( 'book_wp_hidden_tags', '' ) ) ) ) );
        ?>

        <div class="bwp-field">
            <label class="bwp-field__label" for="bwp-hidden-tags-input">
                <?php esc_html_e( 'Hidden Post Tags', 'book-wp' ); ?>
            </label>
            <div class="bwp-field__control">
                <?php if ( empty( $all_tags ) ) : ?>
                    <p style="font-size:.8rem; color:var(--theme-text-muted); margin:0;"><?php esc_html_e( 'No tags found. Create tags on your posts first.', 'book-wp' ); ?></p>
                <?php else : ?>
                    <div class="bwp-tag-select" id="bwp-hidden-tags-widget">

                        <!-- Pill box + search input -->
                        <!-- W-07 / SC 4.1.2: combobox ARIA added.
                             role="combobox" + aria-expanded lets AT announce open/closed state.
                             aria-controls associates the input with the listbox.
                             aria-activedescendant is managed by JS during keyboard nav. -->
                        <div class="bwp-tag-select__box">
                            <input type="text"
                                   class="bwp-tag-select__input"
                                   id="bwp-hidden-tags-input"
                                   placeholder="<?php esc_attr_e( 'Select tags…', 'book-wp' ); ?>"
                                   autocomplete="off"
                                   role="combobox"
                                   aria-expanded="false"
                                   aria-haspopup="listbox"
                                   aria-controls="bwp-hidden-tags-dropdown"
                                   aria-activedescendant=""
                                   aria-label="<?php esc_attr_e( 'Search and select hidden tags', 'book-wp' ); ?>">
                        </div>

                        <!-- Dropdown option list -->
                        <div class="bwp-tag-select__dropdown" id="bwp-hidden-tags-dropdown" role="listbox" aria-multiselectable="true">
                            <?php foreach ( $all_tags as $tag ) : ?>
                                <!-- id allows aria-activedescendant to reference the focused option. (W-07) -->
                                <!-- data-id stores the term_id used for saving; data-slug retained for ARIA id reference. -->
                                <div class="bwp-tag-select__option<?php echo in_array( $tag->term_id, $saved_ids, true ) ? ' is-selected' : ''; ?>"
                                     id="bwp-tag-opt-<?php echo esc_attr( $tag->slug ); ?>"
                                     role="option"
                                     data-id="<?php echo (int) $tag->term_id; ?>"
                                     data-slug="<?php echo esc_attr( $tag->slug ); ?>"
                                     data-label="<?php echo esc_attr( $tag->name ); ?>"
                                     aria-selected="<?php echo in_array( $tag->term_id, $saved_ids, true ) ? 'true' : 'false'; ?>">
                                    <?php echo esc_html( $tag->name ); ?>
                                    <?php if ( $tag->count ) : ?>
                                        <span style="margin-left:auto; font-size:.7rem; color:var(--theme-text-muted);"><?php echo (int) $tag->count; ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Hidden input that stores the saved value for form submission -->
                        <input type="hidden"
                               name="book_wp_hidden_tags"
                               class="bwp-tag-select__value"
                               value="<?php echo esc_attr( get_option( 'book_wp_hidden_tags', '' ) ); ?>">
                    </div>
                <?php endif; ?>
                <span class="bwp-field__hint">
                    <?php esc_html_e( 'Posts tagged with any of these tags will be hidden from the TOC, category archives, and search results. They remain accessible via their URL and tag archive. Tag display and the tag cap do not apply to these tags.', 'book-wp' ); ?>
                </span>
            </div>
        </div>

    <div class="bwp-section">
        <div class="bwp-info">
            <div>
                <p><strong><?php esc_html_e( 'Available Shortcodes', 'book-wp' ); ?></strong></p>
                <p><?php echo wp_kses( __( '<code>[quote source="…"]…[/quote]</code> — Styled literary pull-quote with optional attribution', 'book-wp' ), array( 'code' => array() ) ); ?></p>
                <p><?php echo wp_kses( __( '<code>[divider style="fleuron"]</code> — Decorative section divider (uses global ornament if no style given)', 'book-wp' ), array( 'code' => array() ) ); ?></p>
                <p><?php echo wp_kses( __( '<code>[verse num="1"]…[/verse]</code> — Numbered verse block; number outdents into the left margin', 'book-wp' ), array( 'code' => array() ) ); ?></p>
                <p><?php echo wp_kses( __( '<code>[term definition="…"]word[/term]</code> — Inline glossary tooltip; hover or focus to reveal', 'book-wp' ), array( 'code' => array() ) ); ?></p>
                <p style="margin-top:.6rem; color:var(--theme-text-muted);"><?php echo wp_kses( __( 'In the <strong>block editor</strong>, use the dedicated <em>Literary Quote</em>, <em>Literary Divider</em>, and <em>Verse Block</em> blocks. For term definitions, select a word and click the <strong>Term Definition</strong> button (book icon) in the formatting toolbar — no shortcode block needed.', 'book-wp' ), array( 'strong' => array(), 'em' => array() ) ); ?></p>
                <p style="margin-top:.3rem; color:var(--theme-text-muted);"><?php echo wp_kses( __( 'Per-work overrides for ornament and drop cap are set in the <strong>Literary Work Details</strong> meta box on each post\'s edit screen.', 'book-wp' ), array( 'strong' => array() ) ); ?></p>
            </div>
        </div>
    </div>
    <?php
}

/* ══════════════════════════════════════
   TAB 4 — Reading
   ══════════════════════════════════════ */

function bwp_tab_reading() {
    $comments = get_option( 'book_wp_comments', 'show' );
    ?>
    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Reading Enhancements', 'book-wp' ); ?></p>
        <?php bwp_toggle( 'book_wp_progress_bar',   __( 'Reading Progress Bar', 'book-wp' ), __( 'A thin line at the top of the viewport tracks scroll position. Helpful for longer prose works and poetry sequences.', 'book-wp' ), '1' ); ?>
        <?php bwp_toggle( 'book_wp_keyboard_nav',   __( 'Keyboard Navigation', 'book-wp' ), __( 'Arrow keys ← → move between works in the anthology. Works in sequence order as shown in the TOC.', 'book-wp' ), '1' ); ?>
        <?php bwp_toggle( 'book_wp_reading_memory', __( 'Reading Position Memory', 'book-wp' ), __( 'Restores scroll position when a reader returns to a work they were part-way through. Uses localStorage — no server storage.', 'book-wp' ), '1' ); ?>
        <?php bwp_toggle( 'book_wp_night_toggle',   __( 'Night Mode Toggle', 'book-wp' ), __( 'A floating button lets readers switch between the current colour scheme and the Night scheme. Position: bottom-right corner.', 'book-wp' ), '1' ); ?>
        <?php bwp_toggle( 'book_wp_share_row',      __( 'Share Row', 'book-wp' ),         __( 'Display a compact share strip below the tags on single posts: Copy link, X (Twitter), Facebook, WhatsApp. No third-party scripts loaded.', 'book-wp' ), '0' ); ?>
        <?php bwp_toggle( 'book_wp_back_to_top',    __( 'Back to Top Button', 'book-wp' ),  __( 'Show a fixed button in the bottom-right corner that smoothly scrolls back to the top of the page.', 'book-wp' ), '1' ); ?>
    </div>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Interaction', 'book-wp' ); ?></p>
        <div class="bwp-field">
            <label class="bwp-field__label" for="book_wp_comments"><?php esc_html_e( 'Comments', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <select name="book_wp_comments" id="book_wp_comments">
                    <option value="show" <?php selected( $comments, 'show' ); ?>><?php esc_html_e( 'Show comments', 'book-wp' ); ?></option>
                    <option value="hide" <?php selected( $comments, 'hide' ); ?>><?php esc_html_e( 'Hide comments', 'book-wp' ); ?></option>
                </select>
            </div>
        </div>
    </div>
    <?php
}

/* ══════════════════════════════════════
   TAB 5 — Advanced
   ══════════════════════════════════════ */

function bwp_tab_advanced() {
    ?>
    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'SEO', 'book-wp' ); ?></p>
        <?php bwp_toggle( 'book_wp_structured_data', __( 'Structured Data (JSON-LD)', 'book-wp' ), __( "Outputs Schema.org CreativeWork markup in the page head. Helps search engines understand each work's type, author, and date. Disable if you use a dedicated SEO plugin that handles this.", 'book-wp' ), '1' ); ?>

        <?php
        $seo_active = book_wp_seo_plugin_active();
        $seo_notice = '';
        if ( defined( 'RANK_MATH_VERSION' ) )            $seo_notice = 'RankMath';
        elseif ( defined( 'WPSEO_VERSION' ) )            $seo_notice = 'Yoast SEO';
        elseif ( defined( 'AIOSEO_VERSION' ) )           $seo_notice = 'All in One SEO';
        elseif ( defined( 'SEOPRESS_VERSION' ) )         $seo_notice = 'SEOPress';
        elseif ( class_exists( 'The_SEO_Framework\Load' ) ) $seo_notice = 'The SEO Framework';
        ?>

        <?php if ( $seo_active ) : ?>
        <div class="bwp-toggle-row bwp-toggle-row--disabled">
            <span class="bwp-toggle-track bwp-toggle-track--off" aria-hidden="true"></span>
            <div class="bwp-toggle-body">
                <span class="bwp-toggle-name"><?php esc_html_e( 'Social Meta Tags (OpenGraph / Twitter Card)', 'book-wp' ); ?></span>
                <span class="bwp-toggle-desc">
                    <?php printf(
                        /* translators: %s: SEO plugin name */
                        esc_html__( '%s is active — social meta tags are handled by that plugin. This toggle has no effect.', 'book-wp' ),
                        '<strong>' . esc_html( $seo_notice ) . '</strong>'
                    ); ?>
                </span>
            </div>
            <input type="hidden" name="book_wp_social_meta_tags" value="0">
        </div>
        <?php else : ?>
        <?php bwp_toggle( 'book_wp_social_meta_tags', __( 'Social Meta Tags (OpenGraph / Twitter Card)', 'book-wp' ), __( 'Outputs og:title, og:description, og:image, twitter:card, and related tags on single work pages. Controls share card appearance on X (Twitter), Facebook, LinkedIn, WhatsApp, and Telegram. Automatically disabled when RankMath, Yoast, or another SEO plugin is detected.', 'book-wp' ), '1' ); ?>
        <?php endif; ?>

        <?php bwp_toggle( 'book_wp_json_feed', __( 'JSON Feed v1.1', 'book-wp' ), __( 'Publishes a machine-readable feed at /feed/json — consumed by feed readers that support the JSON Feed spec (Reeder, NetNewsWire, etc.). Includes content, summary, literary form, word count, and reading time per item. Adds an autodiscovery <link> tag to the front-page <head>.', 'book-wp' ), '1' ); ?>

        <?php if ( '1' === get_option( 'book_wp_json_feed', '1' ) ) : ?>
        <p class="bwp-field__hint" style="margin:.25rem 0 0 1.5rem;">
            <?php esc_html_e( 'Feed URL:', 'book-wp' ); ?>
            <a href="<?php echo esc_url( get_feed_link( 'json' ) ); ?>" target="_blank" rel="noopener noreferrer">
                <?php echo esc_html( get_feed_link( 'json' ) ); ?>
            </a>
        </p>
        <?php endif; ?>
    </div>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Permalinks', 'book-wp' ); ?></p>

        <?php
        $form_permalinks_on = '1' === get_option( 'book_wp_form_permalinks', '0' );
        $form_slugs         = book_wp_permalink_form_slugs();
        $example_slug       = ! empty( $form_slugs ) ? $form_slugs[0] : 'puisi';
        $example_url        = trailingslashit( home_url( '/' . $example_slug . '/post-slug' ) );
        ?>

        <?php bwp_toggle( 'book_wp_form_permalinks', __( 'Literary Form Permalinks', 'book-wp' ), sprintf(
            /* translators: %s: example URL */
            __( 'Structures post URLs as /{literary-form}/{post-slug}/. Example: <code>%s</code>. Posts with no literary form assigned retain their existing permalink. Taxonomy archives are not affected — they remain at /form/{slug}/.', 'book-wp' ),
            esc_html( $example_url )
        ), '0' ); ?>

        <div class="bwp-info" style="margin-top:.5rem;">
            <div>
                <?php if ( $form_permalinks_on ) : ?>
                <p>
                    <strong><?php esc_html_e( 'Active form slugs:', 'book-wp' ); ?></strong>
                    <?php echo implode( ', ', array_map( 'esc_html', $form_slugs ) ); ?>
                </p>
                <?php endif; ?>
                <p<?php if ( $form_permalinks_on ) : ?> style="margin-top:.35rem;"<?php endif; ?>>
                    <?php printf(
                        /* translators: %s: Permalinks settings URL */
                        wp_kses( __( '<strong>Required:</strong> After saving this setting (on or off), visit <a href="%s">Settings &rsaquo; Permalinks</a> and click Save Changes to flush the rewrite rules. Skipping this step causes 404 errors on the new URL structure.', 'book-wp' ), array( 'a' => array( 'href' => array() ), 'strong' => array() ) ),
                        esc_url( admin_url( 'options-permalink.php' ) )
                    ); ?>
                </p>
            </div>
        </div>

        <?php if ( $form_permalinks_on ) : ?>
        <?php
        // ── Rewrite rule inspector ──────────────────────────────────────────
        // Reads the live rewrite rules table and filters for any rule whose
        // regex contains a known form slug. Ground truth: if rules appear here,
        // WordPress is routing /{form}/{post-slug}/ URLs correctly right now.
        // Read from the database option — $wp_rewrite->rules is not populated in admin context.
        $all_rules  = (array) get_option( 'rewrite_rules', array() );
        $form_rules = array();

        if ( ! empty( $form_slugs ) && ! empty( $all_rules ) ) {
            foreach ( $all_rules as $regex => $query ) {
                foreach ( $form_slugs as $s ) {
                    if ( strpos( $regex, preg_quote( $s, '/' ) ) !== false ) {
                        $form_rules[ $regex ] = $query;
                        break;
                    }
                }
            }
        }

        $rule_count   = count( $form_rules );
        // 3 rule patterns per slug set: single / comment-page / paged.
        $expected     = 3;
        $rules_ok     = $rule_count >= $expected;
        $status_color = $rules_ok
            ? 'color:#166534;background:#dcfce7;border-color:#bbf7d0;'
            : 'color:#92400e;background:#fef3c7;border-color:#fde68a;';
        $status_label = $rules_ok
            ? sprintf(
                _n( '%d rule registered — routing active', '%d rules registered — routing active', $rule_count, 'book-wp' ),
                $rule_count
              )
            : ( 0 === $rule_count
                ? __( '0 rules found — flush required', 'book-wp' )
                : sprintf(
                    /* translators: 1: found count, 2: expected count */
                    __( '%1$d rule(s) found — fewer than expected (%2$d), consider flushing', 'book-wp' ),
                    $rule_count, $expected
                  )
              );
        ?>
        <div style="margin-top:.75rem;padding:.65rem .85rem;border:1px solid;border-radius:4px;font-size:.82rem;<?php echo esc_attr( $status_color ); ?>">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:.75rem;">
                <span>
                    <strong><?php esc_html_e( 'Rewrite rules:', 'book-wp' ); ?></strong>
                    <?php echo esc_html( $status_label ); ?>
                </span>
                <?php if ( ! $rules_ok ) : ?>
                <a href="<?php echo esc_url( admin_url( 'options-permalink.php' ) ); ?>"
                   style="font-weight:600;white-space:nowrap;text-decoration:underline;">
                    <?php esc_html_e( 'Flush now', 'book-wp' ); ?> &rarr;
                </a>
                <?php endif; ?>
            </div>
            <?php if ( ! empty( $form_rules ) ) : ?>
            <details style="margin-top:.5rem;">
                <summary style="cursor:pointer;font-weight:600;user-select:none;">
                    <?php esc_html_e( 'Show registered rules', 'book-wp' ); ?>
                </summary>
                <table style="margin-top:.5rem;width:100%;border-collapse:collapse;font-family:monospace;font-size:.78rem;">
                    <thead>
                        <tr>
                            <th style="text-align:left;padding:.2rem .4rem;border-bottom:1px solid currentColor;opacity:.6;font-weight:600;width:55%;"><?php esc_html_e( 'Pattern', 'book-wp' ); ?></th>
                            <th style="text-align:left;padding:.2rem .4rem;border-bottom:1px solid currentColor;opacity:.6;font-weight:600;"><?php esc_html_e( 'Resolves to', 'book-wp' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $form_rules as $regex => $query ) : ?>
                        <tr>
                            <td style="padding:.25rem .4rem;vertical-align:top;word-break:break-all;border-bottom:1px solid rgba(0,0,0,.07);"><code><?php echo esc_html( $regex ); ?></code></td>
                            <td style="padding:.25rem .4rem;vertical-align:top;word-break:break-all;border-bottom:1px solid rgba(0,0,0,.07);"><code><?php echo esc_html( $query ); ?></code></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </details>
            <?php endif; ?>
        </div>
        <?php endif; // $form_permalinks_on ?>
    </div>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Custom CSS', 'book-wp' ); ?></p>
        <div class="bwp-field bwp-field--full">
            <label class="bwp-field__label" for="book_wp_custom_css"><?php esc_html_e( 'Additional Styles', 'book-wp' ); ?></label>
            <div class="bwp-field__control">
                <textarea name="book_wp_custom_css" id="book_wp_custom_css" rows="8"
                          class="bwp-code"><?php echo esc_textarea( get_option( 'book_wp_custom_css', '' ) ); ?></textarea>
            </div>
            <span class="bwp-field__hint"><?php esc_html_e( 'Injected after all theme styles. Use the CSS custom properties below for consistency — hard-coding colour hex values will break in Night mode and time-of-day schemes.', 'book-wp' ); ?></span>
        </div>

        <div class="bwp-info" style="margin-top:.75rem;">
            <div>
                <p><strong><?php esc_html_e( 'Available CSS Custom Properties', 'book-wp' ); ?></strong></p>
                <p>
                    <code>--bwp-bg</code> <code>--bwp-bg-subtle</code> <code>--bwp-text</code>
                    <code>--bwp-text-mid</code> <code>--bwp-muted</code> <code>--bwp-accent</code>
                    <code>--bwp-link</code> <code>--bwp-border</code> <code>--bwp-border-ink</code>
                    <code>--bwp-ornament</code> <code>--bwp-badge-puisi</code>
                    <code>--bwp-badge-nukilan</code> <code>--bwp-badge-ratapan</code>
                    <code>--bwp-font</code> <code>--bwp-font-ui</code> <code>--bwp-font-display</code>
                    <code>--bwp-lh</code>
                </p>
                <p style="margin-top:.5rem; color:var(--theme-text-muted);"><?php esc_html_e( 'All properties update automatically across colour schemes and time-of-day palettes.', 'book-wp' ); ?></p>
            </div>
        </div>
    </div>
    <?php
}


/* ── Social tab ────────────────────────────────────────────────────────── */

/**
 * Render a single social URL field with an icon prefix badge.
 *
 * @param string $slug     Platform slug.
 * @param array  $meta     Platform meta from book_wp_social_platforms().
 */
function bwp_social_field( $slug, $meta ) {
    $option      = 'book_wp_social_' . $slug;
    $value       = get_option( $option, '' );
    $placeholder = ( ! empty( $meta['rss'] ) ) ? esc_attr( get_feed_link() ) : 'https://';
    $icon        = book_wp_social_icon( $slug );
    ?>
    <div class="bwp-field bwp-social-field">
        <label class="bwp-field__label" for="<?php echo esc_attr( $option ); ?>">
            <?php echo esc_html( $meta['label'] ); ?>
        </label>
        <div class="bwp-field__control">
            <div class="bwp-icon-input">
                <span class="bwp-icon-input__badge bwp-icon-input__badge--<?php echo esc_attr( $slug ); ?>" aria-hidden="true">
                    <?php echo $icon; // phpcs:ignore — sanitised SVG from internal registry ?>
                </span>
                <input type="url"
                       id="<?php echo esc_attr( $option ); ?>"
                       name="<?php echo esc_attr( $option ); ?>"
                       value="<?php echo esc_url( $value ); ?>"
                       placeholder="<?php echo esc_attr( $placeholder ); ?>"
                       autocomplete="url"
                       spellcheck="false">
            </div>
        </div>
    </div>
    <?php
}

function bwp_tab_social() {
    $platforms = book_wp_social_platforms();

    // Group platforms into logical sections.
    $groups = array(
        array(
            'label'    => __( 'Social Media', 'book-wp' ),
            'slugs'    => array( 'twitter', 'bluesky', 'facebook', 'instagram', 'threads', 'tiktok', 'linkedin', 'youtube', 'medium', 'substack', 'telegram', 'whatsapp' ),
        ),
        array(
            'label'    => __( 'Libraries & Catalogues', 'book-wp' ),
            'slugs'    => array( 'wikipedia', 'goodreads', 'worldcat', 'loc', 'nlb', 'pnm' ),
        ),
        array(
            'label'    => __( 'Developer & Repository', 'book-wp' ),
            'slugs'    => array( 'github' ),
        ),
        array(
            'label'    => __( 'Author Identifiers', 'book-wp' ),
            'slugs'    => array( 'orcid', 'isni', 'viaf', 'wikidata', 'academia' ),
        ),
        array(
            'label'    => __( 'Syndication', 'book-wp' ),
            'slugs'    => array( 'rss' ),
        ),
    );
    ?>

    <div class="bwp-section">
        <p class="bwp-section__label"><?php esc_html_e( 'Social Profiles', 'book-wp' ); ?></p>
        <p class="bwp-field__hint" style="margin:0 0 .875rem;"><?php esc_html_e( 'Active icons appear in the site footer. Leave a field blank to hide that icon. For RSS, leave blank to use the WordPress feed URL automatically.', 'book-wp' ); ?></p>

        <?php foreach ( $groups as $group ) : ?>
            <p class="bwp-social-group-label"><?php echo esc_html( $group['label'] ); ?></p>
            <?php foreach ( $group['slugs'] as $slug ) :
                if ( isset( $platforms[ $slug ] ) ) {
                    bwp_social_field( $slug, $platforms[ $slug ] );
                }
            endforeach; ?>
        <?php endforeach; ?>

    </div>
    <?php
}
