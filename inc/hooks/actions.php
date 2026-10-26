<?php
/**
 * Book WP — Action Hooks
 *
 * Non-setup, non-admin add_action registrations.
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/* ── Literary Taxonomy ─────────────────────────────────────────────────── */

add_action( 'init', 'book_wp_register_taxonomy' );

function book_wp_register_taxonomy() {
    register_taxonomy( 'literary_form', 'post', array(
        'labels' => array(
            'name'          => _x( 'Literary Forms', 'taxonomy general name', 'book-wp' ),
            'singular_name' => _x( 'Literary Form',  'taxonomy singular name', 'book-wp' ),
            'menu_name'     => __( 'Literary Forms', 'book-wp' ),
        ),
        'hierarchical'       => true,
        'public'             => true,
        'show_in_rest'       => true,
        'show_admin_column'  => true,
        'rewrite'            => array( 'slug' => 'form' ),
    ) );

    // Seed default terms. Uses a version flag so any terms added in later
    // releases are inserted on the next page load without wiping user data —
    // wp_insert_term() is skipped for slugs that already exist.
    $default_terms = array(
        'syair'   => 'Syair',
        'karmina' => 'Karmina',
        'hikayat' => 'Hikayat',
        'puisi'   => 'Puisi',
        'cerpen'  => 'Cerpen',
        'novel'   => 'Novel',
        'nukilan' => 'Nukilan',
        'ratapan' => 'Ratapan',
        'esei'    => 'Esei',
        'memoir'  => 'Memoir',
    );

    // Term migrations: old_slug => array( 'slug' => new_slug, 'name' => new_name ).
    // Handles renames and slug changes from previous releases. Safe to run
    // repeatedly — skipped if the old slug no longer exists.
    $term_migrations = array(
        'autobiografi' => array( 'slug' => 'memoir', 'name' => 'Memoir' ), // 3.3.4: slug migration (3.3.3 flag already consumed by name change)
    );

    $terms_version = '3.3.4';
    if ( get_option( 'book_wp_terms_version' ) !== $terms_version ) {
        // Insert any terms that don't exist yet.
        foreach ( $default_terms as $slug => $name ) {
            if ( ! term_exists( $slug, 'literary_form' ) ) {
                wp_insert_term( $name, 'literary_form', array( 'slug' => $slug ) );
            }
        }
        // Migrate terms: update slug and/or name from previous releases.
        foreach ( $term_migrations as $old_slug => $updates ) {
            $term = get_term_by( 'slug', $old_slug, 'literary_form' );
            if ( $term && ! is_wp_error( $term ) ) {
                $args = array();
                if ( ! empty( $updates['name'] ) && $term->name !== $updates['name'] ) {
                    $args['name'] = $updates['name'];
                }
                if ( ! empty( $updates['slug'] ) && $term->slug !== $updates['slug'] ) {
                    $args['slug'] = $updates['slug'];
                }
                if ( ! empty( $args ) ) {
                    wp_update_term( $term->term_id, 'literary_form', $args );
                }
            }
        }
        update_option( 'book_wp_terms_version', $terms_version );
    }
}

/* ── Post Meta Registration ────────────────────────────────────────────── */

add_action( 'init', 'book_wp_register_meta' );

