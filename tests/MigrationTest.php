<?php
/**
 * Upgrade path from Book WP: theme mods carried across a folder rename.
 */
class MigrationTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        bwp_reset_mocks();
        $GLOBALS['_bwp_stylesheet'] = 'menjmy';
    }

    public function test_copies_logo_and_css_from_book_wp_folder(): void {
        bwp_set_option( 'theme_mods_book-wp', array( 'custom_logo' => 42, 'custom_css_post_id' => 7, 'nav_menu_locations' => array( 'primary' => 3 ) ) );
        $copied = book_wp_migrate_theme_mods();
        sort( $copied );
        $this->assertSame( array( 'custom_css_post_id', 'custom_logo', 'nav_menu_locations' ), $copied );
        $this->assertSame( 42, get_theme_mod( 'custom_logo' ) );
        $this->assertSame( array( 'primary' => 3 ), get_theme_mod( 'nav_menu_locations' ) );
    }

    public function test_never_overwrites_a_mod_the_new_theme_already_has(): void {
        bwp_set_option( 'theme_mods_book-wp', array( 'custom_logo' => 42 ) );
        set_theme_mod( 'custom_logo', 99 );
        $this->assertSame( array(), book_wp_migrate_theme_mods() );
        $this->assertSame( 99, get_theme_mod( 'custom_logo' ) );
    }

    public function test_noop_when_upgraded_in_place_under_the_same_folder(): void {
        $GLOBALS['_bwp_stylesheet'] = 'book-wp';
        bwp_set_option( 'theme_mods_book-wp', array( 'custom_logo' => 42 ) );
        $this->assertSame( array(), book_wp_migrate_theme_mods() );
    }

    public function test_noop_without_a_legacy_install(): void {
        $this->assertSame( array(), book_wp_migrate_theme_mods() );
        $this->assertFalse( get_option( 'book_wp_migrated_from' ) );
    }

    public function test_ignores_unknown_keys_in_the_legacy_mods(): void {
        bwp_set_option( 'theme_mods_book-wp', array( 'sidebars_widgets' => array( 'x' ), 'custom_logo' => 1 ) );
        $this->assertSame( array( 'custom_logo' ), book_wp_migrate_theme_mods() );
        $this->assertFalse( get_theme_mod( 'sidebars_widgets' ) );
    }

    public function test_body_font_moves_to_sabon_once(): void {
        bwp_set_option( 'book_wp_font', 'vollkorn' );
        $this->assertTrue( book_wp_migrate_body_font() );
        $this->assertSame( 'sabon', get_option( 'book_wp_font' ) );
        bwp_set_option( 'book_wp_font', 'lora' );
        $this->assertFalse( book_wp_migrate_body_font(), 'runs once; a later explicit choice is kept' );
        $this->assertSame( 'lora', get_option( 'book_wp_font' ) );
    }
}
