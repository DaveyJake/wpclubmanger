<?php
/**
 * USA Rugby Database API: WP Club Manager seasonetition adjustments.
 *
 * @author Davey Jacobson <djacobson@usa.rugby>
 * @package USA_Rugby_Database
 * @subpackage WPCM_Seasons
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if directly accessed

class USARDB_WPCM_Seasons extends WPCM_Admin_Taxonomies {
    /**
     * Primary constructor.
     *
     * @return USARDB_WPCM_Seasons
     */
    public function __construct() {
        remove_filters_for_anonymous_class( 'manage_wpcm_season_custom_column', 'WPCM_Admin_Taxonomies', 'season_custom_columns', 5 );
        remove_filters_for_anonymous_class( 'manage_edit-wpcm_season_columns', 'WPCM_Admin_Taxonomies', 'season_edit_columns' );

        add_action( 'manage_wpcm_season_custom_column', array( $this, 'season_custom_columns' ), 5, 3 );
        add_filter( 'manage_edit-wpcm_season_columns', array( $this, 'season_edit_columns' ) );
    }

    /**
     * Add custom columns for the `wpcm_season` taxonomy.
     *
     * @param array $columns The defaults for all WP columns.
     *
     * @return array The custom columns we've added.
     */
    public function season_edit_columns( $columns ) {
        $columns = array(
            'cb'      => "<input type=\"checkbox\" />",
            'move'    => '',
            'name'    => __( 'Name', 'wp-club-manager' ),
            'matches' => __( 'Matches', 'wp-club-manager' ),
            'players' => __( 'Players', 'wp-club-manager' ),
            'ID'      => __( 'ID', 'wp-club-manager' ),
        );

        return $columns;
    }

    /**
     * Additional custom columns for `wpcm_season` taxonomy.
     *
     * @global WP_Post|object $post The current post.
     *
     * @uses USARDB_WPCM_seasons::usardb_get_wpcm_player_count_by_season()
     * @uses get_term_meta()
     *
     * @param mixed  $value  The value for the column.
     * @param string $column The column name.
     * @param int    $t_id   The term ID.
     */
    public function season_custom_columns( $value, $column, $t_id ) {
        global $post;

        $season = get_terms( array(
            'taxonomy'         => 'wpcm_season',
            'term_taxonomy_id' => $t_id,
            'fields'           => 'id=>slug',
            'hide_empty'       => false,
        ) );

        switch ( $column ) {
            case 'move':
                echo '<i class="dashicons dashicons-move"></i>';
                break;
            case 'matches':
                $count = $this->usardb_get_wpcm_match_count_by_season( $t_id );
                echo '<a href="' . admin_url( 'edit.php?post_type=wpcm_match&wpcm_season=' . $season[ $t_id ] ) . '">' . ( !empty( $count ) ? $count : '0' ) . '</a>';
                break;
            case 'players':
                $count = $this->usardb_get_wpcm_player_count_by_season( $t_id );
                echo '<a href="' . admin_url( 'edit.php?post_type=wpcm_player&wpcm_season=' . $season[ $t_id ] ) . '">' . ( !empty( $count ) ? $count : '0' ) . '</a>';
                break;
            case 'ID':
                echo $t_id;
                break;
        }
    }

    /**
     * Get match counts for each venue.
     *
     * @access private
     *
     * @link {@see 'USARDB_WPCM_Venues::venue_custom_columns'}
     *
     * @uses WP_Query()
     *
     * @param int $t_id The current term's ID.
     *
     * @return int The post count for the term.
     */
    private function usardb_get_wpcm_match_count_by_season( $t_id ) {
        $args = array(
            'post_type'      => 'wpcm_match',
            'post_status'    => array( 'publish', 'future' ),
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy'         => 'wpcm_season',
                    'field'            => 'term_id',
                    'terms'            => array( $t_id ),
                    'include_children' => false,
                ),
            ),
        );

        $query = new WP_Query( $args );
        $count = (int) $query->post_count;
        wp_reset_postdata();

        return $count;
    }

    /**
     * Get player counts for each venue.
     *
     * @access private
     *
     * @link {@see 'USARDB_WPCM_Seasons::season_custom_columns'}
     *
     * @uses WP_Query()
     *
     * @param int $t_id The current term's ID.
     *
     * @return int The post count for the term.
     */
    private function usardb_get_wpcm_player_count_by_season( $t_id ) {
        $args = array(
            'post_type'      => 'wpcm_player',
            'post_status'    => array( 'publish', 'future' ),
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy'         => 'wpcm_season',
                    'field'            => 'term_id',
                    'terms'            => array( $t_id ),
                    'include_children' => false,
                ),
            ),
        );

        $query = new WP_Query( $args );
        $count = (int) $query->post_count;
        wp_reset_postdata();

        return $count;
    }

}

return new USARDB_WPCM_Seasons();
