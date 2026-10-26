<?php
/**
 * Literary Form Permalinks
 *
 * When the book_wp_form_permalinks option is enabled, post URLs take the form:
 *
 *     https://example.com/{literary-form-slug}/{post-slug}/
 *
 * e.g. https://example.com/puisi/waktu-melata/
 *
 * The taxonomy archive for a form is unaffected — it continues to live at:
 *
 *     https://example.com/form/puisi/
 *
 * Posts with no literary form assigned retain their current permalink unchanged.
 *
 * IMPLEMENTATION NOTES
 * --------------------
 * - Rewrite rules are built from all terms currently registered in the
 *   literary_form taxonomy (not a hardcoded slug list), so custom terms work
 *   automatically after a flush.
 * - Rules are inserted at the top of the rewrite table so they fire before
 *   WordPress's catch-all `^([^/]+)/?` page rule.
 * - The pattern uses a compiled alternation of known form slugs rather than
 *   a wildcard `([^/]+)` prefix, so pages, categories, and other content are
 *   never caught by these rules.
 * - When the option is toggled or a literary_form term is created/edited/deleted,
 *   rewrite rules are flushed automatically on the next admin request.
 * - A permalink flush is required on first activation. The settings UI shows a
 *   notice with a direct link to the Permalinks screen when the option is on but
 *   the rules have not yet been flushed.
 *
 * @package Book_WP
 * @since   3.4.60
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Utility: get all registered literary_form slugs ─────────────────── */

/**
 * Return an array of term slugs for all terms in the literary_form taxonomy.
 * Reads from a transient; regenerated on term create/edit/delete.
 *
 * Note: book_wp_get_form_slug( $post_id ) is defined in inc/helpers/literary.php
 * and is available here without redeclaration.
 *
 * @return string[]
 */
function book_wp_permalink_form_slugs() {
    $cached = get_transient( 'book_wp_form_slugs' );
    if ( false !== $cached ) return $cached;

    $terms = get_terms( array(
        'taxonomy'   => 'literary_form',
        'hide_empty' => false,
        'fields'     => 'slugs',
    ) );

    $slugs = ( $terms && ! is_wp_error( $terms ) ) ? array_values( $terms ) : array();
    set_transient( 'book_wp_form_slugs', $slugs, DAY_IN_SECONDS );
    return $slugs;
}

/* ── Transient invalidation on term changes ──────────────────────────── */

add_action( 'created_term',  'book_wp_invalidate_form_slugs_transient', 10, 3 );
add_action( 'edited_term',   'book_wp_invalidate_form_slugs_transient', 10, 3 );
add_action( 'delete_term',   'book_wp_invalidate_form_slugs_transient', 10, 3 );

function book_wp_invalidate_form_slugs_transient( $term_id, $tt_id, $taxonomy ) {
    if ( 'literary_form' !== $taxonomy ) return;
    delete_transient( 'book_wp_form_slugs' );
    // Belt-and-suspenders for persistent object caches that may not honour
    // delete_transient() immediately (e.g. some Redis/Memcached configurations).
    wp_cache_delete( 'book_wp_form_slugs', 'transient' );

    // WP-CLI and REST API contexts never fire admin_init, so the deferred flag
    // would sit unprocessed indefinitely. Flush immediately in those contexts.
    $is_cli  = defined( 'WP_CLI' ) && WP_CLI;
    $is_rest = defined( 'REST_REQUEST' ) && REST_REQUEST;

    if ( $is_cli || $is_rest ) {
        flush_rewrite_rules( false );
    } else {
        // Web admin context: defer to admin_init so the flush runs after all
        // init hooks have fired (including register_taxonomy priority 10).
        update_option( 'book_wp_flush_rewrite_rules', '1' );
    }
}

/* ── Deferred rewrite flush (admin only) ─────────────────────────────── */

add_action( 'admin_init', 'book_wp_maybe_flush_rewrite_rules' );

