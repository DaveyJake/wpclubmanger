<?php
/**
 * Single Match - Report
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$played = get_post_meta( $post->ID, 'wpcm_played', true );

if ( $played )
{
	if ( get_the_content() )
    {
		echo '<div class="wpcm-match-report">';
			echo '<h3>';
                _e( 'Match Report', 'wp-club-manager' );
            echo '</h3>';
			echo '<div class="wpcm-entry-content">';
                the_content();
			echo '</div>';
		echo '</div>';
	}
}
else
{
	if ( has_excerpt() )
    {
		echo '<div class="wpcm-match-report wpcm-match-preview">';
			echo '<h3>';
                _e( 'Match Preview', 'wp-club-manager' );
            echo '</h3>';
			echo '<div class="wpcm-entry-content">';
				the_excerpt();
			echo '</div>';
		echo '</div>';
	}
}