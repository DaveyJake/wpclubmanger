<?php
/**
 * Single Player - Title
 *
 * @author  ClubPress
 * @package WPClubManager/Templates
 * @version 1.5.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo '<h1 class="entry-title">' . apply_filters( 'the_title', get_the_title() ) . '</h1>';
