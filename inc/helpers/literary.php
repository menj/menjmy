<?php
/**
 * Sasterawan — Literary Form Helpers & Ornaments
 *
 * Form slug/label resolution, poetry detection, form descriptions,
 * and the decorative typographic ornament renderer.
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

/* ── Form helpers ─────────────────────────────────────────────────────── */

function book_wp_get_form_slug( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $terms = get_the_terms( $post_id, 'literary_form' );
    return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : '';
}

function book_wp_get_form_label( $post_id = 0 ) {
    $slug = book_wp_get_form_slug( $post_id );
    return $slug ? book_wp_form_label( $slug ) : '';
}

/**
 * Return the translated display label for a literary form slug.
 *
 * All strings are registered with WordPress gettext so translators
 * see them via Poedit / Loco Translate. The slugs are the taxonomy
 * term slugs registered in actions.php.
 *
 * @param  string $slug  Literary form taxonomy slug.
 * @return string        Translated label, or the slug itself as fallback.
 */
function book_wp_form_label( $slug ) {
    $labels = array(
        'puisi'   => __( 'Poetry',        'book-wp' ),
        'syair'   => __( 'Syair',         'book-wp' ),
        'karmina' => __( 'Karmina',       'book-wp' ),
        'hikayat' => __( 'Hikayat',       'book-wp' ),
        'cerpen'  => __( 'Short Story',   'book-wp' ),
        'novel'   => __( 'Novel',         'book-wp' ),
        'nukilan' => __( 'Excerpts',      'book-wp' ),
        'ratapan' => __( 'Lamentations',  'book-wp' ),
        'esei'    => __( 'Essay',         'book-wp' ),
        'memoir'  => __( 'Memoir',        'book-wp' ),
        'terjemahan' => __( 'Translation', 'book-wp' ),
    );
    return isset( $labels[ $slug ] ) ? $labels[ $slug ] : $slug;
}

/**
 * Jawi spelling of a literary form, used as the faint watermark behind a
 * work's opening. Decorative only (aria-hidden via CSS generated content).
 *
 * @param  string $slug Literary form slug.
 * @return string       Jawi string, or '' when unknown.
 */
function book_wp_form_jawi( $slug ) {
    $jawi = array(
        'puisi'      => 'ڤويسي',
        'syair'      => 'شعير',
        'karmina'    => 'كرمينا',
        'hikayat'    => 'حكايت',
        'cerpen'     => 'چرڤين',
        'novel'      => 'نوۏيل',
        'nukilan'    => 'نوكيلن',
        'ratapan'    => 'راتڤن',
        'esei'       => 'ايسي',
        'memoir'     => 'ميموار',
        'terjemahan' => 'ترجمهن',
    );
    $jawi = apply_filters( 'book_wp_form_jawi', $jawi );
    return isset( $jawi[ $slug ] ) ? $jawi[ $slug ] : '';
}

/**
 * Short context label for the centre of the site header:
 * "Puisi · Sanggahan" on a work, the archive type on archives, the tagline at home.
 */
function book_wp_header_section_label() {
    if ( is_singular( 'post' ) ) {
        $parts = array();
        $label = book_wp_get_form_label();
        if ( $label ) $parts[] = $label;
        $cats = get_the_category();
        if ( $cats && ! is_wp_error( $cats ) && 'uncategorized' !== $cats[0]->slug ) $parts[] = $cats[0]->name;
        return implode( ' · ', $parts );
    }
    if ( is_category() )  return __( 'Jalur', 'book-wp' ) . ' · ' . single_cat_title( '', false );
    if ( is_tax( 'literary_form' ) ) return __( 'Form', 'book-wp' ) . ' · ' . single_term_title( '', false );
    if ( is_tag() )       return __( 'Tag', 'book-wp' ) . ' · ' . single_tag_title( '', false );
    if ( is_search() )    return __( 'Search', 'book-wp' );
    if ( is_page() )      return get_the_title();
    if ( is_home() || is_front_page() ) return get_bloginfo( 'description' );
    return '';
}

/**
 * Translation apparatus shown above a translated work: source title, author,
 * year and language, plus the translator's note.
 */