function book_wp_register_meta() {

    /*
     * REST API exposure policy:
     *
     * show_in_rest: true  — exposed to block editor (Gutenberg needs these
     *                       to populate block attributes). Also publicly readable
     *                       via /wp-json/wp/v2/posts/{id}/meta without auth.
     *
     * show_in_rest: false — private to PHP/admin only. Not accessible via REST.
     *                       Used for all scholarly, personal, and sensitive fields
     *                       that have no block editor use case.
     *
     * Only the chapter block attributes need REST exposure. All other fields
     * are read exclusively by PHP helpers (colophon.php, navigation.php, etc.)
     * and do not require REST access.
     */

    // ── Block-editor fields (REST exposed) ───────────────────────────────
    // Required by the book-wp/chapter block to read/write chapter attributes.
    $rest_fields = array(
        'book_wp_chapter_number', // block: chapter num
        'book_wp_chapter_part',   // block: verse start offset
    );

    foreach ( $rest_fields as $key ) {
        register_post_meta( 'post', $key, array(
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => function () {
                return current_user_can( 'edit_posts' );
            },
        ) );
    }

    // ── Private fields (REST hidden) ─────────────────────────────────────
    // All scholarly, compositional, and personal metadata. Readable only by
    // PHP — never exposed to /wp-json/wp/v2/posts/{id}/meta.
    $private_fields = array(
        // Display overrides
        'book_wp_drop_cap_override',
        // Core literary apparatus
        'book_wp_epigraph', 'book_wp_epigraph_source', 'book_wp_dedication',
        'book_wp_year', 'book_wp_source', 'book_wp_original_lang',
        'book_wp_notes', 'book_wp_ornament', 'book_wp_toc_excerpt',
        'book_wp_date_composed', 'book_wp_location',
        // Syair / Karmina / verse forms
        'book_wp_occasion',
        'book_wp_inspiration',
        'book_wp_narrative_subject',
        'book_wp_metre',
        'book_wp_melody',
        'book_wp_completeness',
        'book_wp_canto_count',
        // Karmina structural labels
        'book_wp_sampiran',
        'book_wp_isi',
        'book_wp_rhyme_scheme',
        'book_wp_register',
        // Hikayat (bibliographic — private scholarly data)
        'book_wp_hikayat_title',
        'book_wp_tale_cycle',
        'book_wp_protagonist',
        'book_wp_manuscript',
        'book_wp_editor_transcriber',
        'book_wp_folio_page',
        'book_wp_annotation',        // private commentary — never expose
        // Puisi
        'book_wp_poetic_mode',
        'book_wp_sequence',
        'book_wp_performance_history',
        // Cerpen / Novel
        'book_wp_point_of_view',
        'book_wp_setting',
        'book_wp_theme',
        'book_wp_novel_title',
        'book_wp_genre',
        'book_wp_timeline_position',
        // Nukilan
        'book_wp_source_work_title',
        'book_wp_source_author',
        'book_wp_source_genre',
        // Ratapan
        'book_wp_occasion_of_grief',
        'book_wp_addressee',
        'book_wp_lament_register',
        'book_wp_duration_composed',
        // Esei
        'book_wp_thesis',
        'book_wp_abstract',
        'book_wp_field',
        'book_wp_methodology',
        'book_wp_subject',
        'book_wp_first_published',
        // Memoir
        'book_wp_period_covered',
        'book_wp_key_figures',
        'book_wp_memoir_tone',
        'book_wp_memory_type',
        'book_wp_verification_note', // private research note — never expose
    );

    foreach ( $private_fields as $key ) {
        register_post_meta( 'post', $key, array(
            'show_in_rest'  => false,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => function () {
                return current_user_can( 'edit_posts' );
            },
        ) );
    }
}

/* ── TOC Transient Cache Invalidation ───────────────────────────────────── */

/**
 * Clear the cached TOC post list.
 * Called by all hooks that can change TOC visibility or order.
 */
function book_wp_clear_toc_cache() {
    delete_transient( 'book_wp_toc_posts' );
    // Belt-and-suspenders for persistent object caches (Redis, Memcached) that
    // may not honour delete_transient() immediately. Same pattern as v3.4.82
    // fix for book_wp_form_slugs in inc/helpers/permalink.php.
    wp_cache_delete( 'book_wp_toc_posts', 'transient' );
}

// 1. Post saved (non-autosave, non-revision only) — catches title/content/date changes.
add_action( 'save_post', function( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;
    book_wp_clear_toc_cache();
} );

// 2. Publish / unpublish transitions — post appears in or disappears from TOC.
add_action( 'transition_post_status', function( $new, $old ) {
    $toc_statuses = array( 'publish' );
    if ( in_array( $new, $toc_statuses, true ) || in_array( $old, $toc_statuses, true ) ) {
        book_wp_clear_toc_cache();
    }
}, 10, 2 );

// 3. Trashed / restored — explicit UI actions that bypass transition_post_status.
add_action( 'trashed_post',   'book_wp_clear_toc_cache' );
add_action( 'untrashed_post', 'book_wp_clear_toc_cache' );

// 4. Tag assignment changes — a hidden tag being added or removed changes TOC visibility.
add_action( 'set_object_terms', function( $object_id, $terms, $tt_ids, $taxonomy ) {
    if ( 'post_tag' !== $taxonomy ) return;
    if ( 'post' !== get_post_type( $object_id ) ) return;
    book_wp_clear_toc_cache();
}, 10, 4 );

