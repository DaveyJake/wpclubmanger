<?php
/**
 * Single Player - Bio
 *
 * @author  ClubPress
 * @package WPClubManager/Templates
 * @version 1.5.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( get_the_content() ) {

	echo '<div class="wpcm-entry-content">';

	the_content();

	echo '</div>';

}