<?php
/**
 * USA Rugby Database API: WP Club Manger Match API
 *
 * @author Davey Jacobson <djacobson@usa.rugby>
 * @package USA_Rugby_Database
 * @subpackage WPCM_Meta_Box_Match_Result
 * @since USARDB 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if directly accessed

class USARDB_WPCM_Meta_Box_Match_Result extends WPCM_Meta_Box_Match_Result {

    /**
     * Output the metabox
     */
    public static function output( $post ) {

        wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

        $played    = get_post_meta( $post->ID, 'wpcm_played', true );
        $postponed = get_post_meta( $post->ID, '_wpcm_postponed', true );
        $walkover  = get_post_meta( $post->ID, '_wpcm_walkover', true );

        // Postponed Args.
        $postponed_args = array(
            'id'            => '_wpcm_walkover',
            'value'         => $walkover,
            'class'         => 'chosen_select',
            'label'         => '',
            'wrapper_class' => 'wpcm-postponed-result',
            'options'       => array(
                ''         => __( 'To be rescheduled', 'wp-club-manager' ),
                'home_win' => __( 'Home win', 'wp-club-manager' ),
                'away_win' => __( 'Away win', 'wp-club-manager' ),
            ),
        );

        // Goals.
        $wpcm_goals = (array) unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) );
        $goals      = array_merge( array( 'total' => array( 'home' => '0', 'away' => '0' ) ), $wpcm_goals );

        // Overtime.
        $overtime = get_post_meta( $post->ID, 'wpcm_overtime', true );

        // Bonus points.
        $wpcm_bonus = (array) unserialize( get_post_meta( $post->ID, 'wpcm_bonus', true ) );
        $bonus      = array_merge( array( 'home' => '0', 'away' => '0' ), $wpcm_bonus );

        echo '<p>';
            echo '<label class="selectit">';
                echo '<input type="checkbox" name="wpcm_played" id="wpcm_played" value="1" ' . checked( true, $played, false ) . ' />';
                _e( 'Result', 'wp-club-manager' );
            echo '</label>';
        echo '</p>';
        echo '<p>';
            echo '<label class="selectit">';
                echo '<input type="checkbox" name="_wpcm_postponed" id="_wpcm_postponed" value="1" ' . checked( true, $postponed, false ) . ' />';
                _e( 'Postponed', 'wp-club-manager' );
            echo '</label>';
        echo '</p>';

        // Postponed meta box.
        wpclubmanager_wp_select( $postponed_args );

        // Results table.
        echo '<div id="results-table">';

        if ( 'yes' === get_option( 'wpcm_match_box_scores' ) )
        {
            echo '<table class="box-scores-table">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<td>&nbsp;</td>';
                        echo '<th>';
                            _ex( 'Home', 'team', 'wp-club-manager' );
                        echo '</th>';
                        echo '<th>';
                            _ex( 'Away', 'team', 'wp-club-manager' );
                        echo '</th>';
                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                    $ht_goals  = (array) unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) );
                    $box_goals = array_merge( array( 'q1' => array( 'home' => '0', 'away' => '0' ) ), $ht_goals );
                    echo '<tr class="wpcm-ss-admin-tr-last">';
                        echo '<th align="right">';
                            _e( 'Half Time', 'wp-club-manager' );
                        echo '</th>';
                        echo '<td><input type="text" name="wpcm_goals[q1][home]" id="wpcm_goals_q1_home" value="' . (int) $box_goals['q1']['home'] . '" size="3" /></td>';
                        echo '<td><input type="text" name="wpcm_goals[q1][away]" id="wpcm_goals_q1_away" value="' . (int) $box_goals['q1']['away'] . '" size="3" /></td>';
                    echo '</tr>';
                echo '</tbody>';
            echo '</table>';
        }
            echo '<table class="final-score-table">';
                if ( 'yes' !== get_option( 'wpcm_results_box_scores' ) )
                {
                    echo '<thead>';
                        echo '<tr>';
                            echo '<td>&nbsp;</td>';
                            echo '<th>';
                                _ex( 'Home', 'team', 'wp-club-manager' );
                            echo '</th>';
                            echo '<th>';
                                _ex( 'Away', 'team', 'wp-club-manager' );
                            echo '</th>';
                        echo '</tr>';
                    echo '</thead>';
                }
                echo '<tbody>';
                    do_action( 'wpclubmanager_admin_results_table', $post->ID );
                    echo '<tr>';
                        echo '<th align="right">';
                            _e( 'Final Score', 'wp-club-manager' );
                        echo '</th>';
                        echo '<td><input type="text" name="wpcm_goals[total][home]" id="wpcm_goals_total_home" value="' . (int) $goals['total']['home'] . '" size="3" /></td>';
                        echo '<td><input type="text" name="wpcm_goals[total][away]" id="wpcm_goals_total_away" value="' . (int) $goals['total']['away'] . '" size="3" /></td>';
                    echo '</tr>';
                echo '</tbody>';
            echo '</table>';

            echo '<table class="wpcm-results-bonus">';
                echo '<tbody>';
                    echo '<tr>';
                        echo '<th align="right">';
                            _e( 'Bonus Points', 'wp-club-manager' );
                        echo '</th>';
                        echo '<td><input type="text" name="wpcm_bonus[home]" id="wpcm_bonus_home" value="' . (int) $bonus['home'] . '" size="3" /></td>';
                        echo '<td><input type="text" name="wpcm_bonus[away]" id="wpcm_bonus_away" value="' . (int) $bonus['away'] . '" size="3" /></td>';
                    echo '</tr>';
                echo '</tbody>';
            echo '</table>';

        echo '</div>';

        do_action( 'wpclubmanager_admin_after_results_table', $post->ID );

    }

}