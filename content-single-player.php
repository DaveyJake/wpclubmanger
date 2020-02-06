<?php
/**
 * The template for displaying product content in the single-player.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-player.php
 *
 * @author  ClubPress
 * @package WPClubManager/Templates
 * @version 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

do_action( 'wpclubmanager_before_single_player' );

echo '<article id="post-' . get_the_ID() . '" '; post_class(); echo '>';

    echo '<div class="wpcm-player-info wpcm-row">';

    	/**
    	 * wpclubmanager_single_player_image hook
    	 *
    	 * @hooked wpclubmanager_template_single_player_images - 5
    	 */
    	do_action( 'wpclubmanager_single_player_image' );

		echo '<div class="wpcm-profile-meta">';

			/**
			 * wpclubmanager_single_player_info hook
			 *
			 * @hooked wpclubmanager_template_single_player_title - 5
			 * @hooked wpclubmanager_template_single_player_meta - 10
			 */
			do_action( 'wpclubmanager_single_player_info' );

		echo '</div>';

	echo '</div>';

	echo '<div class="wpcm-profile-stats wpcm-row">';

		/**
		 * wpclubmanager_single_player_stats hook
		 *
		 * @hooked wpclubmanager_template_single_player_stats - 5
		 */
		do_action( 'wpclubmanager_single_player_stats' );

	echo '</div>';

	echo '<div class="wpcm-profile-bio wpcm-row">';

		/**
		 * wpclubmanager_single_player_bio hook
		 *
		 * @hooked wpclubmanager_template_single_player_bio - 5
		 */
		do_action( 'wpclubmanager_single_player_bio' );

	echo '</div>';

	do_action( 'wpclubmanager_after_single_player' );

echo '</article>';