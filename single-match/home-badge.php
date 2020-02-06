<?php
/**
 * Single match - Home Badge
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$badges = wpcm_get_match_badges( $post->ID, 'crest-medium', array( 'class' => 'home-logo' ) );
$format = get_match_title_format();

if ( '%home% vs %away%' === $format ) {
	$badge = $badges[0];
} else {
	$badge = $badges[1];
}

echo '<div class="wpcm-match-home-club-badge">' . $badge . '</div>';