<?php
/**
 * Main class
 *
 * @author Your Inspiration Themes
 * @package YITH Pre-Launch
 * @version 1.0.2
 */

if ( !defined( 'YITH_PRELAUNCH' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'YITH_Prelaunch' ) ) {
    /**
     * YITH Pre-Launch
     *
     * @since 1.0.0
     */
    class YITH_Prelaunch {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_PRELAUNCH_VERSION;

        /**
         * Plugin object
         *
         * @var string
         * @since 1.0.0
         */
        public $obj = null;

        /**
         * Constructor
         *
         * @return mixed|YITH_Prelaunch_Admin|YITH_Prelaunch_Frontend
         * @since 1.0.0
         */
        public function __construct() {
            if( is_admin() ) {
                $this->obj = new YITH_Prelaunch_Admin( $this->version );
            } else {
                $this->obj = new YITH_Prelaunch_Frontend( $this->version );
            }

            return $this->obj;
        }
    }
}