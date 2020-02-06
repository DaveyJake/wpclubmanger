<?php
/**
 * Single Match - Lineup Row
 *
 * @author ClubPress
 * @package WPClubManager/Templates
 * @since WPCM 2.5.0 - Custom integration.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$captain = get_post_meta( $post->ID, '_wpcm_match_captain', true );

echo '<tr>';

    if ( 'yes' === get_option( 'wpcm_lineup_show_shirt_numbers' ) )
    {
        echo '<th class="shirt-number">' . $count . '</th>';
    }

    echo '<th class="name">';

        if ( 'yes' === get_option( 'wpcm_results_show_image' ) )
        {
            echo wpcm_get_player_thumbnail( $key, 'player_thumbnail', array( 'class' => 'lineup-thumb' ) );
        }

        echo '<a href="' . get_permalink( $key ) . '">' . get_player_title( $key, get_option( 'wpcm_name_format' ) ) . '</a>';

        echo ( $key === $captain ? ' (c) ' : '' );

        if ( isset( $value['mvp'] ) )
        {
            echo '<span class="mvp" title="';
                _e( 'Player of Match', 'wp-club-manager' );
                echo '">&#9733;';
            echo '</span>';
        }

        if ( array_key_exists( 'sub', $value ) && $value['sub'] > 0 )
        {
            echo '<span class="sub">&larr; ' . get_player_title( $value['sub'], get_option( 'wpcm_name_format' ) ) . '</span>';
        }

    echo '</th>';

    foreach ( $value as $key => $stat )
    {
        if ( $stat == '0' || $stat == null )
        {
            $stat = '&mdash;';
        }

        if ( ! in_array( $key, wpcm_exclude_keys() ) && get_option( "wpcm_show_stats_{$key}" ) && get_option( "wpcm_match_show_stats_{$key}" ) )
        {
            echo "<td class='stats {$key}'>{$stat}</td>";
        }
    }

    if ( 'yes' === get_option( 'wpcm_show_stats_yellowcards' ) || 'yes' === get_option( 'wpcm_show_stats_redcards' ) )
    {
        echo '<td class="notes">';

        if ( 'yes' === get_option( 'wpcm_show_stats_yellowcards' ) && isset( $value['yellowcards'] ) && get_option( 'wpcm_show_stats_yellowcards' ) )
        {
            echo '<span class="yellowcard" title="';
                _e( 'Yellow Card', 'wp-club-manager' );
                echo '">';
                _e( 'Yellow Card', 'wp-club-manager' );
            echo '</span>';
        }

        if ( 'yes' === get_option( 'wpcm_show_stats_redcards' ) && isset( $value['redcards'] ) && get_option( 'wpcm_show_stats_redcards' ) )
        {
            echo '<span class="redcard" title="';
                _e( 'Red Card', 'wp-club-manager' );
                echo '">';
                _e( 'Red Card', 'wp-club-manager' );
            echo '</span>';
        }
        echo '</td>';
    }

echo '</tr>';