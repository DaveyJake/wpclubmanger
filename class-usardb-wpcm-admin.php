<?php
/**
 * Initialize WP Club Manager admin overrides.
 *
 * @since USARDB 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if directly accessed

class USARDB_WPCM_Admin {

    /**
     * Primary constructor.
     *
     * @return USARDB_WPCM_Admin
     */
    public function __construct() {
        $this->includes();
    }

    /**
     * Files to include.
     *
     * @since USARDB 1.0.0
     */
    private function includes() {
        require 'usardb-wpcm-functions.php';
        require 'admin/class-usardb-wpcm-settings.php';

        require 'admin/class-usardb-wpcm-admin-columns.php';
        require 'admin/class-usardb-wpcm-admin-assets.php';
        require 'admin/class-usardb-wpcm-admin-post-types.php';

        require 'admin/post-types/meta-boxes/class-usardb-wpcm-meta-box-match-details.php';
        require 'admin/post-types/meta-boxes/class-usardb-wpcm-meta-box-match-details-custom.php';
        require 'admin/post-types/meta-boxes/class-usardb-wpcm-meta-box-match-result.php';
        require 'admin/post-types/meta-boxes/class-usardb-wpcm-meta-box-player-details.php';
        require 'admin/post-types/meta-boxes/class-usardb-wpcm-meta-box-match-player-enhancements.php';
        require 'admin/class-usardb-wpcm-admin-meta-boxes.php';

        require 'admin/class-usardb-wpcm-comps.php';
        require 'admin/class-usardb-wpcm-positions.php';
        require 'admin/class-usardb-wpcm-seasons.php';
        require 'admin/class-usardb-wpcm-teams.php';
        require 'admin/class-usardb-wpcm-venues.php';
    }

}

return new USARDB_WPCM_Admin();