// 5. Term slug / name change for post_tag — hidden tag slugs are stored by slug,
//    so an edited slug must trigger a cache flush to rebuild the lookup.
add_action( 'edited_term', function( $term_id, $tt_id, $taxonomy ) {
    if ( 'post_tag' !== $taxonomy ) return;
    book_wp_clear_toc_cache();
}, 10, 3 );

// 6. TOC-relevant settings changed — grouping, pagination, or display options.
add_action( 'updated_option', function( $option_name ) {
    $toc_options = array(
        'book_wp_hidden_tags',
        'book_wp_toc_grouping',
        'book_wp_toc_pagination',
        'book_wp_toc_layout',
        'book_wp_archive_pagination',
        'book_wp_show_badges',
    );
    if ( in_array( $option_name, $toc_options, true ) ) {
        book_wp_clear_toc_cache();
    }
} );

/* ── Word Count Cache ───────────────────────────────────────────────────── */

// str_word_count() on a long post (hikayat, novel chapter) is a non-trivial
// string operation that was previously running uncached on every wp_head
// action for every single post page view. Storing the count in post meta on
// save means the structured data function reads a single integer from the DB
// instead of re-computing on every request. (CWV-06)
add_action( 'save_post', 'book_wp_cache_word_count' );

function book_wp_cache_word_count( $post_id ) {
    // Bail on autosave and revisions — only count on a full save.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;

    $content = get_post_field( 'post_content', $post_id );
    $count   = str_word_count( wp_strip_all_tags( $content ) );
    update_post_meta( $post_id, 'book_wp_word_count', (int) $count );
}

/* ── JSON-LD Structured Data ───────────────────────────────────────────── */

add_action( 'wp_head', 'book_wp_structured_data' );

function book_wp_structured_data() {
    if ( ! is_single() ) return;
    if ( '1' !== get_option( 'book_wp_structured_data', '1' ) ) return;

    global $post;

    $schema = array(
        '@context'      => 'https://schema.org',
        '@type'         => 'CreativeWork',
        'name'          => get_the_title(),
        'author'        => array( '@type' => 'Person', 'name' => get_the_author() ),
        'datePublished' => get_the_date( 'c' ),
        'url'           => get_permalink(),
        'inLanguage'    => substr( get_locale(), 0, 2 ),
        // Read cached word count computed on save_post (book_wp_cache_word_count).
        // Fall back to live computation if meta is absent (e.g. posts saved before
        // this version — will self-heal on next save). (CWV-06)
        'wordCount'     => ( '' !== get_post_meta( $post->ID, 'book_wp_word_count', true ) )
                             ? (int) get_post_meta( $post->ID, 'book_wp_word_count', true )
                             : str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post->ID ) ) ),
    );

    $year = get_post_meta( $post->ID, 'book_wp_year', true );
    if ( $year ) $schema['dateCreated'] = $year;

    $terms = get_the_terms( $post->ID, 'literary_form' );
    if ( $terms && ! is_wp_error( $terms ) ) $schema['genre'] = $terms[0]->name;

    $source = get_post_meta( $post->ID, 'book_wp_source', true );
    if ( $source ) $schema['isPartOf'] = array( '@type' => 'CreativeWork', 'name' => $source );

    $lang = get_post_meta( $post->ID, 'book_wp_original_lang', true );
    if ( $lang ) $schema['translationOfWork'] = array( '@type' => 'CreativeWork', 'inLanguage' => $lang );

    $hidden_slugs = book_wp_hidden_tag_slugs();
    $all_tags     = get_the_tags( $post->ID );
    if ( $all_tags && ! is_wp_error( $all_tags ) ) {
        $thematic = array_filter( $all_tags, function( $t ) use ( $hidden_slugs ) {
            return ! in_array( $t->slug, $hidden_slugs, true );
        } );
        if ( $thematic ) {
            $schema['keywords'] = implode( ', ', wp_list_pluck( $thematic, 'name' ) );
        }
    }

    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        . "</script>\n";
}

/* ── Night Mode Toggle Markup ──────────────────────────────────────────── */

add_action( 'wp_footer', 'book_wp_night_toggle_markup' );

