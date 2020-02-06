<?php
/**
 * USA Rugby Database API: WP Club Manager competition adjustments.
 *
 * @author Davey Jacobson <djacobson@usa.rugby>
 * @package USA_Rugby_Database
 * @subpackage WPCM_Comps
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if directly accessed

class USARDB_WPCM_Comps extends WPCM_Admin_Taxonomies {
    /**
     * Primary constructor.
     *
     * @return USARDB_WPCM_comps
     */
    public function __construct() {
        remove_filters_for_anonymous_class( 'wpcm_comp_add_form_fields', 'WPCM_Admin_Taxonomies', 'comp_add_new_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'wpcm_comp_edit_form_fields', 'WPCM_Admin_Taxonomies', 'comp_edit_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'edited_wpcm_comp', 'WPCM_Admin_Taxonomies', 'save_comp_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'create_wpcm_comp', 'WPCM_Admin_Taxonomies', 'save_comp_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'manage_wpcm_comp_custom_column', 'WPCM_Admin_Taxonomies', 'comp_custom_columns', 5 );
        remove_filters_for_anonymous_class( 'manage_edit-wpcm_comp_columns', 'WPCM_Admin_Taxonomies', 'comp_edit_columns' );

        add_action( 'wpcm_comp_add_form_fields', array( $this, 'comp_add_new_extra_fields' ), 10, 2 );
        add_action( 'wpcm_comp_edit_form_fields',array( $this, 'comp_edit_extra_fields' ), 10, 2);
        add_action( 'edited_wpcm_comp', array( $this, 'save_comp_extra_fields' ), 10, 2);
        add_action( 'create_wpcm_comp', array( $this, 'save_comp_extra_fields' ), 10, 2 );
        add_action( 'manage_wpcm_comp_custom_column', array( $this, 'comp_custom_columns' ), 5,3);
        add_filter( 'manage_edit-wpcm_comp_columns', array( $this, 'comp_edit_columns') );
    }

    /**
     * Add custom fields to `wpcm_comp` taxonomy interface.
     *
     * @uses get_terms()
     * @uses get_term_meta()
     *
     * @param WP_Term|object $tag The current term.
     */
    public function comp_add_new_extra_fields( $tag ) {
        $args = array(
            'orderby'    => 'id',
            'order'      => 'DESC',
            'hide_empty' => false
        );
        // Get latitude and longitude from the last added venue
        $terms = get_terms( 'wpcm_comp', $args );

        if ( $terms ) {
            $term      = reset( $terms );
            $t_id      = $term->term_id;
            $term_meta = get_term_meta( $t_id );
        }
        ?>
        <div class="form-field">
            <label for="term_meta[wpcm_comp_label]"><?php _e( 'Display Name', 'wp-club-manager' ); ?></label>
            <input name="term_meta[wpcm_comp_label]" id="term_meta[wpcm_comp_label]" type="text" value="<?php echo ( isset( $term_meta['wpcm_comp_label'] ) && !empty( $term_meta['wpcm_comp_label'][0] ) ) ? $term_meta['wpcm_comp_label'][0] : '' ?>" />
            <p><?php _e('The comp label is used to display a shortened version of the comp name.', 'wp-club-manager'); ?></p>
        </div>
        <?php
    }

    /**
     * Make the custom `wpcm_comp` fields editable.
     *
     * @uses get_term_meta()
     * @uses usardb_wpcm_decode_address()
     *
     * @param WP_Term|object $tag The current term.
     */
    public function comp_edit_extra_fields( $tag ) {
        $t_id      = $tag->term_id;
        $term_meta = get_term_meta( $t_id );
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="term_meta[wpcm_comp_label]"><?php _e( 'Display Name', 'wp-club-manager' ); ?></label>
            </th>
            <td>
                <input name="term_meta[wpcm_comp_label]" id="term_meta[wpcm_comp_label]" type="text" value="<?php echo $term_meta['wpcm_comp_label'][0] ? $term_meta['wpcm_comp_label'][0] : '' ?>" />
                <p class="description"><?php _e( 'The comp label is used to display a shortened version of the comp name.', 'wp-club-manager' ); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save the custom comp fields as `term_meta`.
     *
     * @uses update_term_meta()
     * @uses get_term_meta()
     *
     * @param int $term_id The ID of the current term.
     */
    public function save_comp_extra_fields( $term_id ) {
        if ( isset( $_POST['term_meta'] ) ) {
            $t_id      = $term_id;
            $term_meta = get_term_meta( $t_id );
            $cat_keys  = array_keys( $_POST['term_meta'] );

            foreach ( $cat_keys as $key ) {
                update_term_meta( $t_id, $key, $_POST['term_meta'][ $key ] );
            }
        }
    }

    /**
     * Add custom columns for the `wpcm_comp` taxonomy.
     *
     * @param array $columns The defaults for all WP columns.
     *
     * @return array The custom columns we've added.
     */
    public function comp_edit_columns( $columns ) {
        $columns = array(
            'cb'      => "<input type=\"checkbox\" />",
            'move'    => '',
            'name'    => __( 'Name', 'wp-club-manager' ),
            'label'   => __( 'Label', 'wp-club-manager' ),
            'ID'      => __( 'ID', 'wp-club-manager' ),
        );

        return $columns;
    }

    /**
     * Additional custom columns for `wpcm_comp` taxonomy.
     *
     * @global WP_Post|object $post The current post.
     *
     * @uses USARDB_WPCM_comps::usardb_get_wpcm_player_count_by_comp()
     * @uses get_term_meta()
     *
     * @param mixed  $value  The value for the column.
     * @param string $column The column name.
     * @param int    $t_id   The term ID.
     */
    public function comp_custom_columns( $value, $column, $t_id ) {
        global $post;

        $term_meta = get_term_meta( $t_id );

        switch ( $column ) {
            case 'move':
                echo '<i class="dashicons dashicons-move"></i>';
                break;
            case 'label':
                echo $term_meta['wpcm_comp_label'][0];
                break;
            case 'ID':
                echo $t_id;
                break;
        }
    }
}

return new USARDB_WPCM_Comps();