function book_wp_terjemahan_block( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    // Only translations carry this apparatus; nukilan share the source-author key.
    if ( ! book_wp_has_form( $post_id, 'terjemahan' ) ) return '';
    $title  = get_post_meta( $post_id, 'book_wp_source_title',  true );
    $author = get_post_meta( $post_id, 'book_wp_source_author', true );
    $year   = get_post_meta( $post_id, 'book_wp_source_year',   true );
    $lang   = get_post_meta( $post_id, 'book_wp_original_lang', true );
    $note   = get_post_meta( $post_id, 'book_wp_translator_note', true );
    if ( ! $title && ! $author && ! $note ) return '';

    $src = array_filter( array(
        $title  ? '“' . $title . '”' : '',
        $author,
        $year,
        $lang ? sprintf( __( 'from %s', 'book-wp' ), $lang ) : '',
    ) );
    $html  = '<div class="bwp-terjemahan">';
    $html .= '<span class="bwp-terjemahan__label">' . esc_html__( 'Translated from', 'book-wp' ) . '</span>';
    if ( $src )  $html .= '<p class="bwp-terjemahan__source">' . esc_html( implode( ' · ', $src ) ) . '</p>';
    if ( $note ) $html .= '<p class="bwp-terjemahan__note">' . esc_html__( "Translator's note", 'book-wp' ) . ': ' . esc_html( $note ) . '</p>';
    return $html . '</div>';
}

/**
 * Every literary_form slug on a post. A translation is filed under
 * terjemahan *and* its native form (puisi, cerpen…), so helpers that care
 * about verse versus prose look at all of them, not just the first.
 *
 * @return string[]
 */
function book_wp_get_form_slugs( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $terms = get_the_terms( $post_id, 'literary_form' );
    if ( ! $terms || is_wp_error( $terms ) ) return array();
    return array_values( array_map( function( $t ) { return $t->slug; }, $terms ) );
}

function book_wp_has_form( $post_id, $slug ) {
    return in_array( $slug, book_wp_get_form_slugs( $post_id ), true );
}

function book_wp_is_poetry( $post_id = 0 ) {
    $verse = array( 'puisi', 'syair', 'karmina', 'ratapan' );
    return (bool) array_intersect( $verse, book_wp_get_form_slugs( $post_id ) );
}

function book_wp_form_description( $slug ) {
    $desc = array(
        'syair'   => __( 'Syair is a four-line monorhyme verse form of Persian origin, typically narrative or didactic in character and traditionally sung.', 'book-wp' ),
        'karmina' => __( 'Karmina or pantun dua kerat consists of two lines — the image and the meaning — the most compressed form in the Malay poetic tradition.', 'book-wp' ),
        'hikayat' => __( 'Hikayat is the dominant classical Malay narrative prose genre, recounting tales of heroes, royalty, and legend, shaped by Persian, Arabic, and Hindu traditions.', 'book-wp' ),
        'puisi'   => __( 'Poetry is a literary form combining figurative language, rhythm, and emotion in structured lines and stanzas.', 'book-wp' ),
        'cerpen'  => __( 'Cerpen is a short prose fiction work focused on a single event, character, or mood within a compact narrative.', 'book-wp' ),
        'novel'   => __( 'A novel is a long-form prose fiction work exploring character, plot, and theme in depth across sequential chapters.', 'book-wp' ),
        'nukilan' => __( 'Nukilan are curated passages from prose works, speeches, or texts that carry standalone literary significance.', 'book-wp' ),
        'ratapan' => __( 'Ratapan are poetic expressions of grief or suffering, often connected to loss, longing, or mourning.', 'book-wp' ),
        'esei'    => __( "An essay is a non-fiction prose work presenting the author's argument, analysis, or personal reflection on a subject.", 'book-wp' ),
        'memoir'  => __( "Autobiografi or memoir is the author's personal account of their own lived experience, written in the first person.", 'book-wp' ),
        'terjemahan' => __( 'Terjemahan are works carried from another language into Malay — poems and short stories alike — each with its source and a translator\'s note.', 'book-wp' ),
    );
    return isset( $desc[ $slug ] ) ? $desc[ $slug ] : '';
}

/* ── Ornament renderer — 7 decorative typographic styles ─────────────── */

