<?php
/**
 * Single Match - Venue Info
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$played     = get_post_meta( $post->ID, 'wpcm_played', true );
$venue_info = usardb_wpcm_get_match_venue( $post->ID );

if ( ! $played )
{
	echo '<div class="wpcm-match-venue-info">';

        echo '<h3>' . $venue_info['name'] . '</h3>';

		if ( 'yes' === get_option( 'wpcm_results_show_map' ) )
        {
			if ( $venue_info['address'] )
            {
				echo do_shortcode( '[map_venue id="' . $venue_info['id'] . '" width="720" height="240" marker="1"]' );
			}
		}

		echo '<div class="wpcm-match-venue-address">';

		if ( $venue_info['address'] )
        {
			echo '<h3>';
                echo _e( 'Venue Address', 'wp-club-manager' );
            echo '</h3>';

			echo '<p class="address">';
				echo stripslashes( nl2br( $venue_info['address'] ) );
			echo '</p>';
		}

		if ( $venue_info['description'] ) {
			echo '<p class="description">';
                nl2br( $venue_info['description'] );
			echo '</p>';
		}

        echo '</div>';

	echo '</div>';
}