function book_wp_maybe_flush_rewrite_rules() {
    if ( '1' === get_option( 'book_wp_flush_rewrite_rules' ) ) {
        delete_option( 'book_wp_flush_rewrite_rules' );
        flush_rewrite_rules( false ); // false = soft flush; avoids full htaccess rewrite
    }
}

/* ── Rewrite rule registration ────────────────────────────────────────── */

add_action( 'init', 'book_wp_register_form_permalink_rules', 20 );
// Priority 20: after register_taxonomy (priority 10) and after add_feed (priority 10),
// so taxonomy and feed rewrites are already in place when we prepend ours.

function book_wp_register_form_permalink_rules() {
    if ( '1' !== get_option( 'book_wp_form_permalinks', '0' ) ) return;

    $slugs = book_wp_permalink_form_slugs();
    if ( empty( $slugs ) ) return;

    // Build a precise alternation — never a wildcard — so only known form
    // slugs trigger these rules. Pages, categories, tags, and other rewrites
    // are not affected.
    // Specify '|' as the delimiter so any literal | in a slug is escaped correctly.
    // WordPress wraps rewrite rule patterns with | as the preg_match delimiter.
    $slugs_pattern = implode( '|', array_map( function( $s ) { return preg_quote( $s, '|' ); }, $slugs ) );

    // Single post: /{form}/{post-slug}/
    add_rewrite_rule(
        '^(' . $slugs_pattern . ')/([^/]+)/?$',
        'index.php?name=$matches[2]',
        'top'
    );

    // Single post with paged comments: /{form}/{post-slug}/comment-page-{n}/
    add_rewrite_rule(
        '^(' . $slugs_pattern . ')/([^/]+)/comment-page-([0-9]{1,4})/?$',
        'index.php?name=$matches[2]&cpage=$matches[3]',
        'top'
    );

    // Paged single post (for very long posts split by <!--nextpage-->):
    // /{form}/{post-slug}/{n}/
    add_rewrite_rule(
        '^(' . $slugs_pattern . ')/([^/]+)/([0-9]+)/?$',
        'index.php?name=$matches[2]&page=$matches[3]',
        'top'
    );
}

/* ── post_link filter ─────────────────────────────────────────────────── */

add_filter( 'post_link', 'book_wp_form_permalink', 10, 3 );

/**
 * Replace the default post permalink with /{form-slug}/{post-slug}/ when:
 * - The option is enabled.
 * - The post has a literary_form term assigned.
 *
 * Posts without a literary form are returned unchanged.
 *
 * @param string  $permalink   The post's permalink.
 * @param WP_Post $post        The post in question.
 * @param bool    $leavename   Whether to keep the %postname% placeholder.
 * @return string
 */
function book_wp_form_permalink( $permalink, $post, $leavename ) {
    if ( '1' !== get_option( 'book_wp_form_permalinks', '0' ) ) return $permalink;
    if ( 'post' !== $post->post_type ) return $permalink;
    if ( 'publish' !== $post->post_status && ! in_array( $post->post_status, array( 'private', 'future' ), true ) ) return $permalink;

    $form_slug = book_wp_get_form_slug( $post->ID );
    if ( ! $form_slug ) return $permalink;

    $post_slug = $leavename ? '%postname%' : $post->post_name;

    return trailingslashit( home_url( '/' . $form_slug . '/' . $post_slug ) );
}

/* ── Flush on option toggle ───────────────────────────────────────────── */

add_action( 'updated_option', 'book_wp_flush_on_permalink_toggle' );

function book_wp_flush_on_permalink_toggle( $option_name ) {
    if ( 'book_wp_form_permalinks' !== $option_name ) return;
    // Do not flush here: this runs inside the request that saved the option,
    // where init already fired with the *old* value, so the form rules are not
    // registered yet and an immediate flush would persist a rule set without
    // them. Defer to admin_init on the next request (see
    // book_wp_maybe_flush_rewrite_rules), by which time init has re-run with
    // the new value and the rules are in place.
    update_option( 'book_wp_flush_rewrite_rules', '1' );
}

