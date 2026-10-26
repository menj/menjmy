<?php
/**
 * Book WP — Literary Form Helpers & Ornaments
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
    );
    return isset( $labels[ $slug ] ) ? $labels[ $slug ] : $slug;
}

function book_wp_is_poetry( $post_id = 0 ) {
    return in_array(
        book_wp_get_form_slug( $post_id ),
        array( 'puisi', 'syair', 'karmina', 'ratapan' ),
        true
    );
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
