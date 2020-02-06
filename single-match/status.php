<?php
/**
 * Single Match - Status
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$overtime = get_post_meta( $post->ID, 'wpcm_overtime', true );
$shootout = get_post_meta( $post->ID, 'wpcm_shootout', true );

if ( $overtime || $shootout )
{
	echo '<div class="wpcm-match-status">';

		do_action( 'wpclubmanager_before_match_status' );

		if ( $overtime )
        {
			echo '<span class="wpcm-match-overtime">';
                _e( 'AET', 'wp-club-manager' );
            echo '</span>';
		}

		do_action( 'wpclubmanager_after_match_status' );

	echo '</div>';
}