<?php
/**
 * Front-page counts and term lists follow the public view: posts carrying a
 * hidden trigger tag are excluded from totals and from strand/form pills.
 */
class VisibilityTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        bwp_reset_mocks();
    }

    private function term( $id, $slug, $count ) {
        return (object) array( 'term_id' => $id, 'slug' => $slug, 'count' => $count );
    }

    public function test_count_uses_status_total_when_nothing_is_hidden(): void {
        $GLOBALS['_bwp_publish_count'] = 22;
        $this->assertSame( 22, book_wp_visible_work_count() );
        $this->assertArrayNotHasKey( '_bwp_last_get_posts_args', $GLOBALS, 'no extra query when nothing is hidden' );
    }

    public function test_terms_pass_through_unchanged_when_nothing_is_hidden(): void {
        $terms = array( $this->term( 1, 'asrar', 3 ), $this->term( 2, 'fiksi', 1 ) );
        $this->assertSame( $terms, book_wp_visible_terms( $terms, 'category' ) );
    }

    /**
     * book_wp_visible_work_ids() caches per request, so this is the only test
     * that populates it; keep it that way or run it in a separate process.
     */
    public function test_hidden_posts_are_excluded_from_count_and_term_pills(): void {
        $GLOBALS['_bwp_hidden_tag_slugs'] = array( 'internal' );
        $GLOBALS['_bwp_publish_count']    = 5;
        $GLOBALS['_bwp_get_posts_result'] = array( 10, 11, 12 ); // 2 of 5 hidden
        $GLOBALS['_bwp_term_objects']['category'] = array(
            1 => array( 10, 11, 99 ),  // 99 is hidden → count 2
            2 => array( 99 ),          // only hidden posts → dropped
        );

        $this->assertSame( 3, book_wp_visible_work_count() );
        $args = $GLOBALS['_bwp_last_get_posts_args'];
        $this->assertSame( 'NOT IN', $args['tax_query'][0]['operator'] );
        $this->assertSame( array( 'internal' ), $args['tax_query'][0]['terms'] );

        $out = book_wp_visible_terms( array( $this->term( 1, 'asrar', 3 ), $this->term( 2, 'rahsia', 1 ) ), 'category' );
        $this->assertCount( 1, $out );
        $this->assertSame( 'asrar', $out[0]->slug );
        $this->assertSame( 2, $out[0]->count );
    }
}
