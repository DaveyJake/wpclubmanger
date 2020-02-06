<?php
/**
 * Single Match - Away Club
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$side = wpcm_get_match_clubs( $post->ID );

echo '<div class="wpcm-match-away-club">' . $side[1] . '</div>';