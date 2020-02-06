<?php
/**
 * USA Rugby Database API: WP Club Manager venue adjustments.
 *
 * @author Davey Jacobson <djacobson@usa.rugby>
 * @package USA_Rugby_Database
 * @subpackage WPCM_Venues
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if directly accessed

class USARDB_WPCM_Venues extends WPCM_Admin_Taxonomies {
    /**
     * Primary constructor.
     *
     * @return USARDB_WPCM_Venues
     */
    public function __construct() {
        remove_filters_for_anonymous_class( 'manage_wpcm_venue_custom_column', 'WPCM_Admin_Taxonomies', 'venue_custom_columns', 5 );
        remove_filters_for_anonymous_class( 'wpcm_venue_add_form_fields', 'WPCM_Admin_Taxonomies', 'venue_add_new_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'wpcm_venue_edit_form_fields', 'WPCM_Admin_Taxonomies', 'venue_edit_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'edited_wpcm_venue', 'WPCM_Admin_Taxonomies', 'save_venue_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'create_wpcm_venue', 'WPCM_Admin_Taxonomies', 'save_venue_extra_fields', 10 );
        remove_filters_for_anonymous_class( 'manage_edit-wpcm_venue_columns', 'WPCM_Admin_Taxonomies', 'venue_edit_columns' );

        add_action( 'manage_wpcm_venue_custom_column', array( $this, 'venue_custom_columns' ), 5, 3 );
        add_action( 'wpcm_venue_add_form_fields', array( $this, 'venue_add_new_extra_fields' ), 10, 2 );
        add_action( 'wpcm_venue_edit_form_fields', array( $this, 'venue_edit_extra_fields' ), 10, 2 );
        add_action( 'edited_wpcm_venue', array( $this, 'save_venue_extra_fields' ), 10, 2 );
        add_action( 'create_wpcm_venue', array( $this, 'save_venue_extra_fields' ), 10, 2 );
        add_filter( 'manage_edit-wpcm_venue_columns', array( $this, 'venue_edit_columns' ) );
    }

    /**
     * Add custom fields to `wpcm_venue` taxonomy interface.
     *
     * @uses get_terms()
     * @uses get_term_meta()
     *
     * @param WP_Term|object $tag The current term.
     */
    public function venue_add_new_extra_fields( $tag ) {
        $args = array(
            'orderby'    => 'id',
            'order'      => 'DESC',
            'hide_empty' => false
        );
        // Get latitude and longitude from the last added venue
        $terms = get_terms( 'wpcm_venue', $args );

        if ( $terms ) {
            $term      = reset( $terms );
            $t_id      = $term->term_id;
            //$term_meta = get_option( "taxonomy_term_$t_id" );
            $term_meta = get_term_meta( $t_id );
            $address   = $term_meta['wpcm_address'][0];
            $capacity  = $term_meta['wpcm_capacity'][0];
            $latitude  = $term_meta['wpcm_latitude'][0];
            $longitude = $term_meta['wpcm_longitude'][0];
            $place_id  = $term_meta['place_id'][0];
            $wr_id     = $term_meta['wr_id'][0];
            $wr_name   = $term_meta['wr_name'][0];
        } else {
            $address   = __( 'USA Rugby, 2655 Crescent Dr, Unit A, Lafayette, CO 80026, USA', 'wp-club-manager' );
            $capacity  = 0;
            $latitude  = '';
            $longitude = '';
            $place_id  = '';
            $wr_id     = '';
            $wr_name   = '';
        }
        ?>
        <div class="form-field">
            <label for="term_meta[wr_id]"><?php _e( 'World Rugby ID', 'usa-rugby-database' ); ?></label>
            <input type="text" class="wr-id" name="term_meta[wr_id]" id="term_meta[wr_id]" value="<?php echo esc_attr( $wr_id ); ?>" />
        </div>
        <div class="form-field">
            <label for="term_meta[wr_name]"><?php _e( 'Historical Name', 'usa-rugby-database' ); ?></label>
            <input type="text" class="wr-name" name="term_meta[wr_name]" id="term_meta[wr_name]" value="<?php echo esc_attr( $wr_name ); ?>" />
        </div>
        <div class="form-field">
            <label for="term_meta[wpcm_address]"><?php _e( 'Venue Address', 'wp-club-manager' ); ?></label>
            <input type="text" class="wpcm-address" name="term_meta[wpcm_address]" id="term_meta[wpcm_address]" value="<?php echo esc_attr( $address ); ?>" />
            <p><div class="wpcm-location-picker"></div></p>
            <p class="description"><?php _e( "Drag the marker to the venue's location.", 'wp-club-manager' ); ?></p>
        </div>
        <div class="form-field">
            <label for="term_meta[wpcm_latitude]"><?php _e( 'Latitude', 'wp-club-manager' ); ?></label>
            <input type="text" class="wpcm-latitude" name="term_meta[wpcm_latitude]" id="term_meta[wpcm_latitude]" value="<?php echo esc_attr( $latitude ); ?>" />
        </div>
        <div class="form-field">
            <label for="term_meta[wpcm_longitude]"><?php _e( 'Longitude', 'wp-club-manager' ); ?></label>
            <input type="text" class="wpcm-longitude" name="term_meta[wpcm_longitude]" id="term_meta[wpcm_longitude]" value="<?php echo esc_attr( $longitude ); ?>" />
        </div>
        <div class="form-field">
            <label for="term_meta[place_id]"><?php _e( 'Place ID', 'wp-club-manager' ); ?></label>
            <input class="place-id" name="term_meta[place_id]" id="term_meta[place_id]" type="text" value="<?php echo esc_attr( $place_id ); ?>" size="8" />
        </div>
        <div class="form-field">
            <label for="term_meta[wpcm_capacity]"><?php _e( 'Venue Capacity', 'wp-club-manager' ); ?></label>
            <input class="wpcm-capacity" name="term_meta[wpcm_capacity]" id="term_meta[wpcm_capacity]" type="text" value="<?php echo esc_attr( $capacity ); ?>" size="8" />
        </div>
        <?php
    }

    /**
     * Make the custom `wpcm_venue` fields editable.
     *
     * @uses get_term_meta()
     * @uses usardb_wpcm_decode_address()
     *
     * @param WP_Term|object $tag The current term.
     */
    public function venue_edit_extra_fields( $tag ) {
        $t_id      = $tag->term_id;
        $term_meta = get_term_meta( $t_id );
        $address   = $term_meta['wpcm_address'][0];

        if ( $address ) {
            $coordinates = usardb_wpcm_decode_address( $address );
            if ( is_array ( $coordinates ) ) {
                $latitude  = $coordinates['lat'];
                $longitude = $coordinates['lng'];
                $place_id  = $coordinates['place_id'];
            }
        }
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta[wr_id]"><?php _e( 'World Rugby ID', 'usa-rugby-database' ); ?></label></th>
            <td><input type="text" class="wr-id" name="term_meta[wr_id]" id="term_meta[wr_id]" value="<?php echo ( isset( $term_meta['wr_id'][0] ) && !empty( $term_meta['wr_id'][0] ) ) ? $term_meta['wr_id'][0] : ''; ?>"></td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta[wr_name]"><?php _e( 'Historical Name', 'usa-rugby-database' ); ?></label></th>
            <td><input type="text" class="wr-name" name="term_meta[wr_name]" id="term_meta[wr_name]" value="<?php echo ( isset( $term_meta['wr_name'][0] ) && !empty( $term_meta['wr_name'][0] ) ) ? $term_meta['wr_name'][0] : ''; ?>"></td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta[wpcm_address]"><?php _e( 'Address', 'wp-club-manager' ); ?></label></th>
            <td>
                <input type="text" class="wpcm-address" name="term_meta[wpcm_address]" id="term_meta[wpcm_address]" value="<?php echo ( isset( $term_meta['wpcm_address'][0] ) && !empty( $term_meta['wpcm_address'][0] ) ) ? $term_meta['wpcm_address'][0] : ''; ?>" />
                <p><div class="wpcm-location-picker"></div></p>
                <p class="description"><?php _e( "Drag the marker to the venue's location.", 'wp-club-manager' ); ?></p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta[wpcm_latitude]"><?php _e( 'Latitude', 'wp-club-manager' ); ?></label></th>
            <td><input type="text" class="wpcm-latitude" name="term_meta[wpcm_latitude]" id="term_meta[wpcm_latitude]" value="<?php echo ( isset( $term_meta['wpcm_latitude'][0] ) && !empty( $term_meta['wpcm_latitude'][0] ) ) ? $term_meta['wpcm_latitude'][0] : $latitude; ?>" /></td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta[wpcm_longitude]"><?php _e( 'Longitude', 'wp-club-manager' ); ?></label></th>
            <td><input type="text" class="wpcm-longitude" name="term_meta[wpcm_longitude]" id="term_meta[wpcm_longitude]" value="<?php echo ( isset( $term_meta['wpcm_longitude'][0] ) && !empty( $term_meta['wpcm_longitude'][0] ) ) ? $term_meta['wpcm_longitude'][0] : $longitude; ?>" /></td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta[place_id]"><?php _e( 'Place ID', 'usa-rugby-database' ); ?></label></th>
            <td><input class="place-id" name="term_meta[place_id]" id="term_meta[place_id]" type="text" value="<?php echo ( isset( $term_meta['place_id'][0] ) && !empty( $term_meta['place_id'][0] ) ) ? $term_meta['place_id'][0] : $place_id; ?>" size="8" /></td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta[wpcm_capacity]"><?php _e( 'Venue Capacity', 'wp-club-manager' ); ?></label></th>
            <td><input class="wpcm-capacity" name="term_meta[wpcm_capacity]" id="term_meta[wpcm_capacity]" type="text" value="<?php echo ( isset( $term_meta['wpcm_capacity'][0] ) && !empty( $term_meta['wpcm_capacity'][0] ) ) ? $term_meta['wpcm_capacity'][0] : ''; ?>" size="8" /></td>
        </tr>
        <?php
    }

    /**
     * Save the custom venue fields as `term_meta`.
     *
     * @uses update_term_meta()
     * @uses get_term_meta()
     *
     * @param int $term_id The ID of the current term.
     */
    public function save_venue_extra_fields( $term_id ) {
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
     * Add custom columns for the `wpcm_venue` taxonomy.
     *
     * @param array $columns The defaults for all WP columns.
     *
     * @return array The custom columns we've added.
     */
    public function venue_edit_columns( $columns ) {
        $columns = array(
            'cb'       => "<input type=\"checkbox\" />",
            'name'     => __( 'Name', 'wp-club-manager' ),
            'address'  => __( 'Address', 'wp-club-manager' ),
            'capacity' => __( 'Capacity', 'wp-club-manager' ),
            'hosted'   => __( 'Hosted', 'usa-rugby-database' ),
            'ID'       => __( 'ID', 'usa-rugby-database' ),
        );

        return $columns;
    }

    /**
     * Additional custom columns for `wpcm_venue` taxonomy.
     *
     * @global WP_Post|object $post The current post.
     *
     * @uses USARDB_WPCM_Venues::usardb_get_wpcm_match_count_by_venue()
     * @uses get_term_meta()
     *
     * @param mixed  $value  The value for the column.
     * @param string $column The column name.
     * @param int    $t_id   The term ID.
     */
    public function venue_custom_columns( $value, $column, $t_id ) {
        global $post;

        $term_meta = get_term_meta( $t_id );

        switch ( $column ) {
            case 'address':
                echo ( isset( $term_meta['wpcm_address'][0] ) && !empty( $term_meta['wpcm_address'][0] ) ) ? $term_meta['wpcm_address'][0] : '';
                break;
            case 'capacity':
                echo ( isset( $term_meta['wpcm_capacity'][0] ) && !empty( $term_meta['wpcm_capacity'][0] ) ) ? $term_meta['wpcm_capacity'][0] : '';
                break;
            case 'hosted':
                $venue = get_terms( array(
                    'taxonomy'         => 'wpcm_venue',
                    'term_taxonomy_id' => $t_id,
                    'fields'           => 'id=>slug',
                    'hide_empty'       => false,
                ) );
                $count = $this->usardb_get_wpcm_match_count_by_venue( $t_id );
                echo '<a href="' . admin_url( 'edit.php?post_type=wpcm_match&wpcm_venue=' . $venue[ $t_id ] ) . '">' . ( !empty( $count ) ? $count : '0' ) . '</a>';
                break;
            case 'ID':
                echo $t_id;
                break;
        }
    }

    /**
     * Get match counts for each venue.
     *
     * @access private
     *
     * @link {@see 'USARDB_WPCM_Venues::venue_custom_columns'}
     *
     * @uses WP_Query()
     *
     * @param int $t_id The current term's ID.
     *
     * @return int The post count for the term.
     */
    private function usardb_get_wpcm_match_count_by_venue( $t_id ) {
        $args = array(
            'post_type'      => 'wpcm_match',
            'post_status'    => array( 'publish', 'future' ),
            'posts_per_page' => -1,
            'tax_query'  => array(
                array(
                    'taxonomy'         => 'wpcm_venue',
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

return new USARDB_WPCM_Venues();