function book_wp_night_toggle_markup() {
    if ( '1' !== get_option( 'book_wp_night_toggle', '1' ) ) return;
    ?>
    <button class="bwp-night-toggle" id="bwpNightToggle" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'book-wp' ); ?>" aria-pressed="false">
        <svg class="bwp-night-toggle__sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="5"/>
            <line x1="12" y1="1"  x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
            <line x1="4.22" y1="4.22"   x2="5.64"  y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
            <line x1="1"    y1="12"      x2="3"     y2="12"/>  <line x1="21"    y1="12"     x2="23"    y2="12"/>
            <line x1="4.22" y1="19.78"  x2="5.64"  y2="18.36"/><line x1="18.36" y1="5.64"  x2="19.78" y2="4.22"/>
        </svg>
        <svg class="bwp-night-toggle__moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
    </button>
    <?php
}

/* ── Theme Activation Hook ─────────────────────────────────────────────── */

add_action( 'after_switch_theme', 'book_wp_on_activation' );

function book_wp_on_activation() {
    book_wp_register_taxonomy();
    flush_rewrite_rules();
}

/* ── Gutenberg Block: book-wp/chapter ──────────────────────────────────── */

add_action( 'init', 'book_wp_register_blocks' );

function book_wp_register_blocks() {
    // book-wp/chapter — server-side rendered (save: null in JS)
    register_block_type(
        BOOK_WP_DIR . '/inc/blocks/chapter/block.json',
        array(
            'render_callback' => 'book_wp_render_chapter_block',
        )
    );

    // book-wp/divider — server-side rendered so the ornament SVG always
    // reflects the current Theme Options setting, not a stale saved value.
    register_block_type( 'book-wp/divider', array(
        'render_callback' => 'book_wp_render_divider_block',
        'attributes'      => array(
            'style' => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );
}

/**
 * PHP render callback for book-wp/divider block.
 * Delegates to the same book_wp_ornament() helper used by [divider] shortcode.
 *
 * @param array $attrs Block attributes.
 * @return string Rendered ornament HTML.
 */
function book_wp_render_divider_block( $attrs ) {
    $style = isset( $attrs['style'] ) && $attrs['style'] ? $attrs['style'] : '';
    return book_wp_ornament( $style ?: get_option( 'book_wp_ornament_style', 'fleuron' ) );
}

/**
 * PHP render callback for book-wp/chapter block.
 *
 * Replicates the [chapter] shortcode logic so both shortcode and block
 * produce identical HTML. Called by WordPress for every server-side render.
 *
 * @param array  $attrs    Block attributes from the editor.
 * @param string $content  Inner block HTML (empty for SSR blocks with save:null).
 * @return string          Rendered chapter HTML.
 */
function book_wp_render_chapter_block( $attrs, $content ) {
    $num   = isset( $attrs['chapterNum'] )   ? $attrs['chapterNum']   : '1';
    $title = isset( $attrs['chapterTitle'] ) ? $attrs['chapterTitle'] : '';
    $start = isset( $attrs['verseStart'] )   ? intval( $attrs['verseStart'] ) : 1;
    // Read RichText HTML, strip phantom empty <p> nodes, number the rest.
    $raw  = isset( $attrs['content'] ) ? $attrs['content'] : '';
    $html = '';
    $v    = $start;

    if ( $raw ) {
        preg_match_all( '/<p[^>]*>(.*?)<\/p>/is', wpautop( wp_kses_post( $raw ) ), $matches, PREG_SET_ORDER );
        foreach ( $matches as $m ) {
            $inner = $m[1];
            // Skip paragraphs that have no real text (Gutenberg phantom nodes)
            if ( ! strlen( trim( wp_strip_all_tags( $inner ) ) ) ) continue;
            // Verses are continuous prose — collapse line breaks to spaces
            $inner = preg_replace( '/<br\s*\/?>/i', ' ', $inner );
            $inner = preg_replace( '/\s+/', ' ', trim( $inner ) );
            $vnum  = '<span class="bwp-vnum" aria-label="'
                . esc_attr( __( 'Verse', 'book-wp' ) . ' ' . $v )
                . '" data-v="' . esc_attr( $v ) . '">' . $v . '</span>';
            $html .= '<p>' . $vnum . wp_kses_post( $inner ) . '</p>';
            $v++;
        }
    }

    // Title spans the full width above the two-column grid (optional).
    $title_html = $title
        ? '<span class="bwp-chapter__title">' . esc_html( $title ) . '</span>'
        : '';

    // Chapter num sits in its own left-column cell — NOT inside the content
    // div. CSS Grid places it beside the verse column with no text wrapping.
    $num_html = '<span class="bwp-chapter__num" aria-label="'
        . esc_attr( __( 'Chapter', 'book-wp' ) . ' ' . $num ) . '">'
        . esc_html( $num ) . '</span>';

    return '<div class="bwp-chapter">'
        . $title_html
        . $num_html
        . '<div class="bwp-chapter__content">' . $html . '</div>'
        . '</div>';
}

/* ═══════════════════════════════════════════════════════════════════════════
   TAG SUPPORT — cap enforcement, hiding, category filter, transient
   ═══════════════════════════════════════════════════════════════════════════ */

/**
 * Return the stored hidden-trigger tag IDs as an array of integers.
 * Cached in a static variable to avoid repeated option reads per request.
 *
 * @return int[]
 */
function book_wp_hidden_tag_ids() {
    static $ids = null;
    if ( null === $ids ) {
        $raw = get_option( 'book_wp_hidden_tags', '' );
        if ( $raw ) {
            $ids = array_values( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) );
        } else {
            $ids = array();
        }
    }
    return $ids;
}

/**
 * Return the hidden-trigger tag slugs as an array.
 * Resolves stored IDs to slugs so all existing call sites remain unchanged.
 * Cached in a static variable — one get_terms() call per request at most.
 *
 * @return string[]
 */
function book_wp_hidden_tag_slugs() {
    static $slugs = null;
    if ( null === $slugs ) {
        $ids = book_wp_hidden_tag_ids();
        if ( empty( $ids ) ) {
            $slugs = array();
        } else {
            $terms = get_terms( array(
                'taxonomy'   => 'post_tag',
                'include'    => $ids,
                'fields'     => 'id=>slug',
                'hide_empty' => false,
            ) );
            $slugs = ( is_array( $terms ) && ! is_wp_error( $terms ) ) ? array_values( $terms ) : array();
        }
    }
    return $slugs;
}

/**
 * One-time migration: convert legacy slug-format book_wp_hidden_tags to tag IDs.
 * Runs on admin_init so it fires before any option reads on admin pages.
 * No-ops immediately if the value is already numeric (already migrated) or empty.
 */
add_action( 'admin_init', 'book_wp_migrate_hidden_tags_to_ids' );

function book_wp_migrate_hidden_tags_to_ids() {
    $raw = get_option( 'book_wp_hidden_tags', '' );
    if ( empty( $raw ) ) return;

    $values = array_filter( explode( ',', $raw ) );
    if ( empty( $values ) ) return;

    // Already numeric → already migrated.
    if ( count( array_filter( $values, 'is_numeric' ) ) === count( $values ) ) return;

    // Values are slugs — resolve each to a term_id.
    $ids = array();
    foreach ( $values as $slug ) {
        $term = get_term_by( 'slug', sanitize_key( $slug ), 'post_tag' );
        if ( $term && ! is_wp_error( $term ) ) {
            $ids[] = (int) $term->term_id;
        }
    }
    update_option( 'book_wp_hidden_tags', implode( ',', $ids ) );
}

/**
 * Enforce the thematic tag cap on classic editor saves.
 * Hiding-trigger tags are excluded from the count and always preserved.
 *
 * @param int $post_id
 */
function book_wp_enforce_tag_cap( $post_id ) {
    // Skip revisions, autosaves, and non-post types.
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) return;
    if ( 'post' !== get_post_type( $post_id ) ) return;

    $hidden   = book_wp_hidden_tag_slugs();
    $all_tags = wp_get_post_tags( $post_id, array( 'fields' => 'all' ) );
    if ( empty( $all_tags ) || is_wp_error( $all_tags ) ) return;

    $trigger_tags  = array();
    $thematic_tags = array();

    foreach ( $all_tags as $tag ) {
        if ( in_array( $tag->slug, $hidden, true ) ) {
            $trigger_tags[] = $tag->term_id;
        } else {
            $thematic_tags[] = $tag->term_id;
        }
    }

    // Trim thematic tags to cap if needed.
    if ( count( $thematic_tags ) > BOOK_WP_MAX_TAGS ) {
        $thematic_tags = array_slice( $thematic_tags, 0, BOOK_WP_MAX_TAGS );
    }

    $final = array_merge( $thematic_tags, $trigger_tags );

    // Only rewrite if something actually changed.
    $current_ids = wp_list_pluck( $all_tags, 'term_id' );
    sort( $final );
    sort( $current_ids );
    if ( $final !== $current_ids ) {
        // Temporarily remove this action to avoid infinite recursion.
        remove_action( 'save_post', 'book_wp_enforce_tag_cap' );
        wp_set_post_tags( $post_id, $final, false );
        add_action( 'save_post', 'book_wp_enforce_tag_cap' );
    }
}
add_action( 'save_post', 'book_wp_enforce_tag_cap' );

