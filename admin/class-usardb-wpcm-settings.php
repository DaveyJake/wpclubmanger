<?php
/**
 * USA Rugby Database API: WP Club Manager Settings
 *
 * This file uses WP Club Manager's own filters to override its default settings.
 *
 * @package USA_Rugby_Database
 * @subpackage WP_Club_Manager_Filters
 * @since USARDB 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if directly accessed.

class USARDB_WPCM_Settings {

    /**
     * USA Rugby Database Team IDs.
     *
     * @var array
     */
    protected $usardb_teams = array( 'mens-eagles', 'womens-eagles', 'mens-sevens', 'womens-sevens' );

    /**
     * Primary constructor.
     *
     * @return USARDB_WPCM_Filters
     */
    public function __construct() {
        add_filter( 'wpcm_sports', array( $this, 'rugby_only' ) );
        add_filter( 'wpclubmanager_countries', array( $this, 'us_only' ) );
        add_filter( 'wpclubmanager_player_header_labels', array( $this, 'wpcm_player_header_labels' ) );
        add_filter( 'wpclubmanager_player_labels', array( $this, 'wpcm_player_header_labels' ) );
        add_filter( 'wpclubmanager_players_settings', array( $this, 'wpcm_player_profile_settings' ) );
        add_filter( 'wpclubmanager_stats_cards', array( $this, 'rugby_cards_only' ) );

        /**
         * WPCM Thumbnail image adjustments.
         *
         * @uses {@see 'wpcm_image_thumbnail_adjust'}
         */
        foreach ( array( 'player_thumbnail', 'staff_thumbnail' ) as $image_size ) {
            add_filter( "wpclubmanager_get_image_size_{$image_size}", array( $this, 'wpcm_image_thumbnail_adjust' ) );
        }

        /**
         * WPCM `club` post type adjustments.
         */
        add_filter( 'wpclubmanager_register_post_type_club', array( $this, 'wpcm_club_post_type_adjustments' ) );

        /**
         * WPCM term adjustmnts.
         */
        add_filter( 'get_terms', array( $this, 'wpcm_term_corrections' ), 10, 4 );

        /**
         * Custom match metadata.
         */
        add_action( 'registered_taxonomy_for_object_type', array( $this, 'wpcm_match_metadata' ) );
    }

    /**
     * Rugby only.
     *
     * @link {@see 'wpcm_sports'}
     *
     * @return array Should only contain rugby.
     */
    public function rugby_only( $sports ) {
        foreach ( $sports as $slug => $data ) {
            if ( 'rugby' !== $slug ) {
                unset( $sports[ $slug ] );
            }
        }

        // American spelling of 'Centre'.
        foreach ( $sports['rugby']['terms']['wpcm_position'] as $i => $data ) {
            if ( 'Centre' === $data['name'] && 'centre' === $data['slug'] ) {
                $data['name'] = 'Center';
                $data['slug'] = 'center';
            }
        }

        // Five-Eighths
        $sports['rugby']['terms']['wpcm_position'][] = array(
            'name' => 'Five-Eighths',
            'slug' => 'five-eighths',
        );

        return $sports;
    }

    /**
     * Rugby cards only.
     *
     * @link {@see 'wpclubmanager_stats_cards'}
     *
     * @return array Should only contain 'yellowcards' and 'redcards'.
     */
    public function rugby_cards_only( $cards ) {
        $cards = array();
        $cards = array( 'yellowcards', 'redcards' );

        return $cards;
    }

    /**
     * Default location: United States.
     *
     * @link {@see 'wpclubmanager_countries'}
     *
     * @return array
     */
    public function us_only( $countries ) {
        global $hook_suffix;

        if ( 'club-manager_page_wpcm-settings' === $hook_suffix ) {
            foreach ( $countries as $slug => $name ) {
                if ( 'us' !== $slug ) {
                    unset( $countries[ $slug ] );
                }
            }
        }

        return $countries;
    }

    /**
     * WP Club Manager image size adjustments.
     *
     * @since USA_Rugby 2.5.0
     *
     * @link {@see "wpclubmanager_get_image_size_{$image_size}"}
     *
     * @param array $size Default image arguments.
     */
    public function wpcm_image_thumbnail_adjust( $size ) {
        $size['width']  = 639;
        $size['height'] = 639;
        $size['crop']   = array( 'center', 'top' );

        return $size;
    }

    /**
     * WP Club Manager `club` post type.
     *
     * @link {@see 'wpclubmanager_register_post_type_club'}
     *
     * @param array $args Default arguments.
     */
    public function wpcm_club_post_type_adjustments( $args ) {
        $args['labels'] = array(
            'name'               => __( 'Unions', 'wp-club-manager' ),
            'singular_name'      => __( 'Union', 'wp-club-manager' ),
            'add_new'            => __( 'Add New', 'wp-club-manager' ),
            'all_items'          => __( 'All Unions', 'wp-club-manager' ),
            'add_new_item'       => __( 'Add New Union', 'wp-club-manager' ),
            'edit_item'          => __( 'Edit Union', 'wp-club-manager' ),
            'new_item'           => __( 'New Union', 'wp-club-manager' ),
            'view_item'          => __( 'View Union', 'wp-club-manager' ),
            'search_items'       => __( 'Search Unions', 'wp-club-manager' ),
            'not_found'          => __( 'No unions found', 'wp-club-manager' ),
            'not_found_in_trash' => __( 'No unions found in trash'),
            'parent_item_colon'  => __( 'Parent Union:', 'wp-club-manager' ),
            'menu_name'          => __( 'Unions', 'wp-club-manager' )
        );

        return $args;
    }

    /**
     * Correct term names when they match term slugs.
     *
     * @link {@see 'get_terms'}
     *
     * @param array                $terms      The current terms to edit.
     * @param array                $taxonomies List of taxonomies.
     * @param array                $args       WP_Term arguments.
     * @param WP_Term_Query|object $term_query WP_Term_Query object.
     *
     * @return WP_Term Updated term.
     */
    public function wpcm_term_corrections( array $terms, array $taxonomies, array $args, WP_Term_Query $term_query ) {
        foreach ( $terms as $term ) {
            if ( isset( $term->name ) ) {
                $clean = array();

                if ( $term->name === $term->slug ) {
                    $parts = preg_split( '/-/', $term->name );

                    foreach ( $parts as $part ) {
                        if ( 'usa' === $part ) {
                            $part = strtoupper( $part );
                        } elseif ( 'canam' === $part ) {
                            $part = 'CanAm';
                        } elseif ( 'irb' === $part ) {
                            $part = 'IRB';
                        } elseif ( 'womens' === $part ) {
                            $part = 'Women\'s';
                        }

                        if ( ! in_array( $part, array( 'of' ) ) ) {
                            $part = ucwords( $part );
                        }

                        $clean[] = $part;
                    }
                }
                elseif ( preg_match( '/Of/', $term->name ) ) {
                    $parts = preg_split( '/\s/', $term->name );

                    foreach ( $parts as $part ) {
                        if ( 'Of' === $part ) {
                            $part = 'of';
                        }

                        if ( ! in_array( $part, array( 'of' ) ) ) {
                            $part = ucwords( $part );
                        }

                        $clean[] = $part;
                    }
                }

                $name = implode( ' ', $clean );

                if ( isset( $term->taxonomy ) ) {
                    if ( 'wpcm_comp' === $term->taxonomy ) {
                        wp_update_term( $term->term_id, 'wpcm_comp', array( 'name' => $name ) );
                    }
                    elseif ( 'wpcm_venue' === $term->taxonomy ) {
                        wp_update_term( $term->term_id, 'wpcm_venue', array( 'name' => $name ) );
                    }
                }
            }
        }

        return $terms;
    }

    /**
     * Additional post meta to include in every match.
     *
     * @link https://developer.wordpress.org/reference/functions/add_post_meta/
     */
    public function wpcm_match_metadata() {
        if ( false === get_term_by( 'slug', 'mens-eagles', 'wpcm_team' ) ) {
            return;
        }

        /*$mens_eagles   = get_term_by( 'slug', 'mens-eagles', 'wpcm_team' );
        $womens_eagles = get_term_by( 'slug', 'womens-eagles', 'wpcm_team' );
        $mens_sevens   = get_term_by( 'slug', 'mens-sevens', 'wpcm_team' );
        $womens_sevens = get_term_by( 'slug', 'womens-sevens', 'wpcm_team' );*/

        foreach ( $this->usardb_teams as $i => $slug ) {
            $team = get_term_by( 'slug', $slug, 'wpcm_team' );
            switch ( $i ) {
                case 0:
                    $me_id = (string) $team->term_id;
                    break;
                case 1:
                    $we_id = (string) $team->term_id;
                    break;
                case 2:
                    $ms_id = (string) $team->term_id;
                    break;
                case 3:
                    $ws_id = (string) $team->term_id;
                    break;
            }
        }

        $usardb_world = array(
            $me_id => MENS_FIFTEENS,
            $we_id => WOMENS_FIFTEENS,
            $ms_id => MENS_SEVENS,
            $ws_id => WOMENS_SEVENS,
        );

        $args = array(
            'post_type'      => array( 'wpcm_match' ),
            'post_status'    => array( 'publish', 'future' ),
            'posts_per_page' => -1,
        );

        $matches = get_posts( $args );

        foreach ( $matches as $match ) {
            $team = wp_get_object_terms( $match->ID, 'wpcm_team', array( 'fields' => 'id=>slug' ) );
            $wr   = array_keys( $team );
            $wr   = (string) $wr[0];

            update_post_meta( $match->ID, 'wr_usa_team', $usardb_world[ $wr ] );
        }
    }

    /**
     * Adjust player header labels.
     *
     * @link {@see 'wpclubmanager_player_header_labels'}
     *
     * @param array $labels Associative array of slugs to labels.
     *
     * @return array Custom player header labels.
     */
    public function wpcm_player_header_labels( $labels ) {
        if ( isset( $labels['joined'] ) ) {
            $labels['joined'] = __( 'Debuted', 'wp-club-manager' );
        }

        return $labels;
    }

    /**
     * Adjust player header labels.
     *
     * @link {@see 'wpclubmanager_players_settings'}
     *
     * @param array $labels Associative array of slugs to labels.
     *
     * @return array Custom player header labels.
     */
    public function wpcm_player_profile_settings( $settings ) {
        $settings[9] = array(
            'desc'          => __( 'Debut Date', 'wp-club-manager' ),
            'id'            => 'wpcm_player_profile_show_joined',
            'default'       => 'no',
            'type'          => 'checkbox',
            'checkboxgroup' => '',
        );

        return $settings;
    }

}

return new USARDB_WPCM_Settings();