<?php
/**
 * USA Rugby Database API: WP Club Manager team adjustments.
 *
 * @author Davey Jacobson <djacobson@usa.rugby>
 * @package USA_Rugby_Database
 * @subpackage WPCM_Teams
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if directly accessed

class USARDB_WPCM_Teams extends WPCM_Admin_Taxonomies {
    /**
     * Primary constructor.
     *
     * @return USARDB_WPCM_Teams
     */
    public function __construct() {
        remove_filters_for_anonymous_class( 'wpcm_team_add_form_fields', 'WPCM_Admin_Taxonomies', 'team_add_new_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'wpcm_team_edit_form_fields', 'WPCM_Admin_Taxonomies', 'team_edit_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'edited_wpcm_team', 'WPCM_Admin_Taxonomies', 'save_team_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'create_wpcm_team', 'WPCM_Admin_Taxonomies', 'save_team_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'manage_wpcm_team_custom_column', 'WPCM_Admin_Taxonomies', 'team_custom_columns', 5 );
        remove_filters_for_anonymous_class( 'manage_edit-wpcm_team_columns', 'WPCM_Admin_Taxonomies', 'team_edit_columns' );

        add_action( 'wpcm_team_add_form_fields', array( $this, 'team_add_new_extra_fields' ), 10, 2 );
        add_action( 'wpcm_team_edit_form_fields', array( $this, 'team_edit_extra_fields' ), 10, 2);
        add_action( 'edited_wpcm_team', array( $this, 'save_team_extra_fields' ), 10, 2);
        add_action( 'create_wpcm_team', array( $this, 'save_team_extra_fields' ), 10, 2 );
        add_action( 'manage_wpcm_team_custom_column', array( $this, 'team_custom_columns' ), 5,3);
        add_filter( 'manage_edit-wpcm_team_columns', array( $this, 'team_edit_columns' ) );
    }

    /**
     * Add custom fields to `wpcm_team` taxonomy interface.
     *
     * @uses get_terms()
     * @uses get_term_meta()
     *
     * @param WP_Term|object $tag The current term.
     */
    public function team_add_new_extra_fields( $tag ) {
        $args = array(
            'orderby'    => 'id',
            'order'      => 'DESC',
            'hide_empty' => false
        );

        $terms = get_terms( 'wpcm_team', $args );

        if ( $terms ) {
            $term      = reset( $terms );
            $t_id      = $term->term_id;
            $term_meta = get_term_meta( $t_id );

            $wpcm_team_label = $term_meta['wpcm_team_label'][0];
        }
        ?>
        <div class="form-field">
            <label for="term_meta[wpcm_team_label]"><?php _e( 'Display Name', 'wp-club-manager' ); ?></label>
            <input name="term_meta[wpcm_team_label]" id="term_meta[wpcm_team_label]" type="text" value="<?php echo ( isset( $term_meta['wpcm_team_label'] ) && !empty( $term_meta['wpcm_team_label'][0] ) ) ? $term_meta['wpcm_team_label'][0] : $wpcm_team_label; ?>" />
            <p><?php _e('The team label is used to display a shortened version of the team name.', 'wp-club-manager'); ?></p>
        </div>
        <?php
    }

    /**
     * Make the custom `wpcm_team` fields editable.
     *
     * @uses get_term_meta()
     * @uses usardb_wpcm_decode_address()
     *
     * @param WP_Term|object $tag The current term.
     */
    public function team_edit_extra_fields( $tag ) {
        $t_id      = $tag->term_id;
        $term_meta = get_term_meta( $t_id );
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="term_meta[wpcm_team_label]"><?php _e( 'Display Name', 'wp-club-manager' ); ?></label>
            </th>
            <td>
                <input name="term_meta[wpcm_team_label]" id="term_meta[wpcm_team_label]" type="text" value="<?php echo $term_meta['wpcm_team_label'][0] ? $term_meta['wpcm_team_label'][0] : '' ?>" />
                <p class="description"><?php _e( 'The team label is used to display a shortened version of the team name.', 'wp-club-manager' ); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save the custom team fields as `term_meta`.
     *
     * @uses update_term_meta()
     * @uses get_term_meta()
     *
     * @param int $term_id The ID of the current term.
     */
    public function save_team_extra_fields( $term_id ) {
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
     * Add custom columns for the `wpcm_team` taxonomy.
     *
     * @param array $columns The defaults for all WP columns.
     *
     * @return array The custom columns we've added.
     */
    public function team_edit_columns( $columns ) {
        $columns = array(
            'cb'      => "<input type=\"checkbox\" />",
            'move'    => '',
            'name'    => __( 'Name', 'wp-club-manager' ),
            'label'   => __( 'Label', 'wp-club-manager' ),
            'players' => __( 'Players', 'wp-club-manager' ),
            'ID'      => __( 'ID', 'wp-club-manager' ),
        );

        return $columns;
    }

    /**
     * Additional custom columns for `wpcm_team` taxonomy.
     *
     * @global WP_Post|object $post The current post.
     *
     * @uses USARDB_WPCM_Teams::usardb_get_wpcm_player_count_by_team()
     * @uses get_term_meta()
     *
     * @param mixed  $value  The value for the column.
     * @param string $column The column name.
     * @param int    $t_id   The term ID.
     */
    public function team_custom_columns( $value, $column, $t_id ) {
        global $post;

        $term_meta = get_term_meta( $t_id );

        switch ( $column ) {
            case 'move':
                echo '<i class="dashicons dashicons-move"></i>';
                break;
            case 'label':
                echo $term_meta['wpcm_team_label'][0];
                break;
            case 'players':
                $teams = get_terms( array(
                    'taxonomy'         => 'wpcm_team',
                    'term_taxonomy_id' => $t_id,
                    'fields'           => 'id=>slug',
                    'hide_empty'       => false,
                ) );
                $count = $this->usardb_get_wpcm_player_count_by_team( $t_id );
                echo '<a href="' . admin_url( 'edit.php?post_type=wpcm_player&wpcm_team=' . $teams[ $t_id ] ) . '">' . ( !empty( $count ) ? $count : '0' ) . '</a>';
                break;
            case 'ID':
                echo $t_id;
                break;
        }
    }

    /**
     * Get player counts for each team.
     *
     * @access private
     *
     * @link {@see 'USARDB_WPCM_Teams::team_custom_columns'}
     *
     * @uses WP_Query()
     *
     * @param int $t_id The current term's ID.
     *
     * @return int The post count for the term.
     */
    private function usardb_get_wpcm_player_count_by_team( $t_id ) {
        $args = array(
            'post_type'      => 'wpcm_player',
            'post_status'    => array( 'publish' ),
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy'         => 'wpcm_team',
                    'field'            => 'term_id',
                    'terms'            => array( $t_id ),
                    'include_children' => false,
                ),
            ),
        );

        $query = new WP_Query( $args );
        $count = (int) $query->post_count;
        wp_reset_postdata();

        return $count;
    }

}

return new USARDB_WPCM_Teams();