/**
 * Enforce tag cap on block editor REST saves.
 *
 * @param WP_Post $post
 */
function book_wp_enforce_tag_cap_rest( $post ) {
    book_wp_enforce_tag_cap( $post->ID );
}
add_action( 'rest_after_insert_post', 'book_wp_enforce_tag_cap_rest' );

/**
 * Exclude hidden-tagged posts from all required public query contexts.
 * Covers: home/blog index, category, date, author, search, feed, and
 * non-hidden tag archives. The matching hidden-tag archive is allowed through.
 * Scoped to the main query only — secondary queries are handled separately.
 *
 * @param WP_Query $q
 */
function book_wp_hide_from_public_queries( $q ) {
    if ( is_admin() || ! $q->is_main_query() ) return;

    $hidden = book_wp_hidden_tag_slugs();
    if ( empty( $hidden ) ) return;

    $should_filter = false;

    if ( $q->is_home() || $q->is_category() || $q->is_date() || $q->is_author() || $q->is_search() || $q->is_feed() ) {
        $should_filter = true;
    } elseif ( $q->is_tag() ) {
        // Allow the matching hidden-tag archive through; filter all other tag archives.
        $queried           = get_queried_object();
        $is_hidden_archive = ( $queried instanceof WP_Term ) && in_array( $queried->slug, $hidden, true );
        $should_filter     = ! $is_hidden_archive;
    }

    if ( ! $should_filter ) return;

    $existing   = $q->get( 'tax_query' ) ?: array();
    $existing[] = array(
        'taxonomy' => 'post_tag',
        'field'    => 'slug',
        'terms'    => $hidden,
        'operator' => 'NOT IN',
    );
    $q->set( 'tax_query', $existing );
}
add_action( 'pre_get_posts', 'book_wp_hide_from_public_queries' );

