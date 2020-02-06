<?php
/**
 * WP Club Manager API: Additional match details.
 *
 * @package USA_Rugby_Database
 * @subpackage WPCM_Meta_Box_Match_Details_Custom
 * @since USARDB 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if directly accessed

class USARDB_WPCM_Meta_Box_Match_Details_Custom {

    /**
     * Primary constructor.
     *
     * @return USARDB_WPCM_Admin_Match_Custom_Fields
     */
    public function __construct() {
        add_action( 'wpclubmanager_admin_match_details', array( $this, 'wpcm_match_additional_detail_fields' ), 10, 1 );
        add_action( 'wpclubmanager_after_admin_match_save', array( $this, 'save_wpcm_match_additional_detail_fields' ), 10, 1 );
    }

    /**
     * Save additional match detail fields.
     *
     * @link {@see 'wpclubmanager_after_admin_match_save'}
     *
     * @param int $post_id The post ID of the match.
     */
    public function save_wpcm_match_additional_detail_fields( $post_id ) {
        // Save the match's ESPN Scrum ID.
        if ( isset( $_POST['usar_scrum_id'] ) ) {
            update_post_meta( $post_id, 'usar_scrum_id', $_POST['usar_scrum_id'] );
        }
        // Save the match's World Rugby ID.
        if ( isset( $_POST['wr_id'] ) ) {
            update_post_meta( $post_id, 'wr_id', $_POST['wr_id'] );
        }
    }

    /**
     * Additional match detail fields.
     *
     * @link {@see 'wpclubmanager_admin_match_details'}
     *
     * @param int $post_id The post ID of the match.
     */
    public function wpcm_match_additional_detail_fields( $post_id ) {
        if ( 'wpcm_match' !== get_post_type( $post_id ) ) {
            return;
        }

        $field = array(
            'id'          => '',
            'label'       => '',
            'placeholder' => '',
            'class'       => '',
            'name'        => '',
            'value'       => '',
        );

        // ESPN Scrum ID
        if ( ! isset( $_POST['usar_scrum_id'] ) ) {
            $scrum_id       = get_post_meta( $post_id, 'usar_scrum_id', true );
            $field['id']    = 'usar_scrum_id';
            $field['name']  = $field['id'];
            $field['label'] = 'ESPN Scrum ID';
            $field['class'] = 'measure-text';
            $field['value'] = $scrum_id;

            wpclubmanager_wp_text_input( $field );
        }

        // World Rugby ID
        if ( ! isset( $_POST['wr_id'] ) ) {
            $world_rugby_id = get_post_meta( $post_id, 'wr_id', true );
            $field['id']    = 'wr_id';
            $field['name']  = $field['id'];
            $field['label'] = 'World Rugby ID';
            $field['class'] = 'measure-text';
            $field['value'] = $world_rugby_id;

            wpclubmanager_wp_text_input( $field );
        }
    }
}

new USARDB_WPCM_Meta_Box_Match_Details_Custom();