function book_wp_ornament( $style = '' ) {
    if ( empty( $style ) ) $style = get_option( 'book_wp_ornament_style', 'fleuron' );
    $map = array(
        'fleuron'  => '<svg viewBox="0 0 64 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M32 4 C24 4 16 8 16 12 C16 16 24 20 32 20 C40 20 48 16 48 12 C48 8 40 4 32 4 Z M32 8 C38 8 44 10 44 12 C44 14 38 16 32 16 C26 16 20 14 20 12 C20 10 26 8 32 8 Z M8 12 C8 12 12 10 16 12 M56 12 C56 12 52 10 48 12"/></svg>',
        'asterism' => '<svg viewBox="0 0 80 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><text x="4"  y="19" font-size="20" font-family="serif">*</text><text x="30" y="19" font-size="20" font-family="serif">*</text><text x="56" y="19" font-size="20" font-family="serif">*</text></svg>',
        'dinkus'   => '<svg viewBox="0 0 80 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="16" cy="12" r="2.5"/><circle cx="40" cy="12" r="2.5"/><circle cx="64" cy="12" r="2.5"/></svg>',
        'dash'     => '<svg viewBox="0 0 64 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><line x1="8" y1="12" x2="56" y2="12" stroke="currentColor" stroke-width="1" fill="none"/></svg>',
        'leaf'     => '<svg viewBox="0 0 48 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M24 4 C12 4 6 10 8 18 C14 14 20 13 24 20 C28 13 34 14 40 18 C42 10 36 4 24 4 Z"/><line x1="24" y1="20" x2="24" y2="4" stroke="currentColor" stroke-width=".8" fill="none" opacity=".4"/></svg>',
        'flower'   => '<svg viewBox="0 0 48 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="24" cy="12" r="3"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(0   24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(60  24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(120 24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(180 24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(240 24 12)"/><ellipse cx="24" cy="5" rx="2.5" ry="3.5" transform="rotate(300 24 12)"/></svg>',
        'diamond'  => '<svg viewBox="0 0 80 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><polygon points="24,4 30,12 24,20 18,12"/><line x1="32" y1="12" x2="48" y2="12" stroke="currentColor" stroke-width="1" fill="none" opacity=".4"/><line x1="0" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="1" fill="none" opacity=".4"/><polygon points="56,4 62,12 56,20 50,12"/></svg>',
        'none'     => '',
    );
    $svg = isset( $map[ $style ] ) ? $map[ $style ] : $map['fleuron'];
    if ( empty( $svg ) ) return '';
    return '<div class="bwp-ornament bwp-ornament--' . esc_attr( $style ) . '" aria-hidden="true">' . $svg . '</div>';
}

/* ── Footer sign-off defaults (Di Bawah Permukaan, Mukadimah) ─────────── */

function book_wp_default_footer_quote() {
    return 'Yang tampak hanyalah permukaan, sedang arus kesedaran diam-diam mengubah haluan, mengendapkan pengalaman; di sini, bahasa bergerak perlahan, menyentuh yang halus, menyusup ke kedalaman.';
}
function book_wp_default_footer_quote_sub() {
    return '';
}

/**
 * Javanese rendering of the anthology name: a translation, not a
 * transliteration. "Sangisoring Lumahan" = beneath the surface (ngoko,
 * literary register). Shown in Aksara Jawa in the footer identity block.
 */
function book_wp_default_jawa_name() {
    return 'ꦱꦔꦶꦱꦺꦴꦫꦶꦁꦭꦸꦩꦲꦤ꧀';
}

/**
 * Duotone portrait medallion. Size: 'sm' (56px), 'md' (120px), 'lg' (240px).
 * The duotone is CSS (grayscale + screen blend over nila), so any photo works.
 */
function book_wp_author_portrait( $size = 'md' ) {
    $url = get_option( 'book_wp_author_portrait', BOOK_WP_URI . '/assets/img/brand/portrait.png' );
    if ( ! $url ) return '';
    $name = get_option( 'book_wp_author_name', '' );
    $name = $name ? $name : get_bloginfo( 'name' );
    $size = in_array( $size, array( 'sm', 'md', 'lg' ), true ) ? $size : 'md';
    return '<span class="bwp-portrait bwp-portrait--' . esc_attr( $size ) . '">'
        . '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $name ) . '" loading="lazy" decoding="async">'
        . '</span>';
}

/**
 * Front page: the newest work (or the pinned one from settings) presented as
 * an opening page — form badge, title, first stanza or lines, dateline.
 */