/**
 * Exclude hidden-tagged posts from sidebar widget secondary queries.
 * Only fires while WordPress is actively rendering a widget area, so the theme's
 * own secondary queries (TOC transient fill, navigation helpers) are unaffected.
 *
 * @param WP_Query $q
 */
function book_wp_hide_from_widget_queries( $q ) {
    if ( is_admin() || $q->is_main_query() ) return;
    if ( ! doing_action( 'dynamic_sidebar' ) ) return;

    $hidden = book_wp_hidden_tag_slugs();
    if ( empty( $hidden ) ) return;

    $existing   = $q->get( 'tax_query' ) ?: array();
    $existing[] = array(
        'taxonomy' => 'post_tag',
        'field'    => 'slug',
        'terms'    => $hidden,
        'operator' => 'NOT IN',
    );
    $q->set( 'tax_query', $existing );
}
add_action( 'pre_get_posts', 'book_wp_hide_from_widget_queries' );

/**
 * Exclude hidden-tagged posts from REST API post queries on public (unauthenticated)
 * requests. Editors and administrators retain full access.
 *
 * @param array           $args    WP_Query args.
 * @param WP_REST_Request $request REST request object.
 * @return array
 */
function book_wp_hide_from_rest_query( $args, $request ) {
    if ( current_user_can( 'edit_posts' ) ) return $args;

    $hidden = book_wp_hidden_tag_slugs();
    if ( empty( $hidden ) ) return $args;

    $existing   = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
    $existing[] = array(
        'taxonomy' => 'post_tag',
        'field'    => 'slug',
        'terms'    => $hidden,
        'operator' => 'NOT IN',
    );
    $args['tax_query'] = $existing;
    return $args;
}
add_filter( 'rest_post_query', 'book_wp_hide_from_rest_query', 10, 2 );

