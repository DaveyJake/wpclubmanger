<?php
/**
 * USA Rugby Database API: WP Club Manager Admin Post Types
 *
 * @author     Davey Jacobson <djacobson@usa.rugby>
 * @category   Admin
 * @package    USA_Rugby_Database
 * @subpackage WPCM_Admin_Post_Types
 * @version    1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'USARDB_WPCM_Admin_Post_Types' ) ) :

class USARDB_WPCM_Admin_Post_Types extends WPCM_Admin_Post_Types {

    /**
     * Primary constructor.
     *
     * @return USARDB_WPCM_Admin_Post_Types
     */
    public function __construct() {
        remove_filters_for_anonymous_class( 'manage_wpcm_match_posts_custom_column', 'WPCM_Admin_Post_Types', 'render_match_columns', 2 );
        remove_filters_for_anonymous_class( 'manage_wpcm_player_posts_columns', 'WPCM_Admin_Post_Types', 'player_columns' );
        remove_filters_for_anonymous_class( 'manage_wpcm_player_posts_custom_column', 'WPCM_Admin_Post_Types', 'render_player_columns', 2 );
        remove_filters_for_anonymous_class( 'manage_wpcm_roster_posts_custom_column', 'WPCM_Admin_Post_Types', 'render_roster_columns', 2 );
        remove_filters_for_anonymous_class( 'quick_edit_custom_box', 'WPCM_Admin_Post_Types', 'quick_edit', 10 );

        add_action( 'manage_wpcm_match_posts_custom_column', array( $this, 'render_match_columns' ), 2 );
        add_filter( 'manage_wpcm_player_posts_columns', array( $this, 'player_columns' ) );
        add_action( 'manage_wpcm_player_posts_custom_column', array( $this, 'render_player_columns' ), 2 );
        // Custom: Sortable Player Columns
        add_filter( 'manage_edit-wpcm_player_sortable_columns', array( $this, 'wpcm_player_sortable_columns' ) );
        add_action( 'pre_get_posts', array( $this, 'wpcm_player_badge_orderby' ), 10, 1 );

        // Roster columns.
        add_action( 'manage_wpcm_roster_posts_custom_column', array( $this, 'render_roster_columns' ), 2 );

        // Quick Edit
        add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit' ), 10, 2 );
    }

    /**
     * Ouput custom columns for matches.
     *
     * @param string $column
     */
    public function render_match_columns( $column ) {

        global $post;

        switch ( $column ) {
            case 'name':
                $edit_link = get_edit_post_link( $post->ID );
                $title     = _draft_or_post_title();

                echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

                _post_states( $post );

                echo '</strong>';

                $friendly = get_post_meta( $post->ID, 'wpcm_friendly', true );
                if ( $friendly ) {
                    echo '<span class="red" title="Non-Test">*</span>';
                }

                if ( $post->post_parent > 0 ) {
                    echo '&nbsp;&nbsp;&larr; <a href="' . get_edit_post_link( $post->post_parent ) . '">' . get_the_title( $post->post_parent ) . '</a>';
                }

                // Excerpt view
                if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
                    echo apply_filters( 'the_excerpt', $post->post_excerpt );
                }

                //$this->_render_match_row_actions( $post, $title );

                get_inline_data( $post );

                $played = get_post_meta( $post->ID, 'wpcm_played', true );
                $score  = wpcm_get_match_result( $post->ID );
                if ( taxonomy_exists( 'wpcm_team' ) )
                {
                    $team = get_the_terms( $post->ID, 'wpcm_team' );
                }
                else
                {
                    $team = null;
                }
                $comp   = get_the_terms( $post->ID, 'wpcm_comp' );
                $season = get_the_terms( $post->ID, 'wpcm_season' );
                $venue  = usardb_wpcm_get_match_venue( $post );
                //$home_goals = get_post_meta( $post->ID, 'wpcm_home_goals', true );
                //$away_goals = get_post_meta( $post->ID, 'wpcm_away_goals', true );
                $referee    = get_post_meta( $post->ID, 'wpcm_referee', true );
                $attendance = get_post_meta( $post->ID, 'wpcm_attendance', true );
                $friendly   = get_post_meta( $post->ID, 'wpcm_friendly', true );
                $neutral    = get_post_meta( $post->ID, 'wpcm_neutral', true );
                $goals      = array_merge(
                    array( 'total' => array( 'home' => 0, 'away' => 0 ) ),
                    (array) unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) )
                );
                /* Custom inline data for wpclubmanager. */
                echo '
                    <div class="hidden" id="wpclubmanager_inline_' . $post->ID . '">
                        ' . ( $team ? '<span class="team">' . $team[0]->slug . '</span>' : '' ) .'
                        <span class="comp">' . $comp[0]->slug . '</span>
                        <span class="season">' . $season[0]->slug . '</span>
                        <span class="venue">' . $venue['slug'] . '</span>
                        <span class="played">' . $played . '</span>
                        <span class="score">' . $score[0] . '</span>
                        <span class="home-goals">' . $goals['total']['home'] . '</span>
                        <span class="away-goals">' . $goals['total']['away'] . '</span>
                        <span class="referee">' . $referee . '</span>
                        <span class="attendance">' . $attendance . '</span>
                        <span class="friendly">' . $friendly . '</span>
                        <span class="neutral">' . $neutral . '</span>
                    </div>
                ';
                break;
            case 'team':
                if ( taxonomy_exists( 'wpcm_team' ) )
                {
                    $terms = get_the_terms( $post->ID, 'wpcm_team' );

                    if ( $terms )
                    {
                        foreach ( $terms as $term )
                        {
                            $teams[] = $term->name;
                        }

                        $output = join( ', ', $teams );
                    }
                    else
                    {
                        $output = '';
                    }

                    echo $output;
                }
                break;
            case 'comp':
                $terms = get_the_terms( $post->ID, 'wpcm_comp' );
                echo $terms[0]->name;
                break;
            case 'season':
                $terms = get_the_terms( $post->ID, 'wpcm_season' );
                echo $terms[0]->name;
                break;
            case 'dates':
                if ( 'future' === get_post_status( $post->ID ) )
                {
                    $date = __( 'Scheduled', 'wp-club-manager' );
                }
                elseif ( 'publish' === get_post_status( $post->ID ) )
                {
                    $played    = get_post_meta( $post->ID, 'wpcm_played', true );
                    $postponed = get_post_meta( $post->ID, '_wpcm_postponed', true );

                    if ( empty( $played ) )
                    {
                        $date = '<span class="red">' . __( 'Awaiting result', 'wp-club-manager' ) . '</span>';
                    }
                    else
                    {
                        if ( $postponed )
                        {
                            $date = '<span>' . __( 'Postponed', 'wp-club-manager' ) . '</span>';
                        }
                        else
                        {
                            $date = '<span class="green">' . __( 'Played', 'wp-club-manager' ) . '</span>';
                        }
                    }
                }
                else
                {
                    $date = ucfirst( get_post_status( $post->ID ) );
                }
                echo $date;
                echo '<br />';
                echo '<abbr title="' . get_the_date ( 'Y/m/d' ) . ' ' . get_the_time ( 'H:i:s' ) . '">' . get_the_date ( 'Y/m/d' ) . '</abbr>';
                break;
            case 'kickoff':
                echo get_the_time( get_option( 'time_format' ) );
                break;
            case 'score':
                $score = wpcm_get_match_result( $post->ID );
                echo $score[0];
                break;
        }
    }

    /**
     * Make player admin columns sortable with metadata.
     *
     * @since USARDB 1.0.0
     *
     * @link {@see 'pre_get_posts'}
     *
     * @param WP_Query|object $query The current `$query` to modify for sorting.
     */
    public function wpcm_player_badge_orderby( $query ) {
        if ( ! ( is_admin() || $query->is_main_query() ) ) {
            return;
        }

        if ( 'wpcm_number' === $query->get( 'orderby' ) ) {
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'meta_key', 'wpcm_number' );
            $query->set( 'meta_type', 'NUMERIC' );
        }
        elseif ( 'last' === $query->get( 'orderby' ) ) {
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'meta_key', '_usar_last' );
            $query->set( 'meta_type', 'DATE' );
        }
    }

    /**
     * Rename and/or add to the `wpcm_player` columns.
     *
     * @since USARDB 1.0.0
     *
     * @link {@see "manage_{$post_type}_posts_columns"}
     *
     * @param array $existing_columns Array of columns to modify.
     */
    public function player_columns( $existing_columns ) {
        if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
            $existing_columns = array();
        }

        unset( $existing_columns['title'], $existing_columns['date'], $existing_columns['comments'] );

        $columns          = array();
        $columns['cb']    = $existing_columns['cb'];
        $columns['image'] = __( '', 'wp-club-manager' );
        $columns['name']  = __( 'Name', 'wp-club-manager' );

        if ( is_league_mode() ) {
            $columns['club'] = __( 'Club', 'wp-club-manager' );
        }

        if ( 'yes' === get_option( 'wpcm_player_profile_show_number' ) ) {
            $columns['number'] = __( 'Badge', 'wp-club-manager' );
        }

        if ( 'yes' === get_option( 'wpcm_player_profile_show_nationality' ) ) {
            $columns['flag'] = __( 'Birthplace', 'usardb' );
        }

        if ( 'yes' === get_option( 'wpcm_player_profile_show_position' ) ) {
            $columns['position'] = __( 'Positions', 'wp-club-manager' );
        }

        if ( 'yes' === get_option( 'wpcm_player_profile_show_joined' ) ) {
            $columns['debut'] = __( 'Debut Date', 'usardb' );
            $columns['last']  = __( 'Last Played On', 'usardb' );
        }

        return wp_parse_args( $columns, $existing_columns );
    }

    /**
     * Add `debut` and `last played` columns to `wpcm_player`.
     *
     * @since USARDB 1.0.0
     *
     * @link {@see "manage_{$post_type}_posts_custom_column"}
     *
     * @param string $column  The column populate with data.
     * @param int    $post_id The post ID to retrieve data from.
     */
    public function render_player_columns( $column ) {

        global $post;

        switch ( $column ) {
            case 'image':
                echo get_the_post_thumbnail( $post->ID, 'player_thumbnail' );
                break;
            case 'name':
                $edit_link = get_edit_post_link( $post->ID );
                $title     = _draft_or_post_title();

                echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

                _post_states( $post );

                echo '</strong>';

                if ( $post->post_parent > 0 ) {
                    echo '&nbsp;&nbsp;&larr; <a href="' . get_edit_post_link( $post->post_parent ) . '">' . get_the_title( $post->post_parent ) . '</a>';
                }

                // Excerpt view
                if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
                    echo apply_filters( 'the_excerpt', $post->post_excerpt );
                }

                //$this->_render_match_row_actions( $post, $title );

                get_inline_data( $post );

                $fname = get_post_meta( $post->ID, '_wpcm_firstname', true );
                $lname = get_post_meta( $post->ID, '_wpcm_lastname', true );
                if ( is_league_mode() ) {
                    $player_club = get_post_meta( $post->ID, '_wpcm_player_club', true );
                }

                // $positions = get_the_terms($post->ID, 'wpcm_position');
                // if( $positions ) {
                //  foreach( $positions as $term ) {
                //      $positions = $term->slug;
                //  }
                //  var_dump($positions);
                //  $position = $positions;
                // } else {
                //  $position = '';
                // }

                /* Custom inline data for wpclubmanager. */
                echo '
                    <div class="hidden" id="wpclubmanager_inline_' . $post->ID . '">
                        <div class="fname">' . $fname . '</div>
                        <div class="lname">' . $lname . '</div>
                        ' . ( is_league_mode() ? '<div class="player_club">' . $player_club . '</div>' : '' ) .'
                    </div>
                ';
                break;
            case 'number':
                $badge = get_post_meta( $post->ID, 'wpcm_number', true );
                echo ( empty( $badge ) ? 'Uncapped' : $badge );
                break;
            case 'position':
                $positions = array();
                $terms     = get_the_terms( $post->ID, 'wpcm_position' );
                if ( ! empty( $terms ) && is_array( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $positions[] = $term->name;
                    }
                }
                $output = join( ', ', $positions );
                echo $output;
                break;
            case 'club':
                $club = get_post_meta( $post->ID, '_wpcm_player_club', true );
                echo get_the_title( $club );
                break;
            case 'flag':
                $nationality = get_post_meta( $post->ID, 'wpcm_natl', true );
                echo "<div class='flag-icon-background flag-icon-{$nationality}'></div>";
                break;
            case 'age':
                $dob = get_post_meta( $post->ID, 'wpcm_dob', true );
                echo get_age( $dob );
                break;
            case 'debut':
                echo get_the_date( 'F j, Y', $post->ID );
                break;
            case 'last':
                $last = get_post_meta( $post->ID, '_usar_last', true );
                echo date( 'F j, Y', strtotime( $last ) );
                break;
        }
    }

    /**
     * Make player admin columns sortable.
     *
     * @since USARDB 1.0.0
     *
     * @link {@see "manage_edit-{$post_type}_sortable_columns"}
     *
     * @param array $columns Array of columns.
     */
    public function wpcm_player_sortable_columns( $columns ) {
        $columns['number'] = 'wpcm_number';
        $columns['debut']  = 'date';
        $columns['last']   = '_usar_last';
        return $columns;
    }

    /**
     * Ouput custom columns for rosters.
     *
     * @since USARDB 1.0.0
     *
     * @param string $column The name of the column.
     */
    public function render_roster_columns( $column ) {

        global $post;

        switch ( $column ) {
            case 'season':
                $seasons = get_the_terms( $post->ID, 'wpcm_season' );
                if ( is_array( $seasons ) ) {
                    $season = $seasons[0]->name;
                } else {
                    $season = null;
                }
                echo $season;
                break;
            case 'team':
                $teams = get_the_terms( $post->ID, 'wpcm_team' );
                if ( is_array( $teams ) ) {
                    $team = $teams[0]->name;
                } else {
                    $team = null;
                }
                echo $team;
                break;
            case 'players':
                $players = unserialize( get_post_meta( $post->ID, '_wpcm_roster_players', true ) );
                echo count( $players );
                break;
            case 'staff':
                if ( ! empty( get_post_meta( $post->ID, '_wpcm_roster_staff', true ) ) ) {
                    $staff = unserialize( get_post_meta( $post->ID, '_wpcm_roster_staff', true ) );
                    echo count( $staff );
                }
                break;
        }
    }

    /**
     * Custom quick edit - form.
     *
     * @param mixed $column_name
     * @param mixed $post_type
     */
    public function quick_edit( $column_name, $post_type ) {
        if ( 'name' !== $column_name ) {
            return;
        }

        if ( 'wpcm_match' === $post_type )
        {
            $teams = get_terms( 'wpcm_team', array(
                'hide_empty' => false,
            ) );

            $seasons = get_terms( 'wpcm_season', array(
                'hide_empty' => false,
            ) );

            $comps = get_terms( 'wpcm_comp', array(
                'hide_empty' => false,
            ) );

            $venues = get_terms( 'wpcm_venue', array(
                'hide_empty' => false,
            ) );

            include( get_template_directory() . '/wpclubmanager/admin/views/html-quick-edit-match.php' );
        }
        elseif ( 'wpcm_club' === $post_type )
        {
            include( WPCM()->plugin_path() . '/includes/admin/views/html-quick-edit-club.php' );
        }
        elseif ( 'wpcm_player' === $post_type )
        {
            $positions = get_terms( 'wpcm_position', array(
                'hide_empty' => false,
            ) );

            $clubs = get_pages( array( 'post_type' => 'wpcm_club' ) );

            include( WPCM()->plugin_path() . '/includes/admin/views/html-quick-edit-player.php' );
        }
        elseif ( 'wpcm_staff' === $post_type )
        {
            $jobs = get_terms( 'wpcm_jobs', array(
                'hide_empty' => false,
            ) );

            $clubs = get_pages( array( 'post_type' => 'wpcm_club' ) );

            include( WPCM()->plugin_path() . '/includes/admin/views/html-quick-edit-staff.php' );
        }
    }

}

endif;

return new USARDB_WPCM_Admin_Post_Types();
