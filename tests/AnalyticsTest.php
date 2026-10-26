<?php
/**
 * Tests for inc/helpers/analytics.php
 *
 * Covers: book_wp_extract_chapter_text, book_wp_get_full_text,
 *         book_wp_reading_time, book_wp_word_count,
 *         book_wp_line_count, book_wp_stanza_count,
 *         book_wp_analytics_bar.
 *
 * @package Book_WP\Tests
 */

class AnalyticsTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        bwp_reset_mocks();
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_extract_chapter_text()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_extract_chapter_text_returns_empty_when_no_chapter_blocks(): void {
        $this->assertSame( '', book_wp_extract_chapter_text( '<p>Hello world</p>' ) );
    }

    public function test_extract_chapter_text_extracts_content_attribute(): void {
        $raw = '<!-- wp:book-wp/chapter {"content":"<p>First verse</p><p>Second verse</p>"} /-->';
        $result = book_wp_extract_chapter_text( $raw );
        $this->assertStringContainsString( 'First verse', $result );
        $this->assertStringContainsString( 'Second verse', $result );
    }

    public function test_extract_chapter_text_strips_html_tags(): void {
        $raw = '<!-- wp:book-wp/chapter {"content":"<p><strong>Bold</strong> text</p>"} /-->';
        $result = book_wp_extract_chapter_text( $raw );
        $this->assertStringNotContainsString( '<strong>', $result );
        $this->assertStringContainsString( 'Bold', $result );
    }

    public function test_extract_chapter_text_handles_br_tags_as_newlines(): void {
        $raw = '<!-- wp:book-wp/chapter {"content":"<p>Line one<br/>Line two</p>"} /-->';
        $result = book_wp_extract_chapter_text( $raw );
        $this->assertStringContainsString( 'Line one', $result );
        $this->assertStringContainsString( 'Line two', $result );
    }

    public function test_extract_chapter_text_handles_legacy_verses_array(): void {
        $json = json_encode( array( 'verses' => array( 'verse one', 'verse two', 'verse three' ) ) );
        $raw  = '<!-- wp:book-wp/chapter ' . $json . ' /-->';
        $result = book_wp_extract_chapter_text( $raw );
        $this->assertStringContainsString( 'verse one', $result );
        $this->assertStringContainsString( 'verse three', $result );
    }

    public function test_extract_chapter_text_combines_multiple_blocks(): void {
        $raw  = '<!-- wp:book-wp/chapter {"content":"<p>Alpha</p>"} /-->';
        $raw .= '<!-- wp:book-wp/chapter {"content":"<p>Beta</p>"} /-->';
        $result = book_wp_extract_chapter_text( $raw );
        $this->assertStringContainsString( 'Alpha', $result );
        $this->assertStringContainsString( 'Beta', $result );
    }

    public function test_extract_chapter_text_skips_invalid_json(): void {
        $raw = '<!-- wp:book-wp/chapter {invalid json} /-->';
        $this->assertSame( '', book_wp_extract_chapter_text( $raw ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_word_count()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_word_count_plain_prose(): void {
        bwp_set_post_field( 1, 'post_content', '<p>The quick brown fox jumps over the lazy dog.</p>' );
        $this->assertSame( 9, book_wp_word_count( 1 ) );
    }

    public function test_word_count_includes_chapter_block_content(): void {
        $body = '<p>Body text here.</p>';
        $chapter = '<!-- wp:book-wp/chapter {"content":"<p>Chapter verse one two</p>"} /-->';
        bwp_set_post_field( 10, 'post_content', $body . $chapter );
        // "Body text here" = 3 words + "Chapter verse one two" = 4 words = 7
        $this->assertSame( 7, book_wp_word_count( 10 ) );
    }

    public function test_word_count_empty_content(): void {
        bwp_set_post_field( 2, 'post_content', '' );
        $this->assertSame( 0, book_wp_word_count( 2 ) );
    }

    public function test_word_count_html_tags_not_counted(): void {
        bwp_set_post_field( 3, 'post_content', '<p><em>one</em> <strong>two</strong> <a href="#">three</a></p>' );
        $this->assertSame( 3, book_wp_word_count( 3 ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_reading_time()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_reading_time_prose_200wpm(): void {
        // 400 words at 200 WPM = 2 min
        $words = implode( ' ', array_fill( 0, 400, 'word' ) );
        bwp_set_post_field( 20, 'post_content', "<p>$words</p>" );
        // No literary_form → not poetry → 200 WPM
        $this->assertSame( '2 min read', book_wp_reading_time( 20 ) );
    }

    public function test_reading_time_poetry_120wpm(): void {
        // 240 words at 120 WPM = 2 min
        $words = implode( ' ', array_fill( 0, 240, 'word' ) );
        bwp_set_post_field( 21, 'post_content', "<p>$words</p>" );
        bwp_set_post_terms( 21, 'literary_form', array( bwp_make_term( 'puisi' ) ) );
        $this->assertSame( '2 min read', book_wp_reading_time( 21 ) );
    }

    public function test_reading_time_minimum_one_minute(): void {
        bwp_set_post_field( 22, 'post_content', '<p>Short.</p>' );
        $this->assertSame( '1 min read', book_wp_reading_time( 22 ) );
    }

    public function test_reading_time_rounds_up(): void {
        // 201 words at 200 WPM = ceil(1.005) = 2 min
        $words = implode( ' ', array_fill( 0, 201, 'word' ) );
        bwp_set_post_field( 23, 'post_content', "<p>$words</p>" );
        $this->assertSame( '2 min read', book_wp_reading_time( 23 ) );
    }

    public function test_reading_time_minutes_returns_numeric_value(): void {
        $words = implode( ' ', array_fill( 0, 201, 'word' ) );
        bwp_set_post_field( 24, 'post_content', "<p>$words</p>" );
        $this->assertSame( 2, book_wp_reading_time_minutes( 24 ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_line_count()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_line_count_counts_paragraphs(): void {
        bwp_set_post_field( 30, 'post_content', '<p>Line one</p><p>Line two</p><p>Line three</p>' );
        $this->assertSame( 3, book_wp_line_count( 30 ) );
    }

    public function test_line_count_counts_br_tags(): void {
        bwp_set_post_field( 31, 'post_content', '<p>Line one<br>Line two<br/>Line three</p>' );
        $this->assertSame( 3, book_wp_line_count( 31 ) );
    }

    public function test_line_count_skips_blank_lines(): void {
        bwp_set_post_field( 32, 'post_content', '<p>One</p><p></p><p>Two</p>' );
        $this->assertSame( 2, book_wp_line_count( 32 ) );
    }

    public function test_line_count_includes_chapter_blocks(): void {
        $raw  = '<p>Body line</p>';
        $raw .= '<!-- wp:book-wp/chapter {"content":"<p>Verse A</p><p>Verse B</p>"} /-->';
        bwp_set_post_field( 33, 'post_content', $raw );
        $count = book_wp_line_count( 33 );
        $this->assertGreaterThanOrEqual( 3, $count );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_stanza_count()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_stanza_count_classic_paragraphs(): void {
        $raw = '<p>Stanza one.</p><p>Stanza two.</p><p>Stanza three.</p>';
        bwp_set_post_field( 40, 'post_content', $raw );
        $this->assertSame( 3, book_wp_stanza_count( 40 ) );
    }

    public function test_stanza_count_gutenberg_paragraph_blocks(): void {
        $raw  = '<!-- wp:paragraph --><p>Stanza one.</p><!-- /wp:paragraph -->';
        $raw .= '<!-- wp:paragraph --><p>Stanza two.</p><!-- /wp:paragraph -->';
        bwp_set_post_field( 41, 'post_content', $raw );
        $this->assertSame( 2, book_wp_stanza_count( 41 ) );
    }

    public function test_stanza_count_chapter_blocks(): void {
        $json = json_encode( array( 'content' => '<p>Verse 1</p><p>Verse 2</p><p>Verse 3</p>' ) );
        $raw  = '<!-- wp:book-wp/chapter ' . $json . ' /-->';
        bwp_set_post_field( 42, 'post_content', $raw );
        $this->assertSame( 3, book_wp_stanza_count( 42 ) );
    }

    public function test_stanza_count_minimum_one(): void {
        bwp_set_post_field( 43, 'post_content', '' );
        $this->assertSame( 1, book_wp_stanza_count( 43 ) );
    }

    public function test_stanza_count_skips_empty_paragraphs_in_chapters(): void {
        $json = json_encode( array( 'content' => '<p>Real</p><p>  </p><p>Also real</p>' ) );
        $raw  = '<!-- wp:book-wp/chapter ' . $json . ' /-->';
        bwp_set_post_field( 44, 'post_content', $raw );
        $this->assertSame( 2, book_wp_stanza_count( 44 ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_analytics_bar()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_analytics_bar_contains_word_count_and_reading_time(): void {
        bwp_set_post_field( 50, 'post_content', '<p>' . implode( ' ', array_fill( 0, 100, 'word' ) ) . '</p>' );
        $html = book_wp_analytics_bar( 50 );
        $this->assertStringContainsString( 'bwp-analytics', $html );
        $this->assertStringContainsString( '100 words', $html );
        $this->assertStringContainsString( 'min read', $html );
    }

    public function test_analytics_bar_includes_form_badge(): void {
        bwp_set_post_field( 51, 'post_content', '<p>Some words here now.</p>' );
        bwp_set_post_terms( 51, 'literary_form', array( bwp_make_term( 'cerpen', 'Short Story' ) ) );
        $html = book_wp_analytics_bar( 51 );
        $this->assertStringContainsString( 'bwp-badge--cerpen', $html );
        $this->assertStringContainsString( 'Short Story', $html );
    }

    public function test_analytics_bar_includes_category(): void {
        bwp_set_post_field( 52, 'post_content', '<p>Some text.</p>' );
        bwp_set_categories( 52, array( bwp_make_category( 'fiction', 'Fiction' ) ) );
        $html = book_wp_analytics_bar( 52 );
        $this->assertStringContainsString( 'Fiction', $html );
    }

    public function test_analytics_bar_excludes_uncategorized(): void {
        bwp_set_post_field( 53, 'post_content', '<p>Some text.</p>' );
        bwp_set_categories( 53, array( bwp_make_category( 'uncategorized', 'Uncategorized' ) ) );
        $html = book_wp_analytics_bar( 53 );
        $this->assertStringNotContainsString( 'Uncategorized', $html );
    }

    public function test_analytics_bar_shows_lines_and_stanzas_for_poetry(): void {
        $content = '<p>Line one</p><p>Line two</p><p>Line three</p>';
        bwp_set_post_field( 54, 'post_content', $content );
        bwp_set_post_terms( 54, 'literary_form', array( bwp_make_term( 'puisi' ) ) );
        $html = book_wp_analytics_bar( 54 );
        $this->assertStringContainsString( 'lines', $html );
        $this->assertStringContainsString( 'stanzas', $html );
    }

    public function test_analytics_bar_hides_lines_and_stanzas_for_prose(): void {
        bwp_set_post_field( 55, 'post_content', '<p>Just some prose text here.</p>' );
        bwp_set_post_terms( 55, 'literary_form', array( bwp_make_term( 'cerpen' ) ) );
        $html = book_wp_analytics_bar( 55 );
        $this->assertStringNotContainsString( 'lines', $html );
        $this->assertStringNotContainsString( 'stanzas', $html );
    }

    public function test_analytics_bar_hides_lines_for_ratapan(): void {
        $content = '<p>Verse one</p><p>Verse two</p><p>Verse three</p>';
        bwp_set_post_field( 56, 'post_content', $content );
        bwp_set_post_terms( 56, 'literary_form', array( bwp_make_term( 'ratapan' ) ) );
        $html = book_wp_analytics_bar( 56 );
        // ratapan is poetry but lines/stanzas are suppressed
        $this->assertStringNotContainsString( 'lines', $html );
        $this->assertStringNotContainsString( 'stanzas', $html );
    }
}