/**
 * Exclude hidden-tagged posts from WordPress core XML sitemaps (WP 5.5+).
 * The core sitemap iterates posts via WP_Query directly, bypassing the main
 * query conditional checks used in book_wp_hide_from_public_queries().
 *
 * @param array $args   WP_Query args for the sitemap posts query.
 * @param string $post_type Post type being sitemapped.
 * @return array
 */
function book_wp_hide_from_sitemap( $args, $post_type ) {
    if ( 'post' !== $post_type ) return $args;

    $hidden = book_wp_hidden_tag_slugs();
    if ( empty( $hidden ) ) return $args;

    $existing   = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
    $existing[] = array(
        'taxonomy' => 'post_tag',
        'field'    => 'slug',
        'terms'    => $hidden,
        'operator' => 'NOT IN',
    );
    $args['tax_query'] = $existing;
    return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'book_wp_hide_from_sitemap', 10, 2 );

/**
 * Exclude hidden-tagged posts from the RankMath HTML Sitemap.
 * RankMath's HTML sitemap module runs its own WP_Query, bypassing both the
 * main query (pre_get_posts) and the core XML sitemap filter above. This
 * filter targets the RankMath-specific query args hook.
 *
 * @param array $args WP_Query args for RankMath's HTML sitemap post listing.
 * @return array
 */
function book_wp_hide_from_rankmath_html_sitemap( $args ) {
    $hidden = book_wp_hidden_tag_slugs();
    if ( empty( $hidden ) ) return $args;

    $existing   = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
    $existing[] = array(
        'taxonomy' => 'post_tag',
        'field'    => 'slug',
        'terms'    => $hidden,
        'operator' => 'NOT IN',
    );
    $args['tax_query'] = $existing;
    return $args;
}
add_filter( 'rank_math/sitemap/html_sitemap/post_args', 'book_wp_hide_from_rankmath_html_sitemap' );

/**
 * Suppress hidden tags from the WordPress Tag Cloud widget.
 * The widget calls get_terms() with its own args; filtering via pre_get_posts
 * has no effect on it. This filter removes hidden tag IDs from the include list
 * (or adds an explicit exclude list) so hidden tags never appear in tag clouds.
 *
 * @param array $args Tag cloud args.
 * @return array
 */
function book_wp_hide_tags_from_cloud( $args ) {
    $ids = book_wp_hidden_tag_ids();
    if ( empty( $ids ) ) return $args;

    $existing_exclude = isset( $args['exclude'] ) ? wp_parse_id_list( $args['exclude'] ) : array();
    $args['exclude']  = implode( ',', array_unique( array_merge( $existing_exclude, $ids ) ) );
    return $args;
}
add_filter( 'widget_tag_cloud_args', 'book_wp_hide_tags_from_cloud' );

/* ── Social Meta Tags (OpenGraph / Twitter Card) ───────────────────────── */

/**
 * Detect whether a known SEO plugin is active.
 * If so, Book WP defers all OG/Twitter meta output to that plugin.
 * Covers: RankMath, Yoast SEO, All in One SEO, SEOPress, The SEO Framework.
 *
 * @return bool
 */
function book_wp_seo_plugin_active() {
    return defined( 'RANK_MATH_VERSION' )             // RankMath
        || defined( 'WPSEO_VERSION' )                 // Yoast SEO / Yoast SEO Premium
        || defined( 'AIOSEO_VERSION' )                // All in One SEO
        || defined( 'SEOPRESS_VERSION' )              // SEOPress
        || class_exists( 'The_SEO_Framework\Load' );  // The SEO Framework
}

/**
 * Output OpenGraph and Twitter Card meta tags in <head> on single posts.
 *
 * Bails silently if:
 * - Not a single post page.
 * - The option is disabled in Appearance > Book WP > Advanced.
 * - A known SEO plugin is active (RankMath, Yoast, etc.) — defers to that plugin.
 *
 * Description source priority: book_wp_toc_excerpt → auto-trim post content (30 words).
 * Image source: featured image at book-wp-cover size (800×400).
 * Twitter handle: resolved from book_wp_social_twitter URL already stored in Social tab.
 */
add_action( 'wp_head', 'book_wp_social_meta', 5 );

