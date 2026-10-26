<?php
/**
 * Book WP — Dashboard Widget: Reading Statistics
 *
 * Adds a dashboard widget to the WordPress admin home screen showing
 * aggregate reading statistics across all published works: total word
 * count, average reading time, works per literary form, and the
 * longest/shortest works.
 *
 * Data is computed from published posts and cached in a transient for
 * 12 hours to avoid expensive queries on every dashboard load.
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/* ── Register the widget ──────────────────────────────────────────────── */

add_action( 'wp_dashboard_setup', 'book_wp_register_dashboard_widget' );

function book_wp_register_dashboard_widget() {
    if ( ! current_user_can( 'edit_posts' ) ) return;
    wp_add_dashboard_widget(
        'book_wp_reading_stats',
        __( 'Reading Statistics', 'book-wp' ),
        'book_wp_dashboard_widget_render'
    );
}

/* ── Enqueue widget assets ────────────────────────────────────────────── */

add_action( 'admin_enqueue_scripts', 'book_wp_dashboard_widget_assets' );

function book_wp_dashboard_widget_assets( $hook ) {
    if ( 'index.php' !== $hook ) return;
    wp_enqueue_style(
        'book-wp-dashboard-widget',
        BOOK_WP_URI . '/assets/css/dashboard-widget.css',
        array(),
        BOOK_WP_VERSION
    );
    wp_enqueue_script(
        'book-wp-dashboard-widget',
        BOOK_WP_URI . '/assets/js/dashboard-widget.js',
        array(),
        BOOK_WP_VERSION,
        true
    );
}

/* ── Data layer — compute + cache ─────────────────────────────────────── */

/**
 * Collect aggregate reading statistics from all published posts.
 *
 * Returns a transient-cached array with:
 *   total_works       int    Total published posts.
 *   total_words       int    Sum of word counts across all works.
 *   avg_words         int    Average words per work.
 *   avg_reading_time  string Human-readable average reading time.
 *   forms             array  slug => [ label, count, words, is_poetry ]
 *   longest           object { title, words, permalink }
 *   shortest          object { title, words, permalink }
 *   last_updated      string ISO 8601 timestamp.
 *
 * @return array
 */
function book_wp_dashboard_stats() {
    $cached = get_transient( 'book_wp_dashboard_stats' );
    if ( false !== $cached ) return $cached;

    // Exclude posts carrying any hidden tag — consistent with the TOC,
    // archives, search, and related works sections on the frontend.
    $query_args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    );
    $hidden_ids = book_wp_hidden_tag_ids();
    if ( ! empty( $hidden_ids ) ) {
        $query_args['tag__not_in'] = $hidden_ids;
    }

    $posts = get_posts( $query_args );

    $total_works  = count( $posts );
    $total_words  = 0;
    $total_time   = 0;
    $forms        = array();
    $longest      = null;
    $shortest     = null;

    foreach ( $posts as $pid ) {
        $words = book_wp_word_count( $pid );
        $slug  = book_wp_get_form_slug( $pid );
        $is_p  = book_wp_is_poetry( $pid );
        $wpm   = $is_p ? 120 : 200;

        $total_words += $words;
        $total_time  += max( 1, (int) ceil( $words / $wpm ) );

        if ( $slug ) {
            if ( ! isset( $forms[ $slug ] ) ) {
                $forms[ $slug ] = array(
                    'label'     => book_wp_form_label( $slug ),
                    'count'     => 0,
                    'words'     => 0,
                    'is_poetry' => $is_p,
                );
            }
            $forms[ $slug ]['count']++;
            $forms[ $slug ]['words'] += $words;
        }

        if ( null === $longest || $words > $longest['words'] ) {
            $longest = array(
                'title'     => get_the_title( $pid ),
                'words'     => $words,
                'permalink' => get_permalink( $pid ),
            );
        }
        if ( null === $shortest || ( $words < $shortest['words'] && $words > 0 ) ) {
            $shortest = array(
                'title'     => get_the_title( $pid ),
                'words'     => $words,
                'permalink' => get_permalink( $pid ),
            );
        }
    }

    // Sort forms by count descending.
    uasort( $forms, function( $a, $b ) { return $b['count'] - $a['count']; } );

    $stats = array(
        'total_works'      => $total_works,
        'total_words'      => $total_words,
        'avg_words'        => $total_works ? (int) round( $total_words / $total_works ) : 0,
        'avg_reading_time' => $total_works ? max( 1, (int) round( $total_time / $total_works ) ) : 0,
        'forms'            => $forms,
        'longest'          => $longest,
        'shortest'         => $shortest,
        'last_updated'     => current_time( 'c' ),
    );

    set_transient( 'book_wp_dashboard_stats', $stats, 12 * HOUR_IN_SECONDS );
    return $stats;
}

/* ── Bust the transient whenever a post is saved or trashed ───────────── */

add_action( 'save_post',       'book_wp_dashboard_stats_bust', 20 );
add_action( 'deleted_post',    'book_wp_dashboard_stats_bust', 20 );
add_action( 'wp_trash_post',   'book_wp_dashboard_stats_bust', 20 );
add_action( 'untrash_post',    'book_wp_dashboard_stats_bust', 20 );

