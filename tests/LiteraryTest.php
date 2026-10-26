<?php
/**
 * Tests for inc/helpers/literary.php
 *
 * Covers: book_wp_get_form_slug, book_wp_get_form_label,
 *         book_wp_form_label, book_wp_is_poetry,
 *         book_wp_form_description, book_wp_ornament.
 *
 * @package Book_WP\Tests
 */

class LiteraryTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        bwp_reset_mocks();
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_get_form_slug()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_get_form_slug_returns_slug(): void {
        bwp_set_post_terms( 1, 'literary_form', array( bwp_make_term( 'puisi' ) ) );
        $this->assertSame( 'puisi', book_wp_get_form_slug( 1 ) );
    }

    public function test_get_form_slug_returns_first_term_slug(): void {
        bwp_set_post_terms( 2, 'literary_form', array(
            bwp_make_term( 'cerpen' ),
            bwp_make_term( 'novel' ),
        ) );
        $this->assertSame( 'cerpen', book_wp_get_form_slug( 2 ) );
    }

    public function test_get_form_slug_returns_empty_when_no_terms(): void {
        $this->assertSame( '', book_wp_get_form_slug( 99 ) );
    }

    public function test_get_form_slug_uses_current_post_id_when_zero(): void {
        bwp_set_current_id( 5 );
        bwp_set_post_terms( 5, 'literary_form', array( bwp_make_term( 'syair' ) ) );
        $this->assertSame( 'syair', book_wp_get_form_slug( 0 ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_form_label()
       ══════════════════════════════════════════════════════════════════════ */

    /**
     * @dataProvider knownFormSlugsProvider
     */
    public function test_form_label_returns_translated_label( string $slug, string $expected ): void {
        $this->assertSame( $expected, book_wp_form_label( $slug ) );
    }

    public static function knownFormSlugsProvider(): array {
        return array(
            array( 'puisi',   'Poetry' ),
            array( 'syair',   'Syair' ),
            array( 'karmina', 'Karmina' ),
            array( 'hikayat', 'Hikayat' ),
            array( 'cerpen',  'Short Story' ),
            array( 'novel',   'Novel' ),
            array( 'nukilan', 'Excerpts' ),
            array( 'ratapan', 'Lamentations' ),
            array( 'esei',    'Essay' ),
            array( 'memoir',  'Memoir' ),
        );
    }

    public function test_form_label_returns_slug_for_unknown_form(): void {
        $this->assertSame( 'unknown_form', book_wp_form_label( 'unknown_form' ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_get_form_label()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_get_form_label_returns_label_for_post(): void {
        bwp_set_post_terms( 10, 'literary_form', array( bwp_make_term( 'esei' ) ) );
        $this->assertSame( 'Essay', book_wp_get_form_label( 10 ) );
    }

    public function test_get_form_label_returns_empty_when_no_form(): void {
        $this->assertSame( '', book_wp_get_form_label( 99 ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_is_poetry()
       ══════════════════════════════════════════════════════════════════════ */

    /**
     * @dataProvider poetryFormsProvider
     */
    public function test_is_poetry_returns_true_for_verse_forms( string $slug ): void {
        bwp_set_post_terms( 100, 'literary_form', array( bwp_make_term( $slug ) ) );
        $this->assertTrue( book_wp_is_poetry( 100 ) );
    }

    public static function poetryFormsProvider(): array {
        return array(
            array( 'puisi' ),
            array( 'syair' ),
            array( 'karmina' ),
            array( 'ratapan' ),
        );
    }

    /**
     * @dataProvider proseFormsProvider
     */
    public function test_is_poetry_returns_false_for_prose_forms( string $slug ): void {
        bwp_set_post_terms( 101, 'literary_form', array( bwp_make_term( $slug ) ) );
        $this->assertFalse( book_wp_is_poetry( 101 ) );
    }

    public static function proseFormsProvider(): array {
        return array(
            array( 'cerpen' ),
            array( 'novel' ),
            array( 'hikayat' ),
            array( 'nukilan' ),
            array( 'esei' ),
            array( 'memoir' ),
        );
    }

    public function test_is_poetry_returns_false_when_no_form(): void {
        $this->assertFalse( book_wp_is_poetry( 200 ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_form_description()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_form_description_returns_string_for_all_known_forms(): void {
        $slugs = array( 'puisi', 'syair', 'karmina', 'hikayat', 'cerpen', 'novel', 'nukilan', 'ratapan', 'esei', 'memoir' );
        foreach ( $slugs as $slug ) {
            $desc = book_wp_form_description( $slug );
            $this->assertNotEmpty( $desc, "Description for '{$slug}' should not be empty" );
            $this->assertIsString( $desc );
        }
    }

    public function test_form_description_returns_empty_for_unknown_slug(): void {
        $this->assertSame( '', book_wp_form_description( 'nonexistent' ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_ornament()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_ornament_returns_svg_for_known_styles(): void {
        $styles = array( 'fleuron', 'asterism', 'dinkus', 'dash', 'leaf', 'flower', 'diamond' );
        foreach ( $styles as $style ) {
            $result = book_wp_ornament( $style );
            $this->assertStringContainsString( '<svg', $result, "Ornament '{$style}' should contain SVG" );
            $this->assertStringContainsString( 'bwp-ornament--' . $style, $result );
            $this->assertStringContainsString( 'aria-hidden="true"', $result );
        }
    }

    public function test_ornament_none_returns_empty(): void {
        $this->assertSame( '', book_wp_ornament( 'none' ) );
    }

    public function test_ornament_defaults_to_fleuron_for_unknown_style(): void {
        $result = book_wp_ornament( 'nonexistent_style' );
        $this->assertStringContainsString( '<svg', $result );
    }

    public function test_ornament_reads_global_option_when_no_argument(): void {
        bwp_set_option( 'book_wp_ornament_style', 'dinkus' );
        $result = book_wp_ornament();
        $this->assertStringContainsString( 'bwp-ornament--dinkus', $result );
    }

    public function test_ornament_wraps_in_div_with_correct_class(): void {
        $result = book_wp_ornament( 'leaf' );
        $this->assertMatchesRegularExpression(
            '/<div class="bwp-ornament bwp-ornament--leaf"/',
            $result
        );
    }

    /* ── Translations: verse detection and the translation block ───────── */

    public function test_translation_filed_under_puisi_counts_as_poetry(): void {
        bwp_set_post_terms( 30, 'literary_form', array( bwp_make_term( 'terjemahan' ), bwp_make_term( 'puisi' ) ) );
        $this->assertTrue( book_wp_is_poetry( 30 ) );
        $this->assertSame( 'terjemahan', book_wp_get_form_slug( 30 ) );
    }

    public function test_translation_of_prose_is_not_poetry(): void {
        bwp_set_post_terms( 31, 'literary_form', array( bwp_make_term( 'terjemahan' ), bwp_make_term( 'cerpen' ) ) );
        $this->assertFalse( book_wp_is_poetry( 31 ) );
    }

    public function test_translation_block_only_renders_for_terjemahan(): void {
        bwp_set_post_terms( 32, 'literary_form', array( bwp_make_term( 'nukilan' ) ) );
        bwp_set_post_meta( 32, 'book_wp_source_author', 'Edgar Allan Poe' );
        $this->assertSame( '', book_wp_terjemahan_block( 32 ), 'nukilan share the source-author key but are not translations' );

        bwp_set_post_terms( 33, 'literary_form', array( bwp_make_term( 'terjemahan' ) ) );
        bwp_set_post_meta( 33, 'book_wp_source_author', 'Edgar Allan Poe' );
        $this->assertStringContainsString( 'bwp-terjemahan', book_wp_terjemahan_block( 33 ) );
    }
}
