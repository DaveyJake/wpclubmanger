<?php
/**
 * USA Rugby Database API: WP Club Manager Meta Boxes
 *
 * @author Davey Jacobson <djacobson@usa.rugby>
 * @package USA_Rugby_Database
 * @subpackage WPCM_Admin_Meta_Boxes
 * @version WPCM 2.1.3
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class USARDB_WPCM_Admin_Meta_Boxes extends WPCM_Admin_Meta_Boxes {

    /**
     * Priamry constructor.
     *
     * @return USARDB_WPCM_Admin_Meta_Boxes
     */
    public function __construct() {
        remove_filters_for_anonymous_class( 'add_meta_boxes', 'WPCM_Admin_Meta_Boxes', 'add_meta_boxes', 20 );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 );
    }

    /**
     * Add WPCM Meta boxes.
     *
     * @global WP_Post|object $post
     */
    public function add_meta_boxes() {
        global $post;

        $this->club_meta_boxes( $post );
        $this->match_meta_boxes( $post );
        $this->player_meta_boxes( $post );
        $this->staff_meta_boxes( $post );
        $this->league_tables( $post );
        $this->rosters( $post );
        $this->sponsors( $post );
    }

    /**
     * Add WPCM Club meta boxes.
     *
     * @access private
     */
    private function club_meta_boxes( $post ) {
        add_meta_box( 'wpclubmanager-club-parent', __( 'Linked Clubs', 'wp-club-manager'), 'WPCM_Meta_Box_Club_Parent::output', 'wpcm_club', 'normal', 'high' );
        add_meta_box( 'wpclubmanager-club-details', __( 'Club Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Details::output', 'wpcm_club', 'normal', 'high' );
        add_meta_box( 'wpclubmanager-club-info', __( 'Club Information', 'wp-club-manager'), function( $post ) {
            wp_editor( $post->post_content, 'post_content', array(
                'name'          => 'post_content',
                'textarea_rows' => 10,
                'tinymce'       => array( 'resize' => false )
            ) );
        }, 'wpcm_club', 'normal', 'high' );

        if ( is_league_mode() && 'publish' === $post->post_status )
        {
            add_meta_box( 'wpclubmanager-club-players', __( 'Players', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Players::output', 'wpcm_club', 'normal', 'high' );
            add_meta_box( 'wpclubmanager-club-staff', __( 'Staff', 'wp-club-manager' ), 'WPCM_Meta_Box_Club_Staff::output', 'wpcm_club', 'normal', 'high' );
        }

        add_meta_box( 'postimagediv', __( 'Club Badge', 'wp-club-manager'), 'post_thumbnail_meta_box', 'wpcm_club', 'side' );
        add_meta_box( 'wpcm_venuediv', __( 'Home Venue', 'wp-club-manager'), array( $this, 'venue_meta_box_cb' ), 'wpcm_club', 'side' );
        add_meta_box( 'wpclubmanager-club-table', __( 'Add to League Table', 'wp-club-manager'), 'WPCM_Meta_Box_Club_Table::output', 'wpcm_club', 'side' );
    }

    /**
     * Add WPCM Match meta boxes with custom details to {@see 'wpclubmanager-match-details'}
     * and {@see 'wpclubmanager-match-result'}.
     *
     * @access private
     */
    private function match_meta_boxes( $post ) {
        add_meta_box( 'wpclubmanager-match-fixture', __( 'Match Fixture', 'wp-club-manager' ), 'WPCM_Meta_Box_Match_Fixture::output', 'wpcm_match', 'normal', 'high' );
        // Custom match details added here.
        add_meta_box( 'wpclubmanager-match-details', __( 'Match Details', 'wp-club-manager' ), 'USARDB_WPCM_Meta_Box_Match_Details::output', 'wpcm_match', 'normal', 'high' );

        if ( 'yes' === get_option( 'wpcm_match_show_report', 'yes' ) )
        {
            add_meta_box( 'wpclubmanager-match-report', __( 'Match Report', 'wp-club-manager' ), function( $post ) {
                wp_editor( $post->post_content, 'post_content', array(
                    'name' => 'post_content',
                    'textarea_rows' => 20
                ) );
            }, 'wpcm_match', 'normal', 'high' );
        }

        if ( 'yes' === get_option( 'wpcm_match_show_preview', 'no' ) )
        {
            add_meta_box( 'postexcerpt', __( 'Match Preview', 'wp-club-manager' ), function( $post ) {
                wp_editor( $post->post_excerpt, 'excerpt', array(
                    'name'                        => 'excerpt',
                    'quicktags'                   => array( 'buttons' => 'em,strong,link' ),
                    'tinymce'                     => array(
                        'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                        'theme_advanced_buttons2' => '',
                    ),
                    'editor_css'                  => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
                ) );
            }, 'wpcm_match', 'normal', 'low' );
        }

        if ( '' !== get_post_meta( $post->ID, 'wpcm_home_club', true ) ) {
            add_meta_box( 'wpclubmanager-match-players', __( 'Select Players', 'wp-club-manager' ), 'WPCM_Meta_Box_Match_Players::output', 'wpcm_match', 'normal', 'low' );
        }

        // Custom match details added here.
        add_meta_box( 'wpclubmanager-match-result', __( 'Match Result', 'wp-club-manager'), 'USARDB_WPCM_Meta_Box_Match_Result::output', 'wpcm_match', 'side' );
        add_meta_box( 'wpclubmanager-match-video', __( 'Match Video', 'wp-club-manager'), 'WPCM_Meta_Box_Match_Video::output', 'wpcm_match', 'side' );
    }

    /**
     * Add WPCM Player meta boxes.
     *
     * @access private
     */
    private function player_meta_boxes( $post ) {

        add_meta_box( 'wpclubmanager-player-details', __( 'Player Details', 'wp-club-manager' ), 'USARDB_WPCM_Meta_Box_Player_Details::output', 'wpcm_player', 'normal', 'high' );

        add_meta_box( 'wpclubmanager-player-bio', __( 'Player Biography', 'wp-club-manager' ), function( $post ) {
            wp_editor( $post->post_content, 'post_content', array(
                'name'          => 'post_content',
                'textarea_rows' => 10,
                'tinymce'       => array( 'resize' => false )
            ) );
        }, 'wpcm_player', 'normal', 'high' );

        if ( 'publish' === $post->post_status )
        {
            add_meta_box( 'wpclubmanager-player-stats', __( 'Player Statistics', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Stats::output', 'wpcm_player', 'normal', 'high' );
            add_meta_box( 'wpclubmanager-player-users', __( 'Link Player to User', 'wp-club-manager' ), 'WPCM_Meta_Box_Player_Users::output', 'wpcm_player', 'normal', 'high' );
        }

        add_meta_box( 'wpclubmanager-player-display', __( 'Player Stats Display', 'wp-club-manager'), 'WPCM_Meta_Box_Player_Display::output', 'wpcm_player', 'side' );
        add_meta_box( 'postimagediv', __( 'Player Image' ), 'post_thumbnail_meta_box', 'wpcm_player', 'side' );

        if ( is_club_mode() )
        {
            add_meta_box( 'wpclubmanager-player-roster', __( 'Add Player to Roster', 'wp-club-manager'), 'WPCM_Meta_Box_Player_Roster::output', 'wpcm_player', 'side' );
        }

    }

    /**
     * Add WPCM Staff meta boxes.
     *
     * @access private
     */
    private function staff_meta_boxes( $post ) {
        add_meta_box( 'wpclubmanager-staff-details', __( 'Staff Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Staff_Details::output', 'wpcm_staff', 'normal', 'high' );
        add_meta_box( 'wpclubmanager-staff-bio', __( 'Staff Biography', 'wp-club-manager'), function( $post ) {
            wp_editor( $post->post_content, 'post_content', array(
                'name'=>'post_content',
                'textarea_rows' => 10,
                'tinymce' => array( 'resize' => false )
            ) );
        }, 'wpcm_staff', 'normal', 'high' );

        add_meta_box( 'postimagediv', __( 'Staff Image' ), 'post_thumbnail_meta_box', 'wpcm_staff', 'side' );

        if ( is_club_mode() )
        {
            add_meta_box( 'wpclubmanager-staff-roster', __( 'Add to Staff Roster', 'wp-club-manager'), 'WPCM_Meta_Box_Staff_Roster::output', 'wpcm_staff', 'side' );
        }
    }

    /**
     * League tables.
     *
     * @access private
     */
    private function league_tables( $post ) {
        if ( $post->post_status == 'publish' )
        {
            add_meta_box( 'wpclubmanager-table-stats', __( 'Manage League Table', 'wp-club-manager' ), 'WPCM_Meta_Box_Table_Stats::output', 'wpcm_table', 'normal', 'high' );
            add_meta_box( 'wpclubmanager-table-notes', __( 'Notes', 'wp-club-manager' ), 'WPCM_Meta_Box_Table_Notes::output', 'wpcm_table', 'normal', 'low' );
            add_meta_box( 'wpclubmanager-table-details', __( 'League Table Setup', 'wp-club-manager' ), 'WPCM_Meta_Box_Table_Details::output', 'wpcm_table', 'side' );
        }
        else
        {
            add_meta_box( 'wpclubmanager-table-details', __( 'League Table Setup', 'wp-club-manager' ), 'WPCM_Meta_Box_Table_Details::output', 'wpcm_table', 'normal', 'low' );
        }
    }

    /**
     * Rosters.
     *
     * @access private
     */
    private function rosters( $post ) {
        if ( 'publish' === $post->post_status )
        {
            add_meta_box( 'wpclubmanager-roster-players', __( 'Manage Players Roster', 'wp-club-manager' ), 'WPCM_Meta_Box_Roster_Players::output', 'wpcm_roster', 'normal', 'high' );
            add_meta_box( 'wpclubmanager-roster-staff', __( 'Manage Staff Roster', 'wp-club-manager' ), 'WPCM_Meta_Box_Roster_Staff::output', 'wpcm_roster', 'normal', 'high' );
            add_meta_box( 'wpclubmanager-roster-details', __( 'Roster Setup', 'wp-club-manager' ), 'WPCM_Meta_Box_Roster_Details::output', 'wpcm_roster', 'side' );
        }
        else
        {
            add_meta_box( 'wpclubmanager-roster-details', __( 'Roster Setup', 'wp-club-manager' ), 'WPCM_Meta_Box_Roster_Details::output', 'wpcm_roster', 'normal', 'low' );
        }
    }

    /**
     * Sponsor meta boxes.
     *
     * @access private
     */
    private function sponsors( $post ) {
        add_meta_box( 'wpclubmanager-sponsor-link', __( 'Sponsor Details', 'wp-club-manager' ), 'WPCM_Meta_Box_Sponsor_Url::output', 'wpcm_sponsor', 'normal', 'high' );
        add_meta_box( 'postimagediv', __( 'Sponsor Logo'), 'post_thumbnail_meta_box', 'wpcm_sponsor', 'side' );
    }

}

new USARDB_WPCM_Admin_Meta_Boxes();
