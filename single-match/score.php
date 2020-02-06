<?php
/**
 * Single Match - Score
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$played = get_post_meta( $post->ID, 'wpcm_played', true );
$score  = wpcm_get_match_result( $post->ID );

echo '<div class="wpcm-match-score">';
	echo $score[1];
	echo '<span class="wpcm-match-score-delimiter">' . ( $played ? $score[3] : get_option( 'wpcm_match_clubs_separator' ) ) . '</span>';
	echo $score[2];
echo '</div>';