<?php
/**
 * Single Player - Meta
 *
 * @author  ClubPress
 * @package WPClubManager/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

echo '<table>';

    echo '<tbody>';

	if ( 'yes' === get_option( 'wpcm_player_profile_show_number' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Eagle No.', 'wp-club-manager' );
            echo '</th>';
			echo '<td>' . get_post_meta( $post->ID, 'wpcm_number', true ) . '</td>';
		echo '</tr>';
    }

	if ( 'yes' === get_option( 'wpcm_player_profile_show_dob' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Date of Birth', 'wp-club-manager' );
            echo '</th>';
			echo '<td>' . date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ) . '</td>';
		echo '</tr>';
	}

	if ( 'yes' === get_option( 'wpcm_player_profile_show_age' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Age', 'wp-club-manager' );
            echo '</th>';
			echo '<td>' . get_age( get_post_meta( $post->ID, 'wpcm_dob', true ) ) . '</td>';
		echo '</tr>';
	}

	if ( 'yes' === get_option( 'wpcm_player_profile_show_height' ) )
    {
        $height = get_post_meta( $post->ID, 'wpcm_height', true );

        if ( $height > 0 ) {
    		echo '<tr>';
    			echo '<th>';
                    _e( 'Height', 'wp-club-manager' );
                echo '</th>';
    			echo '<td>' . $height . '</td>';
    		echo '</tr>';
    	}
    }

	if ( 'yes' === get_option( 'wpcm_player_profile_show_weight' ) )
    {
        $weight = get_post_meta( $post->ID, 'wpcm_weight', true );

        if ( $weight > 0 ) {
    		echo '<tr>';
    			echo '<th>';
                    _e( 'Weight', 'wp-club-manager' );
                echo '</th>';
    			echo '<td>' . $weight . '</td>';
    		echo '</tr>';
        }
	}

	if ( 'yes' === get_option( 'wpcm_player_profile_show_season' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Season', 'wp-club-manager' );
            echo '</th>';
			echo '<td>' . wpcm_get_player_seasons( $post->ID ) . '</td>';
		echo '</tr>';
	}

	if ( 'yes' === get_option( 'wpcm_player_profile_show_team' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Team', 'wp-club-manager' );
            echo '</th>';
			echo '<td>' . wpcm_get_player_teams( $post->ID ) . '</td>';
		echo '</tr>';
	}

	if ( 'yes' === get_option( 'wpcm_player_profile_show_position' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Position', 'wp-club-manager' );
            echo '</th>';
			echo '<td>' . wpcm_get_player_positions( $post->ID ) . '</td>';
		echo '</tr>';
	}

	if ( 'yes' === get_option( 'wpcm_player_profile_show_hometown' ) || 'yes' === get_option( 'wpcm_player_profile_show_nationality' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Hometown', 'wp-club-manager' );
            echo '</th>';
			echo '<td>';
                echo ( 'yes' === get_option( 'wpcm_player_profile_show_hometown' ) ? get_post_meta( $post->ID, 'wpcm_hometown', true ) : '' ) . '&nbsp;';
                echo ( 'yes' === get_option( 'wpcm_player_profile_show_nationality' ) ? '<div class="flag-icon flag-icon-' . get_post_meta( $post->ID, 'wpcm_natl', true ) . '"></div>' : '' );
            echo '</td>';
		echo '</tr>';
	}

	if ( 'yes' === get_option( 'wpcm_player_profile_show_joined' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Debut', 'wp-club-manager' );
            echo '</th>';
			echo '<td>' . date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) . '</td>';
		echo '</tr>';
	}

	if ( 'yes' === get_option( 'wpcm_player_profile_show_exp' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Experience', 'wp-club-manager' );
            echo '</th>';
			echo '<td>' . human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . '</td>';
		echo '</tr>';
	}

    if ( ! empty( get_post_meta( $post->ID, '_wpcm_current_club', true ) ) )
    {
        echo '<tr>';
            echo '<th>';
                _e( 'Current Club', 'wp-club-manager' );
            echo '</th>';
            echo '<td>' . get_post_meta( $post->ID, '_wpcm_current_club', true ) . '</td>';
        echo '</tr>';
    }

	if ( 'yes' === get_option( 'wpcm_player_profile_show_prevclubs' ) )
    {
		echo '<tr>';
			echo '<th>';
                _e( 'Previous Clubs', 'wp-club-manager' );
            echo '</th>';
			echo '<td>' . ( get_post_meta( $post->ID, 'wpcm_prevclubs', true ) ? get_post_meta( $post->ID, 'wpcm_prevclubs', true ) : __('None', 'wp-club-manager') ) . '</td>';
		echo '</tr>';
	}

	echo '</tbody>';

echo '</table>';