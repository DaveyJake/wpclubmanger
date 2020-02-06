/*global ajaxurl, inlineEditPost, inlineEditL10n, wpclubmanager_admin */
( function( $ ) {
    var pagenow = window.pagenow,
        typenow = window.typenow;

    // WP Club Manager - Matches
    if ( 'edit-wpcm_match' === pagenow )
    {
        $( '#the-list' ).on( 'click', '.editinline', function() {
            inlineEditPost.revert();

            var post_id           = $( this ).closest( 'tr' ).attr( 'id' ).replace( 'post-', '' ),
                $wpcm_inline_data = $( '#wpclubmanager_inline_' + post_id ),
                neutral           = $wpcm_inline_data.find( '.neutral' ).text(),
                friendly          = $wpcm_inline_data.find( '.friendly' ).text();

            // Neutral venue.
            if ( '1' === neutral )
            {
                $( 'input[name="wpcm_neutral"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
            }
            else
            {
                $( 'input[name="wpcm_neutral"]', '.inline-edit-row' ).removeAttr( 'checked' );
            }

            // Non-Test match.
            if ( 'yes' === friendly )
            {
                $( 'input[name="wpcm_friendly"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
            }
            else
            {
                $( 'input[name="wpcm_friendly"]', '.inline-edit-row' ).removeAttr( 'checked' );
            }
        });

        // Non-Test match tooltip.
        $( '.red' ).each( function() {
            $( this ).tooltip({
                position: {
                    my: 'left top',
                    at: 'left+250 top-30',
                    of: '#' + this.parentNode.parentNode.id
                }
            });
        });
    }
}( jQuery ) );
