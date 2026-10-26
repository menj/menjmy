<?php
/**
 * Sasterawan — Functions & Definitions
 *
 * Bootstrap only. Defines constants and loads includes.
 * No feature implementations here — those live in inc/.
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/* ── Constants ─────────────────────────────────────────────────────────── */

// Read version from style.css so it never drifts out of sync.
$book_wp_theme = wp_get_theme();
define( 'BOOK_WP_VERSION', $book_wp_theme->get( 'Version' ) );
define( 'BOOK_WP_DIR',     get_template_directory() );
define( 'BOOK_WP_URI',     get_template_directory_uri() );

// Maximum number of thematic tags per post (hiding-trigger tags excluded from cap).
define( 'BOOK_WP_MAX_TAGS', 5 );

/* ── Core includes ─────────────────────────────────────────────────────── */

require_once BOOK_WP_DIR . '/inc/setup/theme-support.php';
require_once BOOK_WP_DIR . '/inc/setup/menus.php';
require_once BOOK_WP_DIR . '/inc/setup/sidebars.php';
require_once BOOK_WP_DIR . '/inc/setup/enqueue.php';

// strings.php removed in v3.3.51 — all strings migrated to WordPress gettext (__())
require_once BOOK_WP_DIR . '/inc/helpers/literary.php';   // form helpers & ornaments
require_once BOOK_WP_DIR . '/inc/helpers/analytics.php';  // reading metrics & analytics bar
require_once BOOK_WP_DIR . '/inc/helpers/colophon.php';   // epigraph, dedication, colophon
require_once BOOK_WP_DIR . '/inc/helpers/navigation.php'; // related, nav, toc, progress bar
require_once BOOK_WP_DIR . '/inc/helpers/social.php';     // social profile icons
require_once BOOK_WP_DIR . '/inc/helpers/json-feed.php';  // JSON Feed v1.1
require_once BOOK_WP_DIR . '/inc/helpers/permalink.php';  // literary form permalinks
require_once BOOK_WP_DIR . '/inc/helpers/migrate.php';    // upgrade path from Book WP

require_once BOOK_WP_DIR . '/inc/hooks/actions.php';
require_once BOOK_WP_DIR . '/inc/hooks/filters.php';

require_once BOOK_WP_DIR . '/inc/shortcodes/shortcodes.php';

/* ── Admin-only includes ───────────────────────────────────────────────── */

if ( is_admin() ) {
    require_once BOOK_WP_DIR . '/inc/admin/meta-boxes.php';
    require_once BOOK_WP_DIR . '/inc/admin/settings.php';
    require_once BOOK_WP_DIR . '/inc/admin/term-covers.php';
    require_once BOOK_WP_DIR . '/inc/admin/dashboard-widget.php';
}
