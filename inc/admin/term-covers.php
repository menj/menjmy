<?php
/**
 * Sasterawan — per-term cover fields
 *
 * Adds "Cover image" and "Cover credit" to the edit screens of categories
 * (jalur) and literary forms. Empty fields fall back to the bundled plate.
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

function book_wp_term_cover_fields( $term ) {
    $img    = get_term_meta( $term->term_id, 'book_wp_cover_image', true );
    $credit = get_term_meta( $term->term_id, 'book_wp_cover_credit', true );
    wp_nonce_field( 'book_wp_term_cover', 'book_wp_term_cover_nonce' );
    ?>
    <tr class="form-field">
        <th scope="row"><label for="book_wp_cover_image"><?php esc_html_e( 'Cover image', 'book-wp' ); ?></label></th>
        <td>
            <input type="url" name="book_wp_cover_image" id="book_wp_cover_image" value="<?php echo esc_attr( $img ); ?>" placeholder="https://">
            <p class="description"><?php esc_html_e( 'Media Library URL of a wide image for this archive\'s title page. Empty uses the bundled plate, if one exists for this term.', 'book-wp' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="book_wp_cover_credit"><?php esc_html_e( 'Cover credit', 'book-wp' ); ?></label></th>
        <td>
            <input type="text" name="book_wp_cover_credit" id="book_wp_cover_credit" value="<?php echo esc_attr( $credit ); ?>">
            <p class="description"><?php esc_html_e( 'Shown small at the foot of the title page, e.g. source and licence.', 'book-wp' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'category_edit_form_fields', 'book_wp_term_cover_fields' );
add_action( 'literary_form_edit_form_fields', 'book_wp_term_cover_fields' );

function book_wp_save_term_cover( $term_id ) {
    if ( ! isset( $_POST['book_wp_term_cover_nonce'] ) || ! wp_verify_nonce( $_POST['book_wp_term_cover_nonce'], 'book_wp_term_cover' ) ) return;
    if ( ! current_user_can( 'manage_categories' ) ) return;
    $img    = isset( $_POST['book_wp_cover_image'] ) ? esc_url_raw( wp_unslash( $_POST['book_wp_cover_image'] ) ) : '';
    $credit = isset( $_POST['book_wp_cover_credit'] ) ? sanitize_text_field( wp_unslash( $_POST['book_wp_cover_credit'] ) ) : '';
    if ( $img ) update_term_meta( $term_id, 'book_wp_cover_image', $img ); else delete_term_meta( $term_id, 'book_wp_cover_image' );
    if ( $credit ) update_term_meta( $term_id, 'book_wp_cover_credit', $credit ); else delete_term_meta( $term_id, 'book_wp_cover_credit' );
}
add_action( 'edited_category', 'book_wp_save_term_cover' );
add_action( 'edited_literary_form', 'book_wp_save_term_cover' );
