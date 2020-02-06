<?php
/**
 * Single Match - Referee
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$referee      = get_post_meta( $post->ID, 'wpcm_referee', true );
$show_referee = get_option( 'wpcm_results_show_referee' );

if ( $referee && 'yes' === $show_referee )
{
	echo '<div class="wpcm-match-referee">';
        _e( 'Referee', 'wp-club-manager' );
        echo ': ' . $referee;
    echo '</div>';
}