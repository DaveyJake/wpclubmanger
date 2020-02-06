<?php
/**
 * WP Club Manager API: Additional player details.
 *
 * @package USA_Rugby_Database
 * @subpackage WPCM_Meta_Box_Player_Details
 * @since USARDB 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if not directly accessed

class USARDB_WPCM_Meta_Box_Player_Details extends WPCM_Meta_Box_Player_Details {

    /**
     * Output the metabox.
     *
     * @param WP_Post|object $post The current post object.
     */
    public static function output( $post ) {
        wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

        $positions    = get_the_terms( $post->ID, 'wpcm_position' );
        $position_ids = array();

        if ( $positions ) {
            foreach ( $positions as $position ) {
                $position_ids[] = $position->term_id;
            }
        }

        $club = get_post_meta( $post->ID, '_wpcm_player_club', true );

        // First name.
        wpclubmanager_wp_text_input( array(
            'id'    => '_wpcm_firstname',
            'label' => __( 'First Name', 'wp-club-manager' ),
            'class' => 'regular-text',
        ) );
        // Last name.
        wpclubmanager_wp_text_input( array(
            'id'    => '_wpcm_lastname',
            'label' => __( 'Last Name', 'wp-club-manager' ),
            'class' => 'regular-text',
        ) );

        // Show player badge number.
        wpclubmanager_wp_text_input( array(
            'id'    => 'wpcm_number',
            'label' => __( 'Squad Number', 'wp-club-manager' ),
            'class' => 'measure-text',
        ) );

        // Not applicable for this.
        if ( is_league_mode() )
        {
            ?>
            <p>
                <label><?php _e( 'Current Club', 'wp-club-manager' ); ?></label>
                <?php
                wpcm_dropdown_posts( array(
                    'id'               => '_wpcm_player_club',
                    'name'             => '_wpcm_player_club',
                    'post_type'        => 'wpcm_club',
                    'limit'            => -1,
                    'show_option_none' => __( 'None', 'wp-club-manager' ),
                    'class'            => 'chosen_select',
                    'selected'         => $club,
                ) );
                ?>
            </p>
            <?php
        }
        else // Is applicable for this.
        {
            wpclubmanager_wp_text_input( array(
                'id'    => '_wpcm_player_club',
                'label' => __( 'Current Club', 'wp-club-manager' ),
                'class' => 'regular-text',
            ) );
        }

        // Show player position.
        if ( 'yes' === get_option( 'wpcm_player_profile_show_position' ) ) {
            ?>
            <p>
                <label><?php _e( 'Position', 'wp-club-manager' ); ?></label>
                <?php
                $args = array(
                    'taxonomy'    => 'wpcm_position',
                    'name'        => 'tax_input[wpcm_position][]',
                    'selected'    => $position_ids,
                    'values'      => 'term_id',
                    'placeholder' => sprintf( __( 'Choose %s', 'wp-club-manager' ), __( 'positions', 'wp-club-manager' ) ),
                    'class'       => 'regular-text',
                    'attribute'   => 'multiple',
                    'chosen'      => true,
                );

                wpcm_dropdown_taxonomies( $args );
                ?>
            </p>
            <?php
        }

        // Show player birthday.
        if ( 'yes' === get_option( 'wpcm_player_profile_show_dob' ) ) {
            $dob = ( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ? get_post_meta( $post->ID, 'wpcm_dob', true ) : '1990-01-01';

            $field = array(
                'id'                => 'wpcm_dob',
                'label'             => __( 'Date of Birth', 'wp-club-manager' ),
                'placeholder'       => _x( 'YYYY-MM-DD', 'placeholder', 'wp-club-manager' ),
                'description'       => '',
                'value'             => $dob,
                'class'             => 'wpcm-birth-date-picker',
                'custom_attributes' => array(
                    'pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"
                ),
            );

            wpclubmanager_wp_text_input( $field );
        }

        // Show player height.
        if ( 'yes' === get_option( 'wpcm_player_profile_show_height' ) ) {
            $field = array(
                'id'    => 'wpcm_height',
                'label' => __( 'Height (cm)', 'wp-club-manager' ),
                'class' => 'measure-text',
            );

            wpclubmanager_wp_text_input( $field );
        }

        // Show player weight.
        if ( 'yes' === get_option( 'wpcm_player_profile_show_weight' ) ) {
            $field = array(
                'id'    => 'wpcm_weight',
                'label' => __( 'Weight (kg)', 'wp-club-manager' ),
                'class' => 'measure-text',
            );

            wpclubmanager_wp_text_input( $field );
        }

        // Birthplace
        if ( metadata_exists( 'post', $post->ID, 'usar_birthplace' ) ||
             ! isset( $_POST['usar_birthplace'] ) ) {

            $birthplace = get_post_meta( $post->ID, 'usar_birthplace', true );
            $hometown   = get_post_meta( $post->ID, 'wpcm_hometown', true );

            $field = array(
                'id'          => 'usar_birthplace',
                'name'        => '',
                'label'       => 'Birthplace',
                'description' => __( 'City, ST -- or -- City, Province, Country (3-letter abbreviation)', 'usa-rugby-database' ),
                'class'       => 'regular-text',
                'value'       => $birthplace,
                'placeholder' => $hometown,
            );
            $field['name'] = $field['id'];

            wpclubmanager_wp_text_input( $field );
        }

        // Show player hometown.
        if ( 'yes' === get_option( 'wpcm_player_profile_show_hometown' ) ) {
            $field = array(
                'id'    => 'wpcm_hometown',
                'label' => 'Hometown',
                'class' => 'regular-text',
            );

            wpclubmanager_wp_text_input( $field );
        }

        // Show player nationality.
        if ( 'yes' === get_option( 'wpcm_player_profile_show_nationality' ) ) {
            $field = array(
                'id'    => 'wpcm_natl',
                'label' => __( 'Nationality', 'wp-club-manager' ),
            );

            wpclubmanager_wp_country_select( $field );
        }

        // Show player previous clubs.
        if ( 'yes' === get_option( 'wpcm_player_profile_show_prevclubs' ) ) {
            $field = array(
                'id'    => 'wpcm_prevclubs',
                'label' => __( 'Previous Clubs', 'wp-club-manager' ),
                'class' => 'regular-text',
            );

            wpclubmanager_wp_textarea_input( $field );
        }

        // High School
        if ( metadata_exists( 'post', $post->ID, 'usar_high_school' ) ||
             ! isset( $_POST['usar_high_school'] ) ) {

            $high_school = get_post_meta( $post->ID, 'usar_high_school', true );

            $field = array(
                'id'    => 'usar_high_school',
                'name'  => '',
                'label' => 'High School',
                'class' => 'regular-text',
                'value' => $high_school,
            );
            $field['name'] = $field['id'];

            wpclubmanager_wp_text_input( $field );
        }

        // University
        if ( metadata_exists( 'post', $post->ID, 'usar_university' ) ||
             ! isset( $_POST['usar_university'] ) ) {

            $university = get_post_meta( $post->ID, 'usar_university', true );

            $field = array(
                'id'    => 'usar_university',
                'name'  => '',
                'label' => 'University',
                'class' => 'regular-text',
                'value' => $university,
            );
            $field['name'] = $field['id'];

            wpclubmanager_wp_text_input( $field );
        }

        // ESPN Scrum ID
        if ( metadata_exists( 'post', $post->ID, 'usar_scrum_id' ) ||
             ! isset( $_POST['usar_scrum_id'] ) ) {

            $scrum_id = get_post_meta( $post->ID, 'usar_scrum_id', true );

            $field = array(
                'id'    => 'usar_scrum_id',
                'name'  => '',
                'label' => 'ESPN Scrum ID',
                'class' => 'measure-text',
                'value' => $scrum_id,
            );
            $field['name'] = $field['id'];

            wpclubmanager_wp_text_input( $field );
        }

        // World Rugby ID
        if ( metadata_exists( 'post', $post->ID, 'wr_id' ) ||
             ! isset( $_POST['wr_id'] ) ) {

            $world_rugby_id = get_post_meta( $post->ID, 'wr_id', true );

            $field = array(
                'id'    => 'wr_id',
                'name'  => '',
                'label' => 'World Rugby ID',
                'class' => 'measure-text',
                'value' => $world_rugby_id,
            );
            $field['name'] = $field['id'];

            wpclubmanager_wp_text_input( $field );
        }

        // World Rugby Match List
        if ( metadata_exists( 'post', $post->ID, 'wr_match_list' ) ||
             ! isset( $_POST['wr_match_list'] ) ) {

            $match_list = get_post_meta( $post->ID, 'wr_match_list', true );
            $match_csv  = preg_split( '/\|/', $match_list );

            $field = array(
                'id'    => 'wr_match_list',
                'name'  => '',
                'label' => 'World Rugby Matches (separate by <code>,</code>)',
                'class' => 'regular-text',
                'value' => implode( ',', $match_csv ),
                'rows'  => '4',
                'cols'  => '40',
            );
            $field['name'] = $field['id'];

            wpclubmanager_wp_textarea_input( $field );
        }

        do_action( 'wpclubmanager_admin_player_details', $post->ID );
    }

    /**
     * Save meta box data.
     *
     * @uses USARDB_WPCM_Meta_Box_Player_Details::set_wpcm_player_additional_detail_fields()
     *
     * @param int            $post_id The current post ID value.
     * @param WP_Post|object $post    The current post object.
     */
    public static function save( $post_id, $post ) {
        // Save current club.
        if ( isset( $_POST['_wpcm_player_club'] ) ) {
            update_post_meta( $post->ID, '_wpcm_player_club', $_POST['_wpcm_player_club'] );
        }

        // Save birthday.
        if ( isset( $_POST['wpcm_dob'] ) ) {
            update_post_meta( $post->ID, 'wpcm_dob', $_POST['wpcm_dob'] );
        }

        // Save first name.
        if ( isset( $_POST['_wpcm_firstname'] ) ) {
            update_post_meta( $post->ID, '_wpcm_firstname', $_POST['_wpcm_firstname'] );
        }

        // Save last name.
        if ( isset( $_POST['_wpcm_lastname'] ) ) {
            update_post_meta( $post->ID, '_wpcm_lastname', $_POST['_wpcm_lastname'] );
        }

        // Save badge number.
        if ( isset( $_POST['wpcm_number'] ) ) {
            update_post_meta( $post->ID, 'wpcm_number', $_POST['wpcm_number'] );
        }

        // Save height.
        if ( isset( $_POST['wpcm_height'] ) ) {
            update_post_meta( $post->ID, 'wpcm_height', $_POST['wpcm_height'] );
        }

        // Save weight.
        if ( isset( $_POST['wpcm_weight'] ) ) {
            update_post_meta( $post->ID, 'wpcm_weight', $_POST['wpcm_weight'] );
        }

        // Save nationality.
        if ( isset( $_POST['wpcm_natl'] ) ) {
            update_post_meta( $post->ID, 'wpcm_natl', $_POST['wpcm_natl'] );
        }

        // Save hometown.
        if ( isset( $_POST['wpcm_hometown'] ) ) {
            update_post_meta( $post->ID, 'wpcm_hometown', $_POST['wpcm_hometown'] );
        }

        // Save previous clubs.
        if ( isset( $_POST['wpcm_prevclubs'] ) ) {
            update_post_meta( $post->ID, 'wpcm_prevclubs', $_POST['wpcm_prevclubs'] );
        }

        // Set private fields.
        self::set_wpcm_player_additional_detail_fields( $post->ID );

        // Save the unique badge.
        if ( isset( $_POST['_usar_badge'] ) ) {
            update_post_meta( $post->ID, '_usar_badge', $_POST['_usar_badge'] );
        }

        // Save the date of last match.
        if ( isset( $_POST['_usar_last'] ) ) {
            update_post_meta( $post->ID, '_usar_last', $_POST['_usar_last'] );
        }

        // Save the player's birthplace.
        if ( isset( $_POST['usar_birthplace'] ) ) {
            update_post_meta( $post->ID, 'usar_birthplace', $_POST['usar_birthplace'] );
        }

        // Save the player's high school.
        if ( isset( $_POST['usar_high_school'] ) ) {
            update_post_meta( $post->ID, 'usar_high_school', $_POST['usar_high_school'] );
        }

        // Save the player's university affiliation.
        if ( isset( $_POST['usar_university'] ) ) {
            update_post_meta( $post->ID, 'usar_university', $_POST['usar_university'] );
        }

        // Save the player's ESPN Scrum ID.
        if ( isset( $_POST['usar_scrum_id'] ) ) {
            update_post_meta( $post->ID, 'usar_scrum_id', $_POST['usar_scrum_id'] );
        }

        // Save the player's World Rugby ID.
        if ( isset( $_POST['wr_id'] ) ) {
            update_post_meta( $post->ID, 'wr_id', $_POST['wr_id'] );
        }

        // Save the player's World Rugby match list.
        if ( isset( $_POST['wr_match_list'] ) ) {
            $match_list             = preg_replace( '/\s/', '', $_POST['wr_match_list'] );
            $match_list             = preg_split( '/,/', $match_list );
            $_POST['wr_match_list'] = implode( '|', $match_list );

            update_post_meta( $post->ID, 'wr_match_list', $_POST['wr_match_list'] );
        }

        do_action( 'wpclubmanager_after_admin_player_save', $post->ID );

        do_action( 'delete_plugin_transients' );
    }

    /**
     * Additional player detail private fields.
     *
     * @access private
     *
     * @link {@see 'USARDB_WPCM_Meta_Box_Player_Details::save'}
     *
     * @uses USARDB_WPCM_Meta_Box_Player_Details::_get_the_latest_badge_number()
     *
     * @param int $post->ID The post ID of the player.
     */
    private static function set_wpcm_player_additional_detail_fields( $post_id ) {
        if ( 'wpcm_player' !== get_post_type( $post->ID ) ) {
            return;
        }

        // Unique badge number.
        if ( isset( $_POST['wpcm_number'] ) && ! isset( $_POST['_usar_badge'] ) ) {
            $valid = array( 'mens-eagles', 'womens-eagles' );
            // Teams attached to player.
            $teams = wp_get_object_terms( $post->ID, 'wpcm_team' );
            foreach ( $teams as $team ) {
                if ( in_array( $team->slug, $valid ) ) {
                    $latest = intval( self::_get_the_latest_badge_number( $team->slug ) );
                    $badge  = $latest + 1;
                    update_post_meta( $post->ID, '_usar_badge', $badge );
                }
            }
        }

        // Date of last match.
        if ( ! isset( $_POST['_usar_last'] ) ) {
            // Match list.
            $last_match_dates = array();
            $match_dates      = array();
            if ( metadata_exists( 'post', $post->ID, 'wr_match_list' ) ) {
                $matches = get_post_meta( $post->ID, 'wr_match_list', true );
                $matches = preg_split( '/\|/', $matches );
                $caps    = count( $matches );

                foreach ( $matches as $match ) {
                    // Get all known matches.
                    $match_args = array(
                        'post_type'      => 'wpcm_match',
                        'post_status'    => 'publish',
                        'meta_key'       => 'wr_id',
                        'meta_value'     => $match,
                    );

                    $posts = get_posts( $match_args );

                    if ( ! empty( $posts ) ) {
                        foreach ( $posts as $post ) {
                            setup_postdata( $post );
                            $match_dates[] = strtotime( $post->post_date );
                        }
                    }
                }

                wp_reset_postdata();

                $last_match = max( $match_dates );
                $last_match_dates[] = $last_match;
            }

            // Get the current `_usar_last` value.
            if ( metadata_exists( 'post', $post->ID, '_usar_last' ) ) {
                $last_match_date    = get_post_meta( $post->ID, '_usar_last', true );
                $last_match_dates[] = strtotime( $last_match_date );
            }

            $actual   = max( $last_match_dates );
            $actual   = date( 'Y-m-d', $actual );
            update_post_meta( $post->ID, '_usar_last', $actual );
        }
    }

    /**
     * Get the latest `_usar_badge` number.
     *
     * @access private
     *
     * @link {@see 'wpcm_player_additional_detail_fields'}
     *
     * @param string $team The team to look through.
     *
     * @return int The latest badge number.
     */
    private static function _get_the_latest_badge_number( $team ) {
        $badges = array();

        $post_args = array(
            'post_type'      => 'wpcm_player',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'tax_query'      => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'wpcm_team',
                    'field'    => 'slug',
                    'terms'    => $team,
                ),
            ),
        );

        $posts = get_posts( $post_args );

        foreach ( $posts as $post ) {
            setup_postdata( $post );

            $badge = get_post_meta( $post->ID, '_usar_badge', true );

            $badges[] = $badge;
        }

        wp_reset_postdata();

        return max( $badges );
    }

    /**
     * Debug match player details.
     *
     * @access private
     */
    private static function debug_match_player_details() {
        if ( is_admin() && 'wpcm_match' === get_post_type() )
        {
            if ( isset( $_POST['wpcm_players'] ) ) {
                $players = (array)$_POST['wpcm_players'];

                if ( is_array( $players ) ) {
                    if ( array_key_exists( 'lineup', $players ) && is_array( $players['lineup'] ) )
                        $players['lineup'] = array_filter( $players['lineup'], 'wpcm_array_filter_checked' );
                    if ( array_key_exists( 'subs', $players ) &&  is_array( $players['subs'] ) )
                        $players['subs'] = array_filter( $players['subs'], 'wpcm_array_filter_checked' );
                    if ( array_key_exists( 'subs_not_used', $players ) &&  is_array( $players['subs_not_used'] ) )
                        $players['subs_not_used'] = array_filter( $players['subs_not_used'], 'wpcm_array_filter_checked' );
                }

                d( $players );
            }

            if ( isset( $_POST['wpcm_match_captain'] ) ) {
                d( $_POST['wpcm_match_captain'] );
            }

            if ( isset( $_POST['wpcm_match_subs_not_used'] ) ) {
                d( $_POST['wpcm_match_subs_not_used'] );
            }
        }
    }

}
