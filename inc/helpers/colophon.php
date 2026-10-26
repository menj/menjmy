<?php
/**
 * Sasterawan — Colophon, Epigraph & Dedication
 *
 * Renders the epigraph (opening quotation), dedication, and the
 * full colophon footer with form-specific literary metadata.
 *
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

function book_wp_epigraph( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $text   = get_post_meta( $post_id, 'book_wp_epigraph',        true );
    $source = get_post_meta( $post_id, 'book_wp_epigraph_source', true );
    if ( empty( $text ) ) return '';
    $html = '<blockquote class="bwp-epigraph"><div class="bwp-epigraph__text">'
        . wp_kses_post( nl2br( $text ) ) . '</div>';
    if ( $source ) {
        $html .= '<cite class="bwp-epigraph__source">&mdash; ' . esc_html( $source ) . '</cite>';
    }
    return $html . '</blockquote>';
}

function book_wp_dedication( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $text = get_post_meta( $post_id, 'book_wp_dedication', true );
    if ( empty( $text ) ) return '';
    return '<p class="bwp-dedication">' . esc_html( $text ) . '</p>';
}

function book_wp_colophon( $post_id = 0 ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $form = book_wp_get_form_slug( $post_id );

    // ── Fetch all meta in one pass ────────────────────────────────────────
    $m = array();
    foreach ( array(
        // Core
        'book_wp_date_composed', 'book_wp_location', 'book_wp_year',
        'book_wp_source', 'book_wp_original_lang', 'book_wp_notes',
        // Syair
        'book_wp_narrative_subject', 'book_wp_metre', 'book_wp_melody',
        'book_wp_completeness', 'book_wp_canto_count', 'book_wp_occasion',
        // Karmina
        'book_wp_sampiran', 'book_wp_isi', 'book_wp_rhyme_scheme', 'book_wp_register',
        // Hikayat
        'book_wp_hikayat_title', 'book_wp_tale_cycle', 'book_wp_protagonist',
        'book_wp_manuscript',
        'book_wp_editor_transcriber', 'book_wp_folio_page',
        // Puisi
        'book_wp_poetic_mode', 'book_wp_performance_history', 'book_wp_sequence',
        // Cerpen / Novel
        'book_wp_point_of_view', 'book_wp_setting',
        'book_wp_theme', 'book_wp_first_published', 'book_wp_novel_title',
        'book_wp_genre', 'book_wp_timeline_position',
        'book_wp_chapter_number', 'book_wp_chapter_part',
        // Nukilan
        'book_wp_source_work_title', 'book_wp_source_author', 'book_wp_source_genre',
        'book_wp_annotation',
        // Ratapan
        'book_wp_occasion_of_grief', 'book_wp_addressee', 'book_wp_lament_register',
        // Esei
        'book_wp_thesis', 'book_wp_abstract', 'book_wp_field',
        'book_wp_methodology', 'book_wp_subject',
        // Memoir
        'book_wp_period_covered', 'book_wp_key_figures',
        'book_wp_memoir_tone', 'book_wp_memory_type',
    ) as $key ) {
        $m[ $key ] = get_post_meta( $post_id, $key, true );
    }

    // Bail early if nothing to show.
    if ( ! array_filter( $m ) ) return '';

    $orn  = get_post_meta( $post_id, 'book_wp_ornament', true );
    $html = '<footer class="bwp-colophon">' . book_wp_ornament( $orn ?: '' );

    // ── Dateline — universal ──────────────────────────────────────────────
    if ( $m['book_wp_date_composed'] && $m['book_wp_location'] ) {
        $html .= '<p class="bwp-colophon__line bwp-colophon__line--dateline">'
            . esc_html( $m['book_wp_location'] ) . ', '
            . esc_html( $m['book_wp_date_composed'] ) . '</p>';
    } elseif ( $m['book_wp_date_composed'] ) {
        $html .= '<p class="bwp-colophon__line">' . esc_html( __( 'Year composed', 'book-wp' ) )
            . ': ' . esc_html( $m['book_wp_date_composed'] ) . '</p>';
    } elseif ( $m['book_wp_location'] ) {
        $html .= '<p class="bwp-colophon__line">&#x1F4CD; ' . esc_html( $m['book_wp_location'] ) . '</p>';
    } elseif ( $m['book_wp_year'] ) {
        $html .= '<p class="bwp-colophon__line">' . esc_html( __( 'Year composed', 'book-wp' ) )
            . ': ' . esc_html( $m['book_wp_year'] ) . '</p>';
    }

    if ( $m['book_wp_source'] ) {
        $html .= '<p class="bwp-colophon__line">' . esc_html( __( 'Source', 'book-wp' ) )
            . ': <em>' . esc_html( $m['book_wp_source'] ) . '</em></p>';
    }
    if ( $m['book_wp_original_lang'] ) {
        $html .= '<p class="bwp-colophon__line">' . esc_html( __( 'Translated from', 'book-wp' ) )
            . ': ' . esc_html( $m['book_wp_original_lang'] ) . '</p>';
    }

    // ── Syair ─────────────────────────────────────────────────────────────
    if ( 'syair' === $form ) {
        if ( $m['book_wp_narrative_subject'] ) {
            $html .= '<p class="bwp-colophon__line bwp-colophon__line--subject">'
                . esc_html( $m['book_wp_narrative_subject'] ) . '</p>';
        }
        $attr = array_filter( array( $m['book_wp_metre'], $m['book_wp_rhyme_scheme'] ) );
        if ( $attr ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( implode( ' &middot; ', $attr ) ) . '</p>';
        if ( $m['book_wp_completeness'] && 'complete' !== $m['book_wp_completeness'] ) {
            $html .= '<p class="bwp-colophon__line">' . esc_html( ucfirst( $m['book_wp_completeness'] ) ) . '</p>';
        }
        if ( $m['book_wp_canto_count'] ) $html .= '<p class="bwp-colophon__line">'
        . esc_html( $m['book_wp_canto_count'] ) . ' ' . esc_html__( 'cantos', 'book-wp' ) . '</p>';
        if ( $m['book_wp_melody'] )  $html .= '<p class="bwp-colophon__line">'
            . esc_html__( 'Melody', 'book-wp' ) . ': ' . esc_html( $m['book_wp_melody'] ) . '</p>';
        if ( $m['book_wp_occasion'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html( $m['book_wp_occasion'] ) . '</p>';
    }

    // ── Karmina ───────────────────────────────────────────────────────────
    if ( 'karmina' === $form ) {
        if ( $m['book_wp_sampiran'] ) {
            $html .= '<div class="bwp-colophon__karmina">'
                . '<p class="bwp-colophon__karmina-label">' . esc_html__( 'Sampiran', 'book-wp' ) . '</p>'
                . '<p class="bwp-colophon__karmina-line">' . esc_html( $m['book_wp_sampiran'] ) . '</p>'
                . '<p class="bwp-colophon__karmina-label">' . esc_html__( 'Isi', 'book-wp' ) . '</p>'
                . '<p class="bwp-colophon__karmina-line">' . esc_html( $m['book_wp_isi'] ) . '</p>'
                . '</div>';
        }
        if ( $m['book_wp_rhyme_scheme'] ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html__( 'Rhyme', 'book-wp' ) . ': ' . esc_html( $m['book_wp_rhyme_scheme'] ) . '</p>';
        if ( $m['book_wp_register'] )  $html .= '<p class="bwp-colophon__line">'
            . esc_html( $m['book_wp_register'] ) . '</p>';
        if ( $m['book_wp_occasion'] )  $html .= '<p class="bwp-colophon__line">'
            . esc_html( $m['book_wp_occasion'] ) . '</p>';
    }

    // ── Hikayat ───────────────────────────────────────────────────────────
    if ( 'hikayat' === $form ) {
        if ( $m['book_wp_hikayat_title'] ) $html .= '<p class="bwp-colophon__line"><em>'
            . esc_html( $m['book_wp_hikayat_title'] ) . '</em></p>';
        if ( $m['book_wp_tale_cycle'] ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( $m['book_wp_tale_cycle'] ) . '</p>';
        if ( $m['book_wp_protagonist'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html__( 'Protagonist', 'book-wp' ) . ': ' . esc_html( $m['book_wp_protagonist'] ) . '</p>';
        if ( $m['book_wp_manuscript'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html__( 'Manuscript', 'book-wp' ) . ': ' . esc_html( $m['book_wp_manuscript'] ) . '</p>';
        if ( $m['book_wp_editor_transcriber'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html__( 'Editor', 'book-wp' ) . ': ' . esc_html( $m['book_wp_editor_transcriber'] ) . '</p>';
        if ( $m['book_wp_folio_page'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html__( 'Folio', 'book-wp' ) . ': ' . esc_html( $m['book_wp_folio_page'] ) . '</p>';
    }

    // ── Puisi ─────────────────────────────────────────────────────────────
    if ( 'puisi' === $form ) {
        if ( $m['book_wp_poetic_mode'] ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( $m['book_wp_poetic_mode'] ) . '</p>';
        $attr = array_filter( array( $m['book_wp_rhyme_scheme'] ) );
        if ( $attr ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( implode( ' &middot; ', $attr ) ) . '</p>';
        if ( $m['book_wp_sequence'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html__( 'Series', 'book-wp' ) . ': ' . esc_html( $m['book_wp_sequence'] ) . '</p>';
        if ( $m['book_wp_performance_history'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html__( 'Performance', 'book-wp' ) . ': ' . esc_html( $m['book_wp_performance_history'] ) . '</p>';
        if ( $m['book_wp_first_published'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html( $m['book_wp_first_published'] ) . '</p>';
    }

    // ── Cerpen ────────────────────────────────────────────────────────────
    if ( 'cerpen' === $form ) {
        if ( $m['book_wp_theme'] ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--subject">'
            . esc_html( $m['book_wp_theme'] ) . '</p>';
        $attr = array_filter( array( $m['book_wp_point_of_view'], $m['book_wp_setting'] ) );
        if ( $attr ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( implode( ' &middot; ', $attr ) ) . '</p>';
        if ( $m['book_wp_first_published'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html( $m['book_wp_first_published'] ) . '</p>';
    }

    // ── Novel ─────────────────────────────────────────────────────────────
    if ( 'novel' === $form ) {
        if ( $m['book_wp_novel_title'] ) $html .= '<p class="bwp-colophon__line"><em>'
            . esc_html( $m['book_wp_novel_title'] ) . '</em></p>';
        $ch = implode( ' &mdash; ', array_filter( array( $m['book_wp_chapter_part'], $m['book_wp_chapter_number'] ) ) );
        if ( $ch ) $html .= '<p class="bwp-colophon__line">' . esc_html( $ch ) . '</p>';
        $attr = array_filter( array( $m['book_wp_genre'], $m['book_wp_timeline_position'], $m['book_wp_point_of_view'] ) );
        if ( $attr ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( implode( ' &middot; ', $attr ) ) . '</p>';
        if ( $m['book_wp_setting'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html( $m['book_wp_setting'] ) . '</p>';
    }

    // ── Nukilan ───────────────────────────────────────────────────────────
    if ( 'nukilan' === $form ) {
        if ( $m['book_wp_source_work_title'] ) $html .= '<p class="bwp-colophon__line"><em>'
            . esc_html( $m['book_wp_source_work_title'] ) . '</em></p>';
        if ( $m['book_wp_source_author'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html( $m['book_wp_source_author'] ) . '</p>';
        $attr = array_filter( array( $m['book_wp_source_genre'] ) );
        if ( $attr ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( implode( ' &middot; ', $attr ) ) . '</p>';
        if ( $m['book_wp_annotation'] ) {
            $html .= '<div class="bwp-colophon__notes"><p class="bwp-colophon__notes-heading">'
                . esc_html__( 'Commentary', 'book-wp' ) . '</p>'
                . '<div class="bwp-colophon__notes-text">' . wp_kses_post( nl2br( $m['book_wp_annotation'] ) ) . '</div></div>';
        }
    }

    // ── Ratapan ───────────────────────────────────────────────────────────
    if ( 'ratapan' === $form ) {
        if ( $m['book_wp_occasion_of_grief'] ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--subject">'
            . esc_html( $m['book_wp_occasion_of_grief'] ) . '</p>';
        if ( $m['book_wp_addressee'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html__( 'Addressed to', 'book-wp' ) . ': ' . esc_html( $m['book_wp_addressee'] ) . '</p>';
        if ( $m['book_wp_lament_register'] ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( $m['book_wp_lament_register'] ) . '</p>';
    }

    // ── Esei ──────────────────────────────────────────────────────────────
    if ( 'esei' === $form ) {
        if ( $m['book_wp_thesis'] ) {
            $html .= '<div class="bwp-colophon__thesis"><p>' . esc_html( $m['book_wp_thesis'] ) . '</p></div>';
        }
        if ( $m['book_wp_abstract'] && $m['book_wp_abstract'] !== $m['book_wp_thesis'] ) {
            $html .= '<div class="bwp-colophon__notes"><p class="bwp-colophon__notes-heading">'
                . esc_html__( 'Abstract', 'book-wp' ) . '</p>'
                . '<div class="bwp-colophon__notes-text">' . wp_kses_post( nl2br( $m['book_wp_abstract'] ) ) . '</div></div>';
        }
        $attr = array_filter( array( $m['book_wp_field'], $m['book_wp_methodology'], $m['book_wp_subject'] ) );
        if ( $attr ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( implode( ' &middot; ', $attr ) ) . '</p>';
        if ( $m['book_wp_first_published'] ) $html .= '<p class="bwp-colophon__line">'
            . esc_html( $m['book_wp_first_published'] ) . '</p>';
    }

    // ── Memoir ────────────────────────────────────────────────────────────
    if ( 'memoir' === $form ) {
        $life = $m['book_wp_period_covered'] ?? '';
        if ( $life ) $html .= '<p class="bwp-colophon__line">' . esc_html( $life ) . '</p>';
        if ( $m['book_wp_key_figures'] ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( $m['book_wp_key_figures'] ) . '</p>';
        $attr = array_filter( array( $m['book_wp_memoir_tone'], $m['book_wp_memory_type'] ) );
        if ( $attr ) $html .= '<p class="bwp-colophon__line bwp-colophon__line--meta">'
            . esc_html( implode( ' &middot; ', $attr ) ) . '</p>';
    }

    return $html . '</footer>';
}
