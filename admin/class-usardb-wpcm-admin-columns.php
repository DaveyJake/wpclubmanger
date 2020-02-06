<?php
/**
 * USA Rugby Database API: WP Club Manager Admin Columns
 *
 * @author Davey Jacobson <djacobson@usa.rugby>
 *
 * @package USA_Rugby_Database
 * @subpackage WPCM_Admin_Columns
 * @since USARDB 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if directly accessed

class USARDB_WPCM_Admin_Columns {

    /**
     * Primary constructor.
     *
     * @return USARDB_WPCM_Admin_Columns
     */
    public function __construct() {
        // WPCM Staff renamed columns.
        add_filter( 'manage_wpcm_staff_posts_columns', array( $this, 'wpcm_staff_customize_columns' ) );

        // WPCM Staff sortable columns.
        add_filter( 'manage_edit-wpcm_staff_sortable_columns', array( $this, 'wpcm_staff_sortable_columns' ) );
    }

    /**
     * Rename and/or add to the `wpcm_staff` columns.
     *
     * @since USARDB 1.0.0
     *
     * @link {@see "manage_{$post_type}_posts_columns"}
     *
     * @param array $original Array of columns to modify.
     */
    public function wpcm_staff_customize_columns( $original ) {
        $columns          = array();
        $columns['cb']    = $original['cb'];
        $columns['image'] = '';
        $columns['name']  = __( 'Name', 'wp-club-manager' );

        if ( is_league_mode() ) {
            $columns['club'] = __( 'Club', 'wp-club-manager' );
        }

        if ( 'yes' === get_option( 'wpcm_staff_profile_show_nationality' ) ) {
            $columns['flag'] = __( 'Nationality', 'wp-club-manager' );
        }

        if ( 'yes' === get_option( 'wpcm_staff_profile_show_age' ) ) {
            $columns['age'] = __( 'Age', 'wp-club-manager' );
        }

        if ( 'yes' === get_option( 'wpcm_show_staff_email' ) ) {
            $columns['email'] = __( 'Email', 'wp-club-manager' );
        }

        if ( 'yes' === get_option( 'wpcm_show_staff_phone' ) ) {
            $columns['phone'] = __( 'Phone', 'wp-club-manager' );
        }

        $columns['jobs'] = __( 'Jobs', 'wp-club-manager' );

        return wp_parse_args( $columns, $original );
    }

    /**
     * Make staff admin columns sortable.
     *
     * @since USARDB 1.0.0
     *
     * @link {@see "manage_edit-{$post_type}_sortable_columns"}
     *
     * @param array $columns Array of columns.
     */
    public function wpcm_staff_sortable_columns( $columns ) {
        $columns['name'] = 'wpcm_number';
        $columns['jobs'] = 'wpcm_jobs';
        return $columns;
    }

}