// Also bust when the hidden tags setting changes — the exclusion set has shifted.
add_action( 'update_option_book_wp_hidden_tags', 'book_wp_dashboard_stats_bust', 20 );

function book_wp_dashboard_stats_bust() {
    delete_transient( 'book_wp_dashboard_stats' );
}

/* ── Render ───────────────────────────────────────────────────────────── */

function book_wp_dashboard_widget_render() {
    $s = book_wp_dashboard_stats();
    ?>
    <div class="bwp-dash" data-updated="<?php echo esc_attr( $s['last_updated'] ); ?>">

        <?php if ( 0 === $s['total_works'] ) : ?>
            <p class="bwp-dash__empty">
                <?php esc_html_e( 'No published works yet. Start writing!', 'book-wp' ); ?>
            </p>
        <?php return; endif; ?>

        <!-- ── Headline metrics ────────────────────────────────── -->
        <div class="bwp-dash__metrics">
            <div class="bwp-dash__metric">
                <span class="bwp-dash__metric-value"><?php echo esc_html( number_format_i18n( $s['total_works'] ) ); ?></span>
                <span class="bwp-dash__metric-label"><?php esc_html_e( 'Works', 'book-wp' ); ?></span>
            </div>
            <div class="bwp-dash__metric">
                <span class="bwp-dash__metric-value"><?php echo esc_html( number_format_i18n( $s['total_words'] ) ); ?></span>
                <span class="bwp-dash__metric-label"><?php esc_html_e( 'Total Words', 'book-wp' ); ?></span>
            </div>
            <div class="bwp-dash__metric">
                <span class="bwp-dash__metric-value"><?php echo esc_html( number_format_i18n( $s['avg_words'] ) ); ?></span>
                <span class="bwp-dash__metric-label"><?php esc_html_e( 'Avg. Words', 'book-wp' ); ?></span>
            </div>
            <div class="bwp-dash__metric">
                <span class="bwp-dash__metric-value">
                    <?php echo esc_html( $s['avg_reading_time'] ); ?><span class="bwp-dash__metric-unit"><?php esc_html_e( 'min', 'book-wp' ); ?></span>
                </span>
                <span class="bwp-dash__metric-label"><?php esc_html_e( 'Avg. Read Time', 'book-wp' ); ?></span>
            </div>
        </div>

        <!-- ── Works per literary form ─────────────────────────── -->
        <?php if ( ! empty( $s['forms'] ) ) : ?>
        <div class="bwp-dash__section">
            <p class="bwp-dash__section-label"><?php esc_html_e( 'Works by Form', 'book-wp' ); ?></p>
            <div class="bwp-dash__form-list">
                <?php
                $max_count = max( array_column( $s['forms'], 'count' ) );
                foreach ( $s['forms'] as $slug => $f ) :
                    $pct = $max_count ? round( ( $f['count'] / $max_count ) * 100 ) : 0;
                ?>
                <div class="bwp-dash__form-row">
                    <span class="bwp-dash__form-badge bwp-dash__form-badge--<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $f['label'] ); ?></span>
                    <span class="bwp-dash__form-bar-wrap">
                        <span class="bwp-dash__form-bar" style="width:<?php echo (int) $pct; ?>%"></span>
                    </span>
                    <span class="bwp-dash__form-count"><?php echo esc_html( $f['count'] ); ?></span>
                    <span class="bwp-dash__form-words"><?php echo esc_html( number_format_i18n( $f['words'] ) ); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Extremes ────────────────────────────────────────── -->
        <?php if ( $s['longest'] && $s['shortest'] && $s['longest']['title'] !== $s['shortest']['title'] ) : ?>
        <div class="bwp-dash__section bwp-dash__extremes">
            <div class="bwp-dash__extreme">
                <span class="bwp-dash__extreme-label"><?php esc_html_e( 'Longest', 'book-wp' ); ?></span>
                <a href="<?php echo esc_url( $s['longest']['permalink'] ); ?>" class="bwp-dash__extreme-title"><?php echo esc_html( $s['longest']['title'] ); ?></a>
                <span class="bwp-dash__extreme-meta">
                    <?php echo esc_html( sprintf( __( '%s words', 'book-wp' ), number_format_i18n( $s['longest']['words'] ) ) ); ?>
                </span>
            </div>
            <div class="bwp-dash__extreme">
                <span class="bwp-dash__extreme-label"><?php esc_html_e( 'Shortest', 'book-wp' ); ?></span>
                <a href="<?php echo esc_url( $s['shortest']['permalink'] ); ?>" class="bwp-dash__extreme-title"><?php echo esc_html( $s['shortest']['title'] ); ?></a>
                <span class="bwp-dash__extreme-meta">
                    <?php echo esc_html( sprintf( __( '%s words', 'book-wp' ), number_format_i18n( $s['shortest']['words'] ) ) ); ?>
                </span>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Footer ──────────────────────────────────────────── -->
        <div class="bwp-dash__footer">
            <a href="<?php echo esc_url( admin_url( 'themes.php?page=book-wp-settings' ) ); ?>" class="bwp-dash__settings-link">
                <?php esc_html_e( 'Theme Settings', 'book-wp' ); ?> &rarr;
            </a>
        </div>

    </div>
    <?php
}
