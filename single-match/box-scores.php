<?php
/**
 * Single Match - Box Scores
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$sport     = get_option('wpcm_sport');
$sep       = get_option('wpcm_match_goals_delimiter');
$intgoals  = unserialize( get_post_meta( $post->ID, 'wpcm_goals', true) );
$played    = get_post_meta( $post->ID, 'wpcm_played', true );
$home_club = get_post_meta( $post->ID, 'wpcm_home_club', true );
$away_club = get_post_meta( $post->ID, 'wpcm_away_club', true );
$overtime  = get_post_meta( $post->ID, 'wpcm_overtime', true );
$shootout  = get_post_meta( $post->ID, 'wpcm_shootout', true );

if ( $played )
{
    if ( isset( $intgoals['q1'] ) )
    {
        echo '<div class="wpcm-ss-halftime wpcm-box-scores">';

        if ( get_option( 'wpcm_hide_scores') == 'yes' && ! is_user_logged_in() )
        {
            _ex( 'HT:', 'Half time', 'wp-club-manager' );
            echo ' ';
            _e( 'x', 'wp-club-manager' );
            echo ' ' . $sep . ' ';
            _e( 'x', 'wp-club-manager' );
        }
        else
        {
            _ex( 'HT:', 'Half time', 'wp-club-manager' );
            echo ' ' . $intgoals['q1']['home'] . ' ' . $sep . ' ' . $intgoals['q1']['away'];
        }

        echo '</div>';
    }
}