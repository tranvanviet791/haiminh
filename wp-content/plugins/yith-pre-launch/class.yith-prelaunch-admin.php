<?php
/**
 * Main admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH Pre-Launch
 * @version 1.0.2
 */

if ( ! defined( 'YITH_PRELAUNCH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Prelaunch_Admin' ) ) {
    /**
     * YITH Custom Login Admin
     *
     * @since 1.0.0
     */
    class YITH_Prelaunch_Admin {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version;

        /**
         * Parameters for add_submenu_page
         *
         * @var array
         * @access public
         * @since 1.0.0
         */
        public $submenu = array();

        /**
         * Initial Options definition:
         *
         * @var array
         * @access public
         * @since 1.0.0
         */
        public $options = array();

        /**
         * Panel instance
         *
         * @var YITH_Panel
         * @since 1.0.0
         */
        public $panel;

        /**
         * Various links
         *
         * @var string
         * @access public
         * @since 1.0.0
         */
        public $banner_url = 'http://cdn.yithemes.com/plugins/yith_prelaunch.php?url';
        public $banner_img = 'http://cdn.yithemes.com/plugins/yith_prelaunch.php';
        public $doc_url = 'http://yithemes.com/docs-plugins/yith_prelaunch/';

        /**
         * Constructor
         *
         * @return YITH_Prelaunch_Admin
         * @since 1.0.0
         */
        public function __construct( $version ) {
            global $yith_prelaunch_options;

            $this->version = $version;
            $this->submenu = apply_filters( 'yith_prelaunch_submenu', array(
                'themes.php',
                __( 'YITH Pre-Launch', 'yith-pre-launch' ),
                __( 'Pre-Launch', 'yith-pre-launch' ),
                'administrator',
                'yith-prelaunch'
            ) );
            $this->options = apply_filters( 'yith_prelaunch_options', $yith_prelaunch_options );

            add_action( 'init', array( $this, 'init_panel' ) );
            add_action( 'init', array( $this, 'default_options' ) );
            add_filter( 'plugin_action_links_' . plugin_basename( dirname( __FILE__ ) . '/init.php' ), array( $this, 'action_links' ) );

            return $this;
        }

        /**
         * Default options
         *
         * Sets up the default options used on the settings page
         *
         * @access public
         * @return void
         * @since 1.0.0
         */
        public function default_options() {
            foreach ( $this->options as $tab ) {
                foreach ( $tab['sections'] as $section ) {
                    foreach ( $section['fields'] as $id => $value ) {
                        if ( isset( $value['std'] ) && isset( $id ) ) {
                            add_option( $id, $value['std'] );
                        }
                    }
                }
            }
        }

        /**
         * Init the panel
         *
         * @return void
         * @since 1.0.0
         */
        public function init_panel() {
            $this->panel = new YITH_Panel(
                $this->submenu,
                $this->options,
                array(
                    'url' => $this->banner_url,
                    'img' => $this->banner_img
                ),
                'yith-prelaunch-group',
                'yith-prelaunch'
            );
        }

        /**
         * action_links function.
         *
         * @access public
         *
         * @param mixed $links
         *
         * @return void
         */
        public function action_links( $links ) {

            $plugin_links = array(
                '<a href="' . admin_url( $this->submenu[0] . '?page=' . $this->submenu[4] ) . '">' . __( 'Settings', 'yith-pre-launch' ) . '</a>',
                '<a href="' . $this->doc_url . '">' . __( 'Docs', 'yith-pre-launch' ) . '</a>',
            );

            return array_merge( $plugin_links, $links );
        }
    }
}