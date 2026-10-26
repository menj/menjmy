<?php
/**
 * Tests for inc/helpers/colophon.php
 *
 * Covers: book_wp_epigraph, book_wp_dedication, book_wp_colophon.
 *
 * @package Book_WP\Tests
 */

class ColophonTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        bwp_reset_mocks();
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_epigraph()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_epigraph_returns_empty_when_no_meta(): void {
        $this->assertSame( '', book_wp_epigraph( 1 ) );
    }

    public function test_epigraph_renders_text_only(): void {
        bwp_set_post_meta( 1, 'book_wp_epigraph', 'The pen is mightier than the sword.' );
        $html = book_wp_epigraph( 1 );
        $this->assertStringContainsString( 'bwp-epigraph', $html );
        $this->assertStringContainsString( 'The pen is mightier than the sword.', $html );
        $this->assertStringNotContainsString( '<cite', $html );
    }

    public function test_epigraph_renders_text_with_source(): void {
        bwp_set_post_meta( 2, 'book_wp_epigraph', 'To be or not to be.' );
        bwp_set_post_meta( 2, 'book_wp_epigraph_source', 'Shakespeare' );
        $html = book_wp_epigraph( 2 );
        $this->assertStringContainsString( 'bwp-epigraph__text', $html );
        $this->assertStringContainsString( '<cite', $html );
        $this->assertStringContainsString( 'Shakespeare', $html );
    }

    public function test_epigraph_wraps_in_blockquote(): void {
        bwp_set_post_meta( 3, 'book_wp_epigraph', 'A quote.' );
        $html = book_wp_epigraph( 3 );
        $this->assertStringStartsWith( '<blockquote', $html );
        $this->assertStringEndsWith( '</blockquote>', $html );
    }

    public function test_epigraph_preserves_newlines(): void {
        bwp_set_post_meta( 4, 'book_wp_epigraph', "Line one\nLine two" );
        $html = book_wp_epigraph( 4 );
        $this->assertStringContainsString( '<br', $html );
    }

    public function test_epigraph_uses_current_id_when_zero(): void {
        bwp_set_current_id( 5 );
        bwp_set_post_meta( 5, 'book_wp_epigraph', 'From the current post.' );
        $html = book_wp_epigraph( 0 );
        $this->assertStringContainsString( 'From the current post.', $html );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_dedication()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_dedication_returns_empty_when_no_meta(): void {
        $this->assertSame( '', book_wp_dedication( 1 ) );
    }

    public function test_dedication_renders_paragraph(): void {
        bwp_set_post_meta( 10, 'book_wp_dedication', 'For my beloved mother' );
        $html = book_wp_dedication( 10 );
        $this->assertStringContainsString( 'bwp-dedication', $html );
        $this->assertStringContainsString( 'For my beloved mother', $html );
        $this->assertStringStartsWith( '<p', $html );
    }

    public function test_dedication_escapes_html(): void {
        bwp_set_post_meta( 11, 'book_wp_dedication', 'For <script>alert("xss")</script>' );
        $html = book_wp_dedication( 11 );
        $this->assertStringNotContainsString( '<script>', $html );
        $this->assertStringContainsString( '&lt;script&gt;', $html );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_colophon()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_colophon_returns_empty_when_no_meta(): void {
        $this->assertSame( '', book_wp_colophon( 1 ) );
    }

    public function test_colophon_renders_dateline_with_location_and_date(): void {
        bwp_set_post_meta( 20, 'book_wp_date_composed', 'March 2024' );
        bwp_set_post_meta( 20, 'book_wp_location', 'Kuala Lumpur' );
        $html = book_wp_colophon( 20 );
        $this->assertStringContainsString( 'bwp-colophon', $html );
        $this->assertStringContainsString( 'Kuala Lumpur, March 2024', $html );
    }

    public function test_colophon_renders_date_only(): void {
        bwp_set_post_meta( 21, 'book_wp_date_composed', '2023' );
        $html = book_wp_colophon( 21 );
        $this->assertStringContainsString( 'Year composed', $html );
        $this->assertStringContainsString( '2023', $html );
    }

    public function test_colophon_renders_location_only(): void {
        bwp_set_post_meta( 22, 'book_wp_location', 'Singapore' );
        $html = book_wp_colophon( 22 );
        $this->assertStringContainsString( 'Singapore', $html );
    }

    public function test_colophon_renders_year_fallback(): void {
        bwp_set_post_meta( 23, 'book_wp_year', '1998' );
        $html = book_wp_colophon( 23 );
        $this->assertStringContainsString( '1998', $html );
        $this->assertStringContainsString( 'Year composed', $html );
    }

    public function test_colophon_renders_source(): void {
        bwp_set_post_meta( 24, 'book_wp_source', 'Dewan Bahasa' );
        $html = book_wp_colophon( 24 );
        $this->assertStringContainsString( 'Source', $html );
        $this->assertStringContainsString( 'Dewan Bahasa', $html );
    }

    public function test_colophon_renders_translation(): void {
        bwp_set_post_meta( 25, 'book_wp_original_lang', 'Arabic' );
        $html = book_wp_colophon( 25 );
        $this->assertStringContainsString( 'Translated from', $html );
        $this->assertStringContainsString( 'Arabic', $html );
    }

    public function test_colophon_wrapped_in_footer(): void {
        bwp_set_post_meta( 26, 'book_wp_year', '2000' );
        $html = book_wp_colophon( 26 );
        $this->assertStringStartsWith( '<footer', $html );
        $this->assertStringEndsWith( '</footer>', $html );
    }

    /* ── Form-specific colophon fields ───────────────────────────────── */

    public function test_colophon_syair_fields(): void {
        $id = 30;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'syair' ) ) );
        bwp_set_post_meta( $id, 'book_wp_narrative_subject', 'The fall of Malacca' );
        bwp_set_post_meta( $id, 'book_wp_metre', 'Mutaqarib' );
        bwp_set_post_meta( $id, 'book_wp_melody', 'Irama Jawi' );
        bwp_set_post_meta( $id, 'book_wp_canto_count', '12' );
        bwp_set_post_meta( $id, 'book_wp_occasion', 'Royal coronation' );
        $html = book_wp_colophon( $id );
        $this->assertStringContainsString( 'The fall of Malacca', $html );
        $this->assertStringContainsString( 'Mutaqarib', $html );
        $this->assertStringContainsString( 'Melody', $html );
        $this->assertStringContainsString( 'Irama Jawi', $html );
        $this->assertStringContainsString( '12', $html );
        $this->assertStringContainsString( 'cantos', $html );
        $this->assertStringContainsString( 'Royal coronation', $html );
    }

    public function test_colophon_karmina_fields(): void {
        $id = 31;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'karmina' ) ) );
        bwp_set_post_meta( $id, 'book_wp_sampiran', 'Buah cempedak di luar pagar' );
        bwp_set_post_meta( $id, 'book_wp_isi', 'Ambil galah tolong jolokkan' );
        bwp_set_post_meta( $id, 'book_wp_rhyme_scheme', 'a-a' );
        bwp_set_post_meta( $id, 'book_wp_register', 'Colloquial' );
        $html = book_wp_colophon( $id );
        $this->assertStringContainsString( 'Sampiran', $html );
        $this->assertStringContainsString( 'Buah cempedak', $html );
        $this->assertStringContainsString( 'Isi', $html );
        $this->assertStringContainsString( 'Rhyme', $html );
        $this->assertStringContainsString( 'a-a', $html );
        $this->assertStringContainsString( 'Colloquial', $html );
    }

    public function test_colophon_hikayat_fields(): void {
        $id = 32;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'hikayat' ) ) );
        bwp_set_post_meta( $id, 'book_wp_hikayat_title', 'Hikayat Hang Tuah' );
        bwp_set_post_meta( $id, 'book_wp_protagonist', 'Hang Tuah' );
        bwp_set_post_meta( $id, 'book_wp_manuscript', 'MS Raffles 18' );
        $html = book_wp_colophon( $id );
        $this->assertStringContainsString( 'Hikayat Hang Tuah', $html );
        $this->assertStringContainsString( 'Protagonist', $html );
        $this->assertStringContainsString( 'Manuscript', $html );
        $this->assertStringContainsString( 'MS Raffles 18', $html );
    }

    public function test_colophon_cerpen_fields(): void {
        $id = 33;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'cerpen' ) ) );
        bwp_set_post_meta( $id, 'book_wp_theme', 'Loss and memory' );
        bwp_set_post_meta( $id, 'book_wp_point_of_view', 'First person' );
        bwp_set_post_meta( $id, 'book_wp_setting', 'Johor Bahru, 1990s' );
        $html = book_wp_colophon( $id );
        $this->assertStringContainsString( 'Loss and memory', $html );
        $this->assertStringContainsString( 'First person', $html );
        $this->assertStringContainsString( 'Johor Bahru', $html );
    }

    public function test_colophon_novel_fields(): void {
        $id = 34;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'novel' ) ) );
        bwp_set_post_meta( $id, 'book_wp_novel_title', 'Salina' );
        bwp_set_post_meta( $id, 'book_wp_chapter_number', 'Chapter 3' );
        bwp_set_post_meta( $id, 'book_wp_genre', 'Social realism' );
        $html = book_wp_colophon( $id );
        $this->assertStringContainsString( 'Salina', $html );
        $this->assertStringContainsString( 'Chapter 3', $html );
        $this->assertStringContainsString( 'Social realism', $html );
    }

    public function test_colophon_nukilan_fields(): void {
        $id = 35;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'nukilan' ) ) );
        bwp_set_post_meta( $id, 'book_wp_source_work_title', 'Sejarah Melayu' );
        bwp_set_post_meta( $id, 'book_wp_source_author', 'Tun Sri Lanang' );
        bwp_set_post_meta( $id, 'book_wp_annotation', 'A key passage on sovereignty.' );
        $html = book_wp_colophon( $id );
        $this->assertStringContainsString( 'Sejarah Melayu', $html );
        $this->assertStringContainsString( 'Tun Sri Lanang', $html );
        $this->assertStringContainsString( 'Commentary', $html );
        $this->assertStringContainsString( 'A key passage on sovereignty.', $html );
    }

    public function test_colophon_ratapan_fields(): void {
        $id = 36;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'ratapan' ) ) );
        bwp_set_post_meta( $id, 'book_wp_occasion_of_grief', 'Loss of homeland' );
        bwp_set_post_meta( $id, 'book_wp_addressee', 'The departed' );
        bwp_set_post_meta( $id, 'book_wp_lament_register', 'Formal' );
        $html = book_wp_colophon( $id );
        $this->assertStringContainsString( 'Loss of homeland', $html );
        $this->assertStringContainsString( 'Addressed to', $html );
        $this->assertStringContainsString( 'The departed', $html );
        $this->assertStringContainsString( 'Formal', $html );
    }

    public function test_colophon_esei_fields(): void {
        $id = 37;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'esei' ) ) );
        bwp_set_post_meta( $id, 'book_wp_thesis', 'Language shapes identity.' );
        bwp_set_post_meta( $id, 'book_wp_field', 'Sociolinguistics' );
        bwp_set_post_meta( $id, 'book_wp_methodology', 'Qualitative' );
        $html = book_wp_colophon( $id );
        $this->assertStringContainsString( 'Language shapes identity.', $html );
        $this->assertStringContainsString( 'Sociolinguistics', $html );
        $this->assertStringContainsString( 'Qualitative', $html );
    }

    public function test_colophon_memoir_fields(): void {
        $id = 38;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'memoir' ) ) );
        bwp_set_post_meta( $id, 'book_wp_period_covered', '1950–1970' );
        bwp_set_post_meta( $id, 'book_wp_key_figures', 'Father, Tok Guru' );
        bwp_set_post_meta( $id, 'book_wp_memoir_tone', 'Reflective' );
        $html = book_wp_colophon( $id );
        $this->assertStringContainsString( '1950–1970', $html );
        $this->assertStringContainsString( 'Father, Tok Guru', $html );
        $this->assertStringContainsString( 'Reflective', $html );
    }

    public function test_colophon_includes_ornament(): void {
        bwp_set_post_meta( 39, 'book_wp_year', '2024' );
        bwp_set_post_meta( 39, 'book_wp_ornament', 'dinkus' );
        $html = book_wp_colophon( 39 );
        $this->assertStringContainsString( 'bwp-ornament--dinkus', $html );
    }

    public function test_colophon_syair_hides_completeness_when_complete(): void {
        $id = 40;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'syair' ) ) );
        bwp_set_post_meta( $id, 'book_wp_completeness', 'complete' );
        bwp_set_post_meta( $id, 'book_wp_narrative_subject', 'Test' );
        $html = book_wp_colophon( $id );
        // "complete" should not appear as a visible line
        $this->assertStringNotContainsString( 'Complete', $html );
    }

    public function test_colophon_esei_abstract_suppressed_when_equals_thesis(): void {
        $id = 41;
        bwp_set_post_terms( $id, 'literary_form', array( bwp_make_term( 'esei' ) ) );
        $same = 'Language is identity.';
        bwp_set_post_meta( $id, 'book_wp_thesis', $same );
        bwp_set_post_meta( $id, 'book_wp_abstract', $same );
        $html = book_wp_colophon( $id );
        // Abstract section should not render when it duplicates thesis
        $this->assertStringNotContainsString( 'Abstract', $html );
    }
}