function book_wp_social_meta() {
    if ( ! is_single() ) return;
    if ( '1' !== get_option( 'book_wp_social_meta_tags', '1' ) ) return;
    if ( book_wp_seo_plugin_active() ) return;

    global $post;

    // ── Core values ────────────────────────────────────────────────────────
    $title     = get_the_title();
    $url       = get_permalink();
    $site_name = get_bloginfo( 'name' );
    $locale    = get_locale(); // e.g. ms_MY — already in correct OG format

    // Description: toc_excerpt first; fall back to trimmed post content.
    $description = trim( (string) get_post_meta( $post->ID, 'book_wp_toc_excerpt', true ) );
    if ( ! $description ) {
        $description = wp_trim_words(
            wp_strip_all_tags( get_post_field( 'post_content', $post->ID ) ),
            30,
            "\xe2\x80\xa6" // …
        );
    }

    // ── Featured image ─────────────────────────────────────────────────────
    $image_url = '';
    $image_w   = 0;
    $image_h   = 0;
    if ( has_post_thumbnail( $post->ID ) ) {
        $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'book-wp-cover' );
        if ( $img ) {
            $image_url = $img[0];
            $image_w   = (int) $img[1];
            $image_h   = (int) $img[2];
        }
    }

    // ── Article meta ───────────────────────────────────────────────────────
    $form_label = book_wp_get_form_label( $post->ID );

    $hidden_slugs  = book_wp_hidden_tag_slugs();
    $all_tags      = get_the_tags( $post->ID );
    $thematic_tags = array();
    if ( $all_tags && ! is_wp_error( $all_tags ) ) {
        $thematic_tags = array_values( array_filter( $all_tags, function( $t ) use ( $hidden_slugs ) {
            return ! in_array( $t->slug, $hidden_slugs, true );
        } ) );
    }

    // ── Twitter handle ─────────────────────────────────────────────────────
    $twitter_handle = '';
    $twitter_url    = get_option( 'book_wp_social_twitter', '' );
    if ( $twitter_url ) {
        // Accept https://twitter.com/handle or https://x.com/handle
        if ( preg_match( '#(?:twitter\.com|x\.com)/(@?[A-Za-z0-9_]{1,50})#', $twitter_url, $m ) ) {
            $twitter_handle = '@' . ltrim( $m[1], '@' );
        }
    }

    // ── Output ─────────────────────────────────────────────────────────────
    echo "\n<!-- Book WP: Social Meta (og / twitter) -->\n";

    // OpenGraph — base
    echo '<meta property="og:type" content="article" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '" />' . "\n";

    // OpenGraph — image (only when a featured image exists)
    if ( $image_url ) {
        echo '<meta property="og:image" content="' . esc_url( $image_url ) . '" />' . "\n";
        if ( $image_w ) echo '<meta property="og:image:width" content="' . $image_w . '" />' . "\n";
        if ( $image_h ) echo '<meta property="og:image:height" content="' . $image_h . '" />' . "\n";
        echo '<meta property="og:image:alt" content="' . esc_attr( $title ) . '" />' . "\n";
    }

    // OpenGraph — article namespace
    echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '" />' . "\n";
    echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '" />' . "\n";
    echo '<meta property="article:author" content="' . esc_attr( get_the_author() ) . '" />' . "\n";
    if ( $form_label ) {
        echo '<meta property="article:section" content="' . esc_attr( $form_label ) . '" />' . "\n";
    }
    foreach ( $thematic_tags as $tag ) {
        echo '<meta property="article:tag" content="' . esc_attr( $tag->name ) . '" />' . "\n";
    }

    // Twitter Card
    $card_type = $image_url ? 'summary_large_image' : 'summary';
    echo '<meta name="twitter:card" content="' . esc_attr( $card_type ) . '" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
    if ( $image_url ) {
        echo '<meta name="twitter:image" content="' . esc_url( $image_url ) . '" />' . "\n";
        echo '<meta name="twitter:image:alt" content="' . esc_attr( $title ) . '" />' . "\n";
    }
    if ( $twitter_handle ) {
        echo '<meta name="twitter:site" content="' . esc_attr( $twitter_handle ) . '" />' . "\n";
    }

    echo "<!-- /Book WP: Social Meta -->\n\n";
}