/* ── Flush on theme activation ────────────────────────────────────────── */

add_action( 'after_switch_theme', 'book_wp_flush_on_theme_switch' );

function book_wp_flush_on_theme_switch() {
    if ( '1' === get_option( 'book_wp_form_permalinks', '0' ) ) {
        flush_rewrite_rules( false );
    }
}

/* ── Form-slug bare URL redirect ──────────────────────────────────────── */

add_action( 'template_redirect', 'book_wp_redirect_bare_form_slug' );

/**
 * Redirect bare literary-form slug URLs to their taxonomy archive.
 *
 * When Literary Form Permalinks is enabled, the rewrite rules only match
 * /{form}/{post-slug}/ patterns. A direct visit to /{form}/ or /{form}
 * with no post slug falls through to a 404. This hook catches that case
 * and redirects:
 *
 *   https://example.com/puisi/     →  https://example.com/
 *   https://example.com/syair      →  https://example.com/
 *
 * Only fires when:
 *   - The Literary Form Permalinks option is enabled.
 *   - The current request is a 404.
 *   - The first path segment exactly matches a registered literary_form slug.
 *
 * Uses 301 (permanent) so browsers and crawlers update bookmarks/indexes.
 *
 * @since 3.4.64
 */
function book_wp_redirect_bare_form_slug() {
    // Only relevant when the feature is active.
    if ( '1' !== get_option( 'book_wp_form_permalinks', '0' ) ) return;

    // Only intercept 404 responses — let WordPress handle everything else.
    if ( ! is_404() ) return;

    // Parse the request path to extract the first segment.
    $request = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );

    // Strip the WordPress sub-directory prefix if the site is installed
    // in a sub-folder (e.g. /blog/puisi/ → puisi/).
    $home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
    if ( $home_path && strpos( $request, $home_path . '/' ) === 0 ) {
        $request = substr( $request, strlen( $home_path ) + 1 );
    }

    // We only want a bare single segment — reject anything with a slash
    // remaining (e.g. /puisi/some-post/ should never reach here as a 404
    // under normal operation, but guard anyway).
    $segment = strtok( $request, '/' );
    if ( ! $segment || strpos( trim( $request, '/' ), '/' ) !== false ) return;

    // Check if the segment matches a known literary_form slug.
    $slugs = book_wp_permalink_form_slugs();
    if ( ! in_array( $segment, $slugs, true ) ) return;

    // Redirect to the homepage — the bare form slug has no content of its own.
    wp_redirect( esc_url_raw( home_url( '/' ) ), 301 );
    exit;
}

/* ── Legacy post-slug URL redirect ────────────────────────────────────── */

add_action( 'template_redirect', 'book_wp_redirect_legacy_post_slug' );

/**
 * Redirect old /{post-slug}/ URLs to the new /{form-slug}/{post-slug}/ URLs.
 *
 * When Literary Form Permalinks is enabled, posts move from
 * /{post-slug}/ to /{form-slug}/{post-slug}/. The old /{post-slug}/ URL
 * is NOT a 404 — WordPress's own %postname% rewrite rule still resolves it
 * to the post. The redirect must therefore fire on a successfully resolved
 * single post, not on a 404.
 *
 * Strategy: on template_redirect for a single post, compare the current
 * request URL against the canonical get_permalink(). If they differ, the
 * visitor is on the old URL structure — redirect 301 to the canonical URL.
 *
 * Only fires when:
 *   - Literary Form Permalinks is enabled.
 *   - The current request is a single post (is_single()).
 *   - The current URL does not match get_permalink() for the post.
 *   - The post has a literary form assigned (posts without a form have
 *     an identical canonical URL and will never trigger this).
 *
 * Pages are never affected — is_single() is false for pages.
 *
 * @since 3.4.67
 */
