<?php
/**
 * Single Match - Lineup
 *
 * @author  ClubPress
 * @package WPClubManager/Templates
 * @since 2.5.0 - Custom integration.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$played                   = get_post_meta( $post->ID, 'wpcm_played', true );
$players                  = unserialize( get_post_meta( $post->ID, 'wpcm_players', true ) );
$wpcm_player_stats_labels = wpcm_get_preset_labels();
$subs_not_used            = get_post_meta( $post->ID, '_wpcm_match_subs_not_used', true );

if ( $played && $players )
{
    if ( array_key_exists( 'lineup', $players ) && is_array( $players['lineup'] ) )
    {
        echo '<div class="wpcm-match-stats-start">';

            echo '<h3>';
                _e( 'Lineup', 'wp-club-manager' );
            echo '</h3>';

            echo '<table class="wpcm-lineup-table">';
                echo '<thead>';
                    echo '<tr>';

                    if ( 'yes' === get_option( 'wpcm_lineup_show_shirt_numbers' ) )
                    {
                        echo '<th class="shirt-number"></th>';
                    }

                    echo '<th class="name">';
                        _e( 'Name', 'wp-club-manager' );
                    echo '</th>';

                    foreach ( $wpcm_player_stats_labels as $key => $val )
                    {
                        if ( ! in_array( $key, wpcm_exclude_keys() ) && get_option( "wpcm_show_stats_{$key}" ) && get_option( "wpcm_match_show_stats_{$key}" ) )
                        {
                            echo '<th class="' . $key . '">' . $val . '</th>';
                        }
                    }

                    if ( 'yes' === get_option( 'wpcm_show_stats_yellowcards' ) &&
                         'yes' === get_option( 'wpcm_match_show_stats_yellowcards' ) ||
                         'yes' === get_option( 'wpcm_show_stats_redcards' ) &&
                         'yes' === get_option( 'wpcm_match_show_stats_redcards' ) ) {

                        echo '<th class="notes">';
                            _e( 'Cards', 'wp-club-manager' );
                        echo '</th>';
                    }

                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                    $count = 0;

                    foreach ( $players['lineup'] as $key => $value ) {
                        $count++;

                        wpclubmanager_get_template( 'single-match/lineup-row.php', array(
                            'key'   => $key,
                            'value' => $value,
                            'count' => $count,
                        ) );
                    }

                echo '</tbody>';
            echo '</table>';
        echo '</div>';
    }

    if ( array_key_exists( 'subs', $players ) && is_array( $players['subs'] ) || is_array( $subs_not_used ) )
    {
        echo '<div class="wpcm-match-stats-subs">';

            echo '<h3>';
                _e( 'Subs', 'wp-club-manager' );
            echo '</h3>';

            echo '<table class="wpcm-subs-table">';
                echo '<thead>';
                    echo '<tr>';

                        if ( 'yes' === get_option( 'wpcm_lineup_show_shirt_numbers' ) ) {
                            echo '<th class="shirt-number"></th>';
                        }

                        echo '<th class="name">';
                            _e( 'Name', 'wp-club-manager' );
                        echo '</th>';

                        foreach ( $wpcm_player_stats_labels as $key => $val ) {
                            if ( ! in_array( $key, wpcm_exclude_keys() ) &&
                                 'yes' === get_option( "wpcm_show_stats_{$key}" ) &&
                                 'yes' === get_option( "wpcm_match_show_stats_{$key}" ) ) {

                                echo '<th class="' . $key . '">' . $val . '</th>';
                            }
                        }

                        if ( 'yes' === get_option( 'wpcm_show_stats_yellowcards' ) && get_option( 'wpcm_match_show_stats_yellowcards' ) ||
                             'yes' === get_option( 'wpcm_show_stats_redcards' ) && get_option( 'wpcm_match_show_stats_redcards' ) ) {

                            echo '<th class="notes">';
                                _e( 'Cards', 'wp-club-manager' );
                            echo '</th>';
                        }

                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ( $players['subs'] as $key => $value ) {
                    $count ++;

                    wpclubmanager_get_template( 'single-match/lineup-row.php', array(
                        'key'   => $key,
                        'value' => $value,
                        'count' => $count
                    ) );
                }

                if ( is_array( $subs_not_used ) ) {
                    foreach( $subs_not_used as $key => $value ) {
                        $count ++;

                        wpclubmanager_get_template( 'single-match/lineup-row.php', array(
                            'key'   => $key,
                            'value' => array(),
                            'count' => $count
                        ) );
                    }
                }

                echo '</tbody>';
            echo '</table>';
        echo '</div>';
    }
}