function book_wp_featured_work() {
    if ( '1' !== get_option( 'book_wp_show_featured_work', '1' ) ) return '';
    $pinned = (int) get_option( 'book_wp_featured_work_id', 0 );
    $args = array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1, 'ignore_sticky_posts' => true );
    if ( $pinned ) $args['p'] = $pinned;
    $hidden = book_wp_hidden_tag_slugs();
    if ( ! $pinned && $hidden ) $args['tax_query'] = array( array( 'taxonomy' => 'post_tag', 'field' => 'slug', 'terms' => $hidden, 'operator' => 'NOT IN' ) );
    $q = new WP_Query( $args );
    if ( ! $q->have_posts() ) return '';
    $post = $q->posts[0];
    $id   = $post->ID;
    $slug = book_wp_get_form_slug( $id );
    $cats = get_the_category( $id );
    $cat  = ( $cats && 'uncategorized' !== $cats[0]->slug ) ? $cats[0] : null;

    // First passage: first non-empty paragraph, line breaks kept for verse.
    $content = apply_filters( 'the_content', $post->post_content );
    $content = book_wp_remove_chapter_blocks( $content );
    preg_match( '/<p(?:\s[^>]*)?>(.*?)<\/p>/si', $content, $m );
    $first = isset( $m[1] ) ? $m[1] : wp_strip_all_tags( $content );
    $first = wp_kses( $first, array( 'br' => array(), 'em' => array(), 'strong' => array() ) );
    if ( ! book_wp_is_poetry( $id ) ) $first = wp_trim_words( wp_strip_all_tags( $first ), 48, '…' );

    $loc  = get_post_meta( $id, 'book_wp_location', true );
    $date = get_post_meta( $id, 'book_wp_date_composed', true );
    $year = get_post_meta( $id, 'book_wp_year', true );
    $dateline = implode( ', ', array_filter( array( $loc, $date ? $date : $year ) ) );

    $html  = '<section class="bwp-featured" aria-labelledby="bwp-featured-title">';
    $html .= '<p class="bwp-featured__kicker">' . ( $pinned ? esc_html__( 'Karya pilihan', 'book-wp' ) : esc_html__( 'Karya terbaharu', 'book-wp' ) ) . '</p>';
    $html .= '<div class="bwp-featured__grid">';
    $html .= '<div class="bwp-featured__head">';
    $html .= '<div class="bwp-analytics">';
    if ( $slug ) $html .= '<span class="bwp-badge bwp-badge--' . esc_attr( $slug ) . '">' . esc_html( book_wp_form_label( $slug ) ) . '</span>';
    if ( $cat )  $html .= '<a class="bwp-analytics__item bwp-analytics__cat" href="' . esc_url( get_category_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
    $html .= '<span class="bwp-analytics__item">' . esc_html( book_wp_reading_time( $id ) ) . '</span>';
    $html .= '</div>';
    $html .= '<h2 class="bwp-featured__title" id="bwp-featured-title"><a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_html( get_the_title( $id ) ) . '</a></h2>';
    if ( $dateline ) $html .= '<p class="bwp-featured__dateline">' . esc_html( $dateline ) . '</p>';
    $html .= '</div>';
    $html .= '<div class="bwp-featured__body' . ( book_wp_is_poetry( $id ) ? ' bwp-featured__body--verse' : '' ) . '">';
    $html .= '<p>' . $first . '</p>';
    $html .= '<a class="bwp-featured__more" href="' . esc_url( get_permalink( $id ) ) . '">' . esc_html__( 'Baca seterusnya', 'book-wp' ) . ' <span aria-hidden="true">&rarr;</span></a>';
    $html .= '</div></div></section>';
    wp_reset_postdata();
    return $html;
}

/**
 * Front-page cover photograph: the owner's Media Library URL, or the bundled
 * toned underwater photograph (David Boca, Pexels — see assets/img/hero/CREDITS.txt).
 * Returns an empty array when the cover is switched off.
 */
function book_wp_hero_image() {
    if ( '1' !== get_option( 'book_wp_show_hero_image', '1' ) ) {
        return array();
    }
    $custom = esc_url( get_option( 'book_wp_hero_image', '' ) );
    if ( $custom ) {
        return array( 'src' => $custom, 'srcset' => '' );
    }
    $base = BOOK_WP_URI . '/assets/img/hero/';
    return array(
        'src'    => $base . 'di-bawah-permukaan.jpg',
        'srcset' => $base . 'di-bawah-permukaan-sm.jpg 1000w, ' . $base . 'di-bawah-permukaan.jpg 2000w',
    );
}

