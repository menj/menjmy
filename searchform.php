<?php defined( 'ABSPATH' ) || exit; ?>
<form role="search" method="get" class="bwp-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label for="bwp-search-input" class="bwp-screen-reader-text"><?php echo esc_html( __( 'Search Works', 'book-wp' ) ); ?></label>
    <input type="search" id="bwp-search-input" class="bwp-search-form__input" placeholder="<?php echo esc_attr( __( 'Search Works', 'book-wp' ) ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
    <button type="submit" class="bwp-search-form__btn"><?php esc_html_e( 'Search', 'book-wp' ); ?></button>
</form>
