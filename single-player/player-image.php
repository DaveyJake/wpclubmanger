<?php
/**
 * Single Player - Image
 *
 * @author  ClubPress
 * @package WPClubManager/Templates
 * @version 1.5.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

echo '<div class="wpcm-profile-image">';
    echo wpcm_get_player_thumbnail( $post->ID, 'player_single' );
echo '</div>';