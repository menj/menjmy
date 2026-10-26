<?php
/**
 * Tests for inc/helpers/navigation.php
 *
 * Covers: book_wp_toc_entry, book_wp_featured_image,
 *         book_wp_progress_bar_markup, book_wp_work_navigation,
 *         book_wp_related_works, book_wp_share_hashtags,
 *         book_wp_share_row.
 *
 * @package Book_WP\Tests
 */

class NavigationTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        bwp_reset_mocks();
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_toc_entry()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_toc_entry_renders_title_and_link(): void {
        $post = bwp_make_post( 1, 'My Poem' );
        bwp_set_post_field( 1, 'post_title', 'My Poem' );
        $html = book_wp_toc_entry( $post );
        $this->assertStringContainsString( '<li', $html );
        $this->assertStringContainsString( 'bwp-toc__item', $html );
        $this->assertStringContainsString( 'My Poem', $html );
        $this->assertStringContainsString( 'href=', $html );
    }

    public function test_toc_entry_shows_form_badge(): void {
        $post = bwp_make_post( 2, 'A Story' );
        bwp_set_post_field( 2, 'post_title', 'A Story' );
        bwp_set_post_terms( 2, 'literary_form', array( bwp_make_term( 'cerpen', 'Short Story' ) ) );
        $html = book_wp_toc_entry( $post, true );
        $this->assertStringContainsString( 'bwp-badge--cerpen', $html );
        $this->assertStringContainsString( 'Short Story', $html );
    }

    public function test_toc_entry_hides_form_badge_when_disabled(): void {
        $post = bwp_make_post( 3, 'Untitled' );
        bwp_set_post_terms( 3, 'literary_form', array( bwp_make_term( 'puisi' ) ) );
        $html = book_wp_toc_entry( $post, false );
        $this->assertStringNotContainsString( 'bwp-badge', $html );
    }

    public function test_toc_entry_shows_year(): void {
        $post = bwp_make_post( 4, 'Old Work' );
        bwp_set_post_field( 4, 'post_title', 'Old Work' );
        bwp_set_post_meta( 4, 'book_wp_year', '1985' );
        $html = book_wp_toc_entry( $post );
        $this->assertStringContainsString( 'bwp-toc__year', $html );
        $this->assertStringContainsString( '1985', $html );
    }

    public function test_toc_entry_shows_excerpt(): void {
        $post = bwp_make_post( 5, 'With Excerpt' );
        bwp_set_post_field( 5, 'post_title', 'With Excerpt' );
        bwp_set_post_meta( 5, 'book_wp_toc_excerpt', 'A brief description.' );
        $html = book_wp_toc_entry( $post );
        $this->assertStringContainsString( 'bwp-toc__excerpt', $html );
        $this->assertStringContainsString( 'A brief description.', $html );
    }

    public function test_toc_entry_shows_category(): void {
        $post = bwp_make_post( 6, 'Categorised' );
        bwp_set_post_field( 6, 'post_title', 'Categorised' );
        bwp_set_categories( 6, array( bwp_make_category( 'fiction', 'Fiction' ) ) );
        bwp_set_option( 'book_wp_toc_grouping', 'none' );
        $html = book_wp_toc_entry( $post );
        $this->assertStringContainsString( 'bwp-toc__cat', $html );
        $this->assertStringContainsString( 'Fiction', $html );
    }

    public function test_toc_entry_hides_category_when_grouped_by_category(): void {
        $post = bwp_make_post( 7, 'Grouped' );
        bwp_set_post_field( 7, 'post_title', 'Grouped' );
        bwp_set_categories( 7, array( bwp_make_category( 'fiction', 'Fiction' ) ) );
        bwp_set_option( 'book_wp_toc_grouping', 'category' );
        $html = book_wp_toc_entry( $post );
        $this->assertStringNotContainsString( 'bwp-toc__cat', $html );
    }

    public function test_toc_entry_hides_uncategorized(): void {
        $post = bwp_make_post( 8, 'Default Cat' );
        bwp_set_post_field( 8, 'post_title', 'Default Cat' );
        bwp_set_categories( 8, array( bwp_make_category( 'uncategorized' ) ) );
        $html = book_wp_toc_entry( $post );
        $this->assertStringNotContainsString( 'bwp-toc__cat', $html );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_featured_image()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_featured_image_returns_empty_when_disabled(): void {
        bwp_set_option( 'book_wp_show_featured', '0' );
        $this->assertSame( '', book_wp_featured_image( 1 ) );
    }

    public function test_featured_image_returns_empty_when_no_thumbnail(): void {
        bwp_set_option( 'book_wp_show_featured', '1' );
        // has_post_thumbnail() stub always returns false
        $this->assertSame( '', book_wp_featured_image( 1 ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_progress_bar_markup()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_progress_bar_renders_on_single(): void {
        bwp_set_option( 'book_wp_progress_bar', '1' );
        ob_start();
        book_wp_progress_bar_markup();
        $html = ob_get_clean();
        $this->assertStringContainsString( 'bwp-progress', $html );
        $this->assertStringContainsString( 'role="progressbar"', $html );
    }

    public function test_progress_bar_hidden_when_disabled(): void {
        bwp_set_option( 'book_wp_progress_bar', '0' );
        ob_start();
        book_wp_progress_bar_markup();
        $html = ob_get_clean();
        $this->assertSame( '', $html );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_work_navigation()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_work_navigation_returns_empty_when_no_adjacent(): void {
        // Stubs return null for get_adjacent_post()
        $this->assertSame( '', book_wp_work_navigation() );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_related_works()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_related_works_returns_empty_when_disabled(): void {
        bwp_set_option( 'book_wp_related_works', '0' );
        $this->assertSame( '', book_wp_related_works( 1 ) );
    }

    public function test_related_works_returns_empty_when_no_matches(): void {
        bwp_set_option( 'book_wp_related_works', '1' );
        // get_posts() stub returns empty array
        $this->assertSame( '', book_wp_related_works( 1 ) );
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_share_hashtags()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_share_hashtags_returns_empty_when_no_tags(): void {
        $result = book_wp_share_hashtags( 1 );
        $this->assertTrue( $result['empty'] );
        $this->assertSame( '', $result['x'] );
        $this->assertSame( '', $result['inline'] );
    }

    public function test_share_hashtags_builds_x_and_inline_from_tags(): void {
        bwp_set_post_tags( 60, array(
            bwp_make_tag( 'love' ),
            bwp_make_tag( 'nature' ),
        ) );
        $result = book_wp_share_hashtags( 60 );
        $this->assertFalse( $result['empty'] );
        $this->assertSame( 'love,nature', $result['x'] );
        $this->assertStringContainsString( '%23love', $result['inline'] );
        $this->assertStringContainsString( '%23nature', $result['inline'] );
    }

    public function test_share_hashtags_strips_hyphens_for_x(): void {
        bwp_set_post_tags( 61, array(
            bwp_make_tag( 'malay-literature' ),
        ) );
        $result = book_wp_share_hashtags( 61 );
        $this->assertSame( 'malayliterature', $result['x'] );
    }

    public function test_share_hashtags_caps_at_three(): void {
        bwp_set_post_tags( 62, array(
            bwp_make_tag( 'one' ),
            bwp_make_tag( 'two' ),
            bwp_make_tag( 'three' ),
            bwp_make_tag( 'four' ),
            bwp_make_tag( 'five' ),
        ) );
        $result = book_wp_share_hashtags( 62 );
        $this->assertSame( 3, substr_count( $result['x'], ',' ) + 1 );
    }

    public function test_share_hashtags_excludes_hidden_tags(): void {
        $GLOBALS['_bwp_hidden_tag_slugs'] = array( 'internal' );
        bwp_set_post_tags( 63, array(
            bwp_make_tag( 'internal' ),
            bwp_make_tag( 'visible' ),
        ) );
        $result = book_wp_share_hashtags( 63 );
        $this->assertStringNotContainsString( 'internal', $result['x'] );
        $this->assertSame( 'visible', $result['x'] );
        $GLOBALS['_bwp_hidden_tag_slugs'] = array();
    }

    /* ══════════════════════════════════════════════════════════════════════
       book_wp_share_row()
       ══════════════════════════════════════════════════════════════════════ */

    public function test_share_row_returns_empty_when_disabled(): void {
        bwp_set_option( 'book_wp_share_row', '0' );
        $this->assertSame( '', book_wp_share_row( 1 ) );
    }

    public function test_share_row_renders_buttons_when_enabled(): void {
        bwp_set_option( 'book_wp_share_row', '1' );
        bwp_set_current_id( 70 );
        $html = book_wp_share_row( 70 );
        $this->assertStringContainsString( 'bwp-share', $html );
        // Should contain share platform buttons
        $this->assertStringContainsString( 'x.com/intent', $html );
        $this->assertStringContainsString( 'facebook.com/sharer', $html );
        $this->assertStringContainsString( 'wa.me', $html );
        $this->assertStringContainsString( 'threads.net', $html );
        $this->assertStringContainsString( 'pinterest.com', $html );
        $this->assertStringContainsString( 'toot.kytta.dev', $html );
        $this->assertStringContainsString( 'mailto:', $html );
    }

    public function test_share_row_contains_copy_button(): void {
        bwp_set_option( 'book_wp_share_row', '1' );
        $html = book_wp_share_row( 71 );
        $this->assertStringContainsString( 'bwp-share__copy', $html );
        $this->assertStringContainsString( 'data-bwp-copy', $html );
    }

    public function test_share_row_has_accessibility_labels(): void {
        bwp_set_option( 'book_wp_share_row', '1' );
        $html = book_wp_share_row( 72 );
        $this->assertStringContainsString( 'aria-label=', $html );
        $this->assertStringContainsString( 'Share this work', $html );
        $this->assertStringContainsString( 'Share on X (Twitter)', $html );
        $this->assertStringContainsString( 'Share on Facebook', $html );
        $this->assertStringContainsString( 'Share via WhatsApp', $html );
    }
}
