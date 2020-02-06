<?php
/**
 * Single Player - Stats Table Wrapper
 *
 * @author  ClubPress
 * @package WPClubManager/Templates
 * @version 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$stats = get_wpcm_player_stats( $post->ID );

if ( is_club_mode() )
{
	$teams = wpcm_get_ordered_post_terms( $post->ID, 'wpcm_team' );
}
else
{
	$teams = null;
}

$seasons = wpcm_get_ordered_post_terms( $post->ID, 'wpcm_season' );

// Reverse chronological order.
asort( $seasons );

if ( is_array( $teams ) && count( $teams ) > 1 )
{
	foreach ( $teams as $team )
    {
		$rand = rand( 1, 99999 );
		$name = $team->name;

		if ( $team->parent )
        {
			$parent_team = get_term( $team->parent, 'wpcm_team' );
			$name        .= ' (' . $parent_team->name . ')';
		}

		echo '<div class="wpcm-profile-stats-block">';

			echo "<h4>{$name}</h4>";

			echo '<ul class="stats-tabs-' . $rand . ' stats-tabs-multi">';

				echo '<li class="tabs-multi">';
                    echo '<a href="#wpcm_team-0_season-0-' . $rand . '">';
                        printf( __( 'All %s', 'wp-club-manager' ), __( 'Seasons', 'wp-club-manager' ) );
                    echo '</a>';
                echo '</li>';

				if ( is_array( $seasons ) )
                {
                    foreach( $seasons as $season )
                    {
					   echo '<li><a href="#wpcm_team-' . $team->term_id . '_season-' . $season->term_id . '">' . $season->name . '</a></li>';
                    }
                }

            echo '</ul>';

			echo '<div id="wpcm_team-0_season-0-' . $rand . '" class="tabs-panel-' . $rand . ' tabs-panel-multi stats-table-season-' . $rand . '">';
                wpclubmanager_get_template( 'single-player/stats-table.php', array(
                    'stats'  => $stats,
                    'team'   => $team->term_id,
                    'season' => 0
                ) );
			echo '</div>';

			if ( is_array( $seasons ) )
            {
                foreach( $seasons as $season ) {
    				echo '<div id="wpcm_team-' . $team->term_id . '_season-' . $season->term_id . '" class="tabs-panel-' . $rand . ' tabs-panel-multi stats-table-season-' . $rand . '" style="display: none;">';
                        wpclubmanager_get_template( 'single-player/stats-table.php', array(
                            'stats'  => $stats,
                            'team'   => $team->term_id,
                            'season' => $season->term_id
                        ) );
				    echo '</div>';
                }
            }
		echo '</div>';

		echo '<script type="text/javascript"> ' .
			'(function($) { ' .
				"$( '.stats-tabs-{$rand} a' ).click( function() { " .
					"var t = $( this ).attr( 'href' ); " .
					"$( this ).parent().addClass( 'tabs-multi {$rand}' ).siblings( 'li' ).removeClass( 'tabs-multi {$rand}' ); " .
					"$( this ).parent().parent().parent().find( '.tabs-panel-{$rand}' ).hide(); " .
					'$( t ).show(); ' .
					'return false; ' .
				'}); ' .
			'})( jQuery ); ' .
		'</script> ';
	}
}
else
{
	echo '<ul class="stats-tabs">';

		echo '<li class="tabs">';
            echo '<a href="#wpcm_team-0_season-0">';
                printf( __( 'All %s', 'wp-club-manager' ), __( 'Seasons', 'wp-club-manager' ) );
            echo '</a>';
        echo '</li>';

		if ( is_array( $seasons ) )
        {
            foreach( $seasons as $season )
            {
                echo '<li><a href="#wpcm_team-0_season-' . $season->term_id . '">' . $season->name . '</a></li>';
            }
        }
	echo '</ul>';

	if ( is_array( $seasons ) )
    {
        foreach( $seasons as $season ) {
    		echo '<div id="wpcm_team-0_season-' . $season->term_id . '" class="tabs-panel" style="display: none;">';
                wpclubmanager_get_template( 'single-player/stats-table.php', array(
                    'stats'  => $stats,
                    'team'   => 0,
                    'season' => $season->term_id
                ) );
            echo '</div>';
        }
    }

	echo '<div id="wpcm_team-0_season-0" class="tabs-panel">';
        wpclubmanager_get_template( 'single-player/stats-table.php', array(
            'stats'  => $stats,
            'team'   => 0,
            'season' => 0
        ) );
	echo '</div>';
}