function book_wp_redirect_legacy_post_slug() {
    if ( '1' !== get_option( 'book_wp_form_permalinks', '0' ) ) return;

    // Only intercept single posts — pages, archives, 404s are all excluded.
    if ( ! is_single() ) return;

    // Sub-resources of a single post have a path that legitimately differs
    // from get_permalink(): <!--nextpage--> pages, paged comments, per-post
    // feeds, embeds, trackbacks and previews. Redirecting them would send the
    // reader back to page one, so leave them alone.
    if ( is_feed() || is_embed() || is_preview() || is_trackback() ) return;
    if ( get_query_var( 'page' ) || get_query_var( 'cpage' ) ) return;

    $post_id = get_the_ID();
    if ( ! $post_id ) return;

    // Canonical URL as the post_link filter produces it (includes form slug
    // when a literary_form term is assigned).
    $canonical = get_permalink( $post_id );
    if ( ! $canonical ) return;

    // Build the current full URL for comparison.
    $scheme   = is_ssl() ? 'https' : 'http';
    $current  = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // Strip query strings from both sides before comparing — query strings
    // are irrelevant to permalink structure and should not trigger a redirect.
    $canonical_path = strtok( $canonical, '?' );
    $current_path   = strtok( $current,   '?' );

    // Normalise trailing slashes so a missing slash doesn't cause a loop
    // when the site uses no-trailing-slash permalinks.
    $canonical_path = trailingslashit( $canonical_path );
    $current_path   = trailingslashit( $current_path );

    if ( $current_path === $canonical_path ) return;

    // Preserve any query string on the redirect target so ?preview=true etc.
    // survive the redirect.
    $query = parse_url( $current, PHP_URL_QUERY );
    $target = $query ? $canonical . '?' . $query : $canonical;

    wp_redirect( esc_url_raw( $target ), 301 );
    exit;
}

/* ── Admin notice: pending rewrite flush ─────────────────────────────── */

add_action( 'admin_notices', 'book_wp_pending_flush_admin_notice' );

/**
 * Show a dismissible warning banner on all admin screens when a rewrite flush
 * is pending but has not yet been processed.
 *
 * This covers the gap where admin_init was supposed to run the flush but
 * something prevented it — e.g. a WP-CLI context that failed, a PHP error
 * during the flush, or a server restart between the flag being set and
 * admin_init firing.
 *
 * The banner links directly to Settings › Permalinks. Dismissed via a
 * query-string parameter so no user meta is needed.
 *
 * @since 3.4.82
 */
function book_wp_pending_flush_admin_notice() {
    // Only relevant when Literary Form Permalinks is active.
    if ( '1' !== get_option( 'book_wp_form_permalinks', '0' ) ) return;

    // No pending flag — nothing to show.
    if ( '1' !== get_option( 'book_wp_flush_rewrite_rules', '' ) ) return;

    // Capability check — only show to users who can manage options.
    if ( ! current_user_can( 'manage_options' ) ) return;

    $flush_url = admin_url( 'options-permalink.php' );
    ?>
    <div class="notice notice-warning is-dismissible">
        <p>
            <strong><?php esc_html_e( 'Sasterawan — rewrite flush pending', 'book-wp' ); ?></strong>
            &nbsp;
            <?php printf(
                wp_kses(
                    /* translators: %s: Settings > Permalinks URL */
                    __( 'A literary form was recently added or changed. Visit <a href="%s">Settings &rsaquo; Permalinks</a> and click <strong>Save Changes</strong> to apply the new URL routing rules. Until then, URLs for the new form may return 404 errors.', 'book-wp' ),
                    array( 'a' => array( 'href' => array() ), 'strong' => array() )
                ),
                esc_url( $flush_url )
            ); ?>
        </p>
    </div>
    <?php
}

