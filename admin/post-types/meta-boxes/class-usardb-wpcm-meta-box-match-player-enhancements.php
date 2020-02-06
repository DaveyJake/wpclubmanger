<?php
/**
 * WP Club Manager API: Match Players
 *
 * @package USA_Rugby
 * @subpackage WPCM_Meta_Box_Match_Player_Enhancements
 * @since USA_Rugby 2.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class USARDB_WPCM_Meta_Box_Match_Player_Enhancements {
    /**
     * Primary constructor.
     *
     * @return USAR_WPCM_Admin_Match_Players
     */
    public function __construct() {
        if ( isset( $_POST['post_type'] ) && 'wpcm_match' !== $_POST['post_type'] ) {
            return;
        }

        // Jersey Number
        add_action( 'admin_head', array( $this, 'wpcm_player_shirt_number_css' ) );
        add_action( 'admin_footer', array( $this, 'wpcm_player_shirt_number_js' ) );
        add_filter( 'wpcm_players_shirt_number_output', array( $this, 'wpcm_player_shirt_number_html' ), 10, 6 );
        // Squad Number
        add_filter( 'wpcm_player_squad_number_output', array( $this, 'wpcm_player_squad_number_html' ), 10, 2 );
    }

    /**
     * Show badge number when hovered over.
     *
     * @link {@see 'admin_enqueue_styles'}
     * @return mixed
     */
    public function wpcm_player_shirt_number_css() {
        echo '<style id="wpcm-player-shirt-number-css"> ' .
                 '.wpcm-match-players-table tr td.names span.name span.capped { white-space: nowrap; opacity: 0.25; float: right; } ' .
                 '.wpcm-match-players-table tr:hover td.names span.name span.capped { opacity: 0.75; } ' .
             '</style>';
    }

    /**
     * Enable edit-ability when player is selected for match.
     *
     * @link {@see 'admin_enqueue_scripts'}
     * @return mixed
     */
    public function wpcm_player_shirt_number_js() {
        echo '<script id="wpcm-player-shirt-number-js"> ' .
                '( function( $ ) { ' .
                    '$( "#wpcm_players .player-select" ).on( "change", function( e ) { ' .
                        'var input  = $( this ).parents( "td.names" ).prev().children( "input" ), ' .
                            'others = $( this ).parents( "td.names" ).siblings(); ' .
                        'if ( e.target.checked ) { ' .
                            'input.removeAttr( "disabled" ); ' .
                            'others.each(function() { $( this ).find( "input" ).removeAttr( "disabled" ); $( this ).find( "select" ).removeAttr( "disabled" ); }); ' .
                        '} else { ' .
                            'input.attr( "disabled", "disabled" ); ' .
                            'others.each(function() { $( this ).find( "input" ).attr( "disabled", "disabled" ); $( this ).find( "select" ).attr( "disabled", "disabled" ); }); ' .
                        '} ' .
                    '}); ' .
                '}( jQuery ) ); '.
            '</script>';
    }

    /**
     * Custom shirt numbers on WPCM admin matches.
     *
     * @link {@see 'wpcm_players_shirt_number_output'}
     *
     * @param mixed $shirt            HTML to inject.
     * @param int   $player_id        Player post ID number.
     * @param array $selected_players Currently selected players.
     * @param mixed $type             Starter or reserve player.
     * @param int   $count            Total players selected.
     * @param bool  $played           If match has already been played.
     *
     * @return mixed
     */
    public function wpcm_player_shirt_number_html( $shirt, $player_id, $selected_players, $type, $count, $played ) {
        if ( '' !== $shirt )
        {
            $shirt  = null;
            $jersey = ( ! empty( $selected_players[ $type ][ $player_id ]['shirtnumber'] ) ? $selected_players[ $type ][ $player_id ]['shirtnumber'] : $count );

            $shirt = '<td class="shirt-number">';
                $shirt .= "<input type='text' data-player='{$player_id}' name='wpcm_players[{$type}][{$player_id}][shirtnumber]' value='{$jersey}'";
                    $shirt .= ( ! $played ? ' disabled' : '' );
                $shirt .= "/>";
            $shirt .= '</td>';
        }

        return $shirt;
    }

    /**
     * Add `Eagles #` or `Debut` in front of players' jersey number.
     *
     * @link {@see 'wpcm_player_squad_number_output'}
     *
     * @param int $squad_number Current number.
     * @param int $player_id    Player post ID number.
     *
     * @return mixed
     */
    public function wpcm_player_squad_number_html( $squad_number, $player_id ) {
        static $reserved = array( 'USA', 'United States', 'united-states', 'usa-rugby', 'usa-eagles', 'USA Rugby', 'USA Eagles' );

        $sport   = get_option( 'wpcm_sport' );
        $country = get_option( 'wpcm_default_country' );
        $club_id = get_option( 'wpcm_default_club' );

        $club = get_post( $club_id );
        $slug = $club->post_name;
        $name = $club->post_title;

        if ( 'rugby' !== $sport || 'us' !== $country ||
             ! in_array( $slug, $reserved ) ||
             ! in_array( $name, $reserved ) ) {

            return $squad_number;
        }

        $number       = get_post_meta( $player_id, 'wpcm_number', true );
        $squad_number = '';

        if ( $number )
        {
            $squad_number = "<span class='capped'>Eagle #{$number}</span>";
        }
        else
        {
            $squad_number = "<span class='capped'>Debut</span>";
        }

        return $squad_number;
    }
}

return new USARDB_WPCM_Meta_Box_Match_Player_Enhancements();
