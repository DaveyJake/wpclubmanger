<?php
/**
 * WPCM-Specific functions.
 *
 * @author Davey Jacobson <djacobson@usa.rugby>
 * @since USARDB 1.0.0
 */

/**
 * Decode address for Google Maps
 *
 * @param string $address Formatted address with no line-breaks.
 *
 * @return mixed          Associative array with `lat`, `lng` and `place_id` keys.
 */
function usardb_wpcm_decode_address( $address ) {

    $address_hash = md5( $address );
    $coordinates  = get_transient( $address_hash );
    $api_key      = get_option( 'wpcm_google_map_api');

    if ( false === $coordinates )
    {
        $args = array(
            'address' => urlencode( $address ),
            'key'     => urlencode( $api_key )
        );
        $url      = add_query_arg( $args, 'https://maps.googleapis.com/maps/api/geocode/json' );
        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            return $response->get_error_message();
        }

        if ( 200 === $response['response']['code'] )
        {
            $data = wp_remote_retrieve_body( $response );

            if ( is_wp_error( $data ) ) {
                return $data->get_error_message();
            }

            $data = json_decode( $data );

            if ( 'OK' === $data->status )
            {
                $coordinates = $data->results[0]->geometry->location;
                $place_id    = $data->results[0]->place_id;

                $cache_value['lat']      = $coordinates->lat;
                $cache_value['lng']      = $coordinates->lng;
                $cache_value['place_id'] = $place_id;

                // cache coordinates for 1 month
                set_transient( $address_hash, $cache_value, 3600*24*30 );
                $coordinates = $cache_value;

            }
            elseif ( 'ZERO_RESULTS' === $data->status )
            {
                return __( 'No location found for the entered address.', 'wp-club-manager' );
            }
            elseif ( 'INVALID_REQUEST' === $data->status )
            {
                return __( 'Invalid request. Address is missing', 'wp-club-manager' );
            }
            else
            {
                return __( 'Something went wrong while retrieving your map.', 'wp-club-manager' );
            }
        }
        else
        {
            return __( 'Unable to contact Google API service.', 'wp-club-manager' );
        }
    }

    return $coordinates;
}

/**
 * Get match competition.
 *
 * @param int $post_id The current post ID value.
 *
 * @return array
 */
function usardb_wpcm_get_match_comp( $post_id ) {

    $competitions = get_the_terms( $post_id, 'wpcm_comp' );
    $status       = get_post_meta( $post_id, 'wpcm_comp_status', true );

    if ( is_array( $competitions ) )
    {
        foreach ( $competitions as $competition ) {
            $comp        = $competition->name;
            $competition = reset( $competitions );
            $t_id        = $competition->term_id;
            $comp_meta   = get_term_meta( $t_id );
            $comp_label  = isset( $comp_meta['wpcm_comp_label'] ) ? $comp_meta['wpcm_comp_label'][0] : '';

            if ( ! empty( $comp_label ) ) {
                $label = $comp_label;
            } else {
                $label = $comp;
            }
        }
    }

    return array( $comp, $label, $status );
}

/**
 * Get match venue.
 *
 * @since WP_Club_Manager 1.4.6
 *
 * @param  WP_Post|int $post The current post object.
 *
 * @return string $venue
 */
function usardb_wpcm_get_match_venue( $post ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return '';
    }

    $club      = get_default_club();
    $venues    = get_the_terms( $post->ID, 'wpcm_venue' );
    $neutral   = get_post_meta( $post->ID, 'wpcm_neutral', true );
    $home_club = get_post_meta( $post->ID, 'wpcm_home_club', true );

    if ( is_array( $venues ) ) {
        $venue = reset( $venues );
        $venue_info['name']        = $venue->name;
        $venue_info['id']          = $venue->term_id;
        $venue_info['slug']        = $venue->slug;
        $venue_info['description'] = $venue->description;
        $venue_meta                = get_term_meta( $venue_info['id'] );
        $venue_info['address']     = $venue_meta['wpcm_address'][0];
        $venue_info['capacity']    = $venue_meta['wpcm_capacity'][0];
    } else {
        $venue_info['name']        = null;
        $venue_info['id']          = null;
        $venue_info['slug']        = null;
        $venue_info['description'] = null;
        $venue_info['address']     = null;
        $venue_info['capacity']    = null;
    }

    if ( $neutral )
    {
        $venue_info['status'] = _x( 'N', 'Neutral ground', 'wp-club-manager' );
    }
    else
    {
        if ( $club === $home_club )
        {
            $venue_info['status'] = _x( 'H', 'Home ground', 'wp-club-manager' );
        }
        else
        {
            $venue_info['status'] = _x( 'A', 'Away ground', 'wp-club-manager' );
        }
    }

    return $venue_info;
}