/* ── Deleted literary-form term: stale URL rescue ────────────────────── */

/**
 * Before a literary_form term is deleted, snapshot the post IDs assigned to
 * it and the term slug into a short-lived transient. This gives the front-end
 * redirect handler enough information to send a 301 from the now-stale
 * /{deleted-form}/{post-slug}/ URL to the post's new canonical permalink.
 *
 * Without this, deleting a literary form creates permanent 404s for any
 * visitor who bookmarked or linked to the old URL structure. WordPress removes
 * the term assignment automatically but cannot know the old URL prefix.
 *
 * The transient uses a 7-day window. After 7 days the stale URLs return 404
 * (no different to any other deleted content), but by then search engines will
 * have re-crawled the 301 and updated their indexes.
 *
 * @since 3.4.82
 */
add_action( 'pre_delete_term', 'book_wp_snapshot_deleted_form_term', 10, 2 );

function book_wp_snapshot_deleted_form_term( $term_id, $taxonomy ) {
    if ( 'literary_form' !== $taxonomy ) return;

    $term = get_term( $term_id, 'literary_form' );
    if ( ! $term || is_wp_error( $term ) ) return;

    // Get all post IDs currently assigned to this term.
    $post_ids = get_objects_in_term( $term_id, 'literary_form' );
    if ( is_wp_error( $post_ids ) || empty( $post_ids ) ) return;

    // Snapshot: slug → [ post_id => post_name, ... ]
    $map = array();
    foreach ( $post_ids as $pid ) {
        $post = get_post( $pid );
        if ( $post && 'publish' === $post->post_status ) {
            $map[ $post->post_name ] = (int) $pid;
        }
    }
    if ( empty( $map ) ) return;

    // Store keyed by the deleted slug so the rescue handler can look it up.
    $existing = (array) get_transient( 'book_wp_deleted_form_slugs' );
    $existing[ $term->slug ] = $map;
    set_transient( 'book_wp_deleted_form_slugs', $existing, 7 * DAY_IN_SECONDS );
}

/**
 * Redirect stale /{deleted-form}/{post-slug}/ URLs to the post's current
 * canonical permalink (301). Fires on template_redirect for 404 responses.
 *
 * Only active when Literary Form Permalinks is enabled and the snapshot
 * transient exists (i.e. a term was deleted within the last 7 days).
 *
 * @since 3.4.82
 */
add_action( 'template_redirect', 'book_wp_rescue_deleted_form_url' );

function book_wp_rescue_deleted_form_url() {
    if ( '1' !== get_option( 'book_wp_form_permalinks', '0' ) ) return;
    if ( ! is_404() ) return;

    $snapshot = get_transient( 'book_wp_deleted_form_slugs' );
    if ( empty( $snapshot ) || ! is_array( $snapshot ) ) return;

    // Parse the two-segment path: /{form-slug}/{post-slug}/
    $request   = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
    $home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
    if ( $home_path && strpos( $request, $home_path . '/' ) === 0 ) {
        $request = substr( $request, strlen( $home_path ) + 1 );
    }

    $parts = explode( '/', trim( $request, '/' ) );
    if ( count( $parts ) < 2 ) return;

    $form_segment = $parts[0];
    $post_segment = $parts[1];

    if ( ! isset( $snapshot[ $form_segment ] ) ) return;
    if ( ! isset( $snapshot[ $form_segment ][ $post_segment ] ) ) return;

    $post_id = $snapshot[ $form_segment ][ $post_segment ];
    $post    = get_post( $post_id );
    if ( ! $post || 'publish' !== $post->post_status ) return;

    // get_permalink uses the post_link filter — if the post still has a
    // literary_form assigned (a different one), the new canonical URL is used.
    // If no form is assigned, the default %postname% permalink is used.
    $target = get_permalink( $post_id );
    if ( ! $target ) return;

    wp_redirect( esc_url_raw( $target ), 301 );
    exit;
}
