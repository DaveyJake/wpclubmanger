<?php
/**
 * USA Rugby Database API: WP Club Manager Meta Box Match Details
 *
 * @author Davey Jacobson <djacobson@usa.rugby>
 * @package USA_Rugby_Database
 * @subpackage WPCM_Meta_Box_Match_Details
 * @version WPCM 2.0.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class USARDB_WPCM_Meta_Box_Match_Details extends WPCM_Meta_Box_Match_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$wpcm_comp_status = get_post_meta( $post->ID, 'wpcm_comp_status', true );
		$neutral          = get_post_meta( $post->ID, 'wpcm_neutral', true );
		$friendly         = get_post_meta( $post->ID, 'wpcm_friendly', true );
		$referee          = get_post_meta( $post->ID, 'wpcm_referee', true );

		$comps = get_the_terms( $post->ID, 'wpcm_comp' );

		if ( is_array( $comps ) )
		{
			$comp = $comps[0]->term_id;
			$comp_slug = $comps[0]->slug;
		}
		else
		{
			$comp = 0;
			$comp_slug = null;
		}

		$seasons = get_the_terms( $post->ID, 'wpcm_season' );

		if ( is_array( $seasons ) )
		{
			$season = $seasons[0]->term_id;
		}
		else
		{
			$season = -1;
		}

		$teams = get_the_terms( $post->ID, 'wpcm_team' );

		if ( is_array( $teams ) )
		{
			$team = $teams[0]->term_id;
		}
		else
		{
			$team = -1;
		}

		$venues        = get_the_terms( $post->ID, 'wpcm_venue' );
		$default_club  = get_default_club();
		$default_venue = get_the_terms( $default_club, 'wpcm_venue' );

		if ( is_array( $venues ) )
		{
			$venue = $venues[0]->term_id;
		}
		else
		{
			if ( is_array( $default_venue ) )
			{
				$venue = $default_venue[0]->term_id;
			}
			else
			{
				$venue = -1;
			}
		}

		$time = ( 'publish' === $post->post_status || 'future' === $post->post_status ? get_the_time() : get_option( 'wpcm_match_time', '15:00' ) );
		$date = get_the_date( 'Y-m-d' );

		wpclubmanager_wp_text_input( array(
			'id'                => 'wpcm_match_date',
			'label'             => __( 'Date', 'wp-club-manager' ),
			'placeholder'       => _x( 'YYYY-MM-DD', 'placeholder', 'wp-club-manager' ),
			'value'             => $date,
			'description'       => '',
			'class'             => 'wpcm-date-picker',
			'custom_attributes' => array(
				'pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"
			),
		) );

		wpclubmanager_wp_text_input( array(
			'id'    => 'wpcm_match_kickoff',
			'label' => __( 'Time', 'wp-club-manager' ),
			'value' => $time,
			'class' => 'wpcm-time-picker',
		) );
		?>
		<p>
			<label><?php _e( 'Competition', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'orderby'    => 'tax_position',
				'meta_key'   => 'tax_position',
				'hide_empty' => false,
				'taxonomy'   => 'wpcm_comp',
				'selected'   => $comp,
				'name'       => 'wpcm_comp',
				'class'      => 'chosen_select',
			));
			?>
			<input type="text" name="wpcm_comp_status" id="wpcm_comp_status" value="<?php echo $wpcm_comp_status; ?>" placeholder="<?php _e( 'Round (Optional)', 'wp-club-manager' ); ?>" />
			<label class="selectit wpcm-cb-block friendly"><input type="checkbox" name="wpcm_friendly" id="wpcm_friendly" value="1" <?php checked( true, $friendly ); ?> /><?php _e( 'Friendly?', 'wp-club-manager' ); ?></label>
		</p>
		<p>
			<label><?php _e( 'Season', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories( array(
				'orderby'    => 'tax_position',
				'meta_key'   => 'tax_position',
				'hide_empty' => false,
				'taxonomy'   => 'wpcm_season',
				'selected'   => $season,
				'name'       => 'wpcm_season',
				'class'      => 'chosen_select',
			));
			?>
		</p>
		<?php
		if ( is_club_mode() && has_teams() )
		{
			?>
			<p>
				<label><?php _e( 'Team', 'wp-club-manager' ); ?></label>
				<?php
				wp_dropdown_categories(array(
					'orderby'    => 'tax_position',
					'meta_key'   => 'tax_position',
					'hide_empty' => false,
					'taxonomy'   => 'wpcm_team',
					'selected'   => $team,
					'name'       => 'wpcm_match_team',
					'class'      => 'chosen_select',
				));
				?>
			</p>
			<?php
		}
		?>
		<p>
			<label><?php _e( 'Venue', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories( array(
				'show_option_none' => __( 'None' ),
				'orderby'          => 'title',
				'hide_empty'       => false,
				'taxonomy'         => 'wpcm_venue',
				'selected'         => $venue,
				'name'             => 'wpcm_venue',
				'class'            => 'chosen_select',
			) );
			?>
			<label class="selectit wpcm-cb-block">
				<input type="checkbox" name="wpcm_neutral" id="wpcm_neutral" value="1" <?php checked( true, $neutral ); ?> />
				<?php _e( 'Neutral?', 'wp-club-manager' ); ?>
			</label>
		</p>
		<?php
		if ( 'yes' === get_option( 'wpcm_results_show_attendance' ) )
		{
			wpclubmanager_wp_text_input( array( 'id' => 'wpcm_attendance', 'label' => __( 'Attendance', 'wp-club-manager' ) ) );
		}

		if ( 'yes' === get_option( 'wpcm_results_show_referee' ) )
		{
			wpclubmanager_wp_text_input( array(
				'id'    => 'wpcm_referee',
				'label' => __( 'Referee', 'wp-club-manager' ),
				'class' => 'regular-text',
				'value' => $referee,
			) );
		}
		/*else
		{
			$option_list = get_option( 'wpcm_referee_list', array() );
			if ( $option_list )
			{
				?>
				<p>
					<label><?php _e( 'Referee', 'wp-club-manager' ); ?></label>
					<select name='wpcm_referee' id="wpcm_referee" class="combify-input">
					<?php
						foreach( $option_list as $option ) {
							?>
							<option value="<?php echo $option; ?>"<?php echo ( $option == $referee ? ' selected' : null ); ?>><?php echo $option; ?></option>
							<?php
						}
					?>
					</select>
				</p>
				<?php
			}
		}*/

		do_action( 'wpclubmanager_admin_match_details', $post->ID );
	}
}
