<?php
/**
 * Main class
 *
 * @author Your Inspiration Themes
 * @package YITH Pre-Launch
 * @version 1.0.2
 */

if ( !defined( 'YITH_PRELAUNCH' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'YITH_Prelaunch_Frontend' ) ) {
    /**
     * YITH Custom Login Frontend
     *
     * @since 1.0.0
     */
    class YITH_Prelaunch_Frontend {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version;

        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $template_file = 'prelaunch.php';

        /**
         * Constructor
         *
         * @param $version
         *
         * @return YITH_Prelaunch_Frontend
         * @since 1.0.0
         */
        public function __construct( $version ) {
            $this->version = $version;

            if ( ! yith_prelaunch_is_enabled() ) return $this;

            // start frontend
            add_action( 'template_redirect', array( $this, 'activate_prelaunch'), 99 );
            add_action( 'admin_bar_menu', array( &$this, 'admin_bar_menu' ), 1000 );
            add_action( 'wp_head', array( &$this, 'custom_style'));
            add_action( 'wp_footer', array( &$this, 'assets' ));
            add_action( 'admin_head', array( &$this, 'custom_style'));

            return $this;
        }

        /**
         * Admin bar menu item
         *
         */
        public function admin_bar_menu(){
            global $wp_admin_bar;

            /* Add the main siteadmin menu item */
            $wp_admin_bar->add_menu( array(
                'id'     => 'prelaunch-bar',
                'href'   => current_user_can( 'administrator' ) ? admin_url( 'themes.php?page=yith-prelaunch' ) : '#',
                'parent' => 'top-secondary',
                'title'  => apply_filters( 'yit_prelaunch_admin_bar_title', __('Pre-Launch Active', 'yith-pre-launch') ),
                'meta'   => array( 'class' => 'yit_prelaunch' ),
            ) );
        }

        /**
         * Custom css for admin bar menu item
         *
         */
        public function custom_style() {
            if ( !is_user_logged_in() ) return; ?>
            <style type="text/css">
                #wp-admin-bar-prelaunch-bar a.ab-item { background: rgb(197, 132, 8) !important; color: #fff !important }
            </style>
        <?php
        }

        /**
         * Enqueue the assets
         */
        public function assets() {
            do_action( 'yit_prelaunch_footer' );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-countdown', YITH_PRELAUNCH_URL . 'assets/js/jquery.countdown.js', array('jquery') );
        }

        /**
         * Render the prelaunch page
         *
         */
        public function activate_prelaunch() {
            if( $this->_userIsAllowed() || $this->_isLoginPage() ) return;

            wp_localize_script( 'jquery', 'yit', array(
                'isRtl' => is_rtl(),
                'ajaxurl' => admin_url( 'admin-ajax.php' )
            ));

            extract( $this->_vars() );

            $theme_path = defined( 'YIT' ) && defined( 'YIT_CORE_VERSION' ) && version_compare( YIT_CORE_VERSION, '2.0.0', '<=' ) ? YIT_THEME_TEMPLATES_PATH . '/prelaunch/' : get_template_directory();
            $child_path = defined( 'YIT' ) && defined( 'YIT_CORE_VERSION' ) && version_compare( YIT_CORE_VERSION, '2.0.0', '<=' ) ? str_replace( get_template_directory(), get_stylesheet_directory(), YIT_THEME_TEMPLATES_PATH ) . '/prelaunch/': get_stylesheet_directory();

            $plugin_path   = plugin_dir_path(__FILE__) . 'templates/' . $this->template_file;
            $template_path = $theme_path . $this->template_file;
            $child_path    = $child_path . $this->template_file;

            foreach ( array( 'child_path', 'template_path', 'plugin_path' ) as $var ) {
                if ( file_exists( ${$var} ) ) {
                    include ${$var};
                    exit();
                }
            }
        }

        /**
         * Return the url of stylesheet position
         *
         */
        public function stylesheet_url() {
            $filename = 'prelaunch.css';
            $plugin_path   = array( 'path' => plugin_dir_path(__FILE__) . 'assets/css/style.css', 'url' => YITH_PRELAUNCH_URL . 'assets/css/style.css' );
            $template_path = array( 'path' => get_template_directory() . '/' . $filename,         'url' => get_template_directory_uri() . '/' . $filename );
            $child_path    = array( 'path' => get_stylesheet_directory() . '/' . $filename,       'url' => get_stylesheet_directory_uri() . '/' . $filename );

            foreach ( array( 'child_path', 'template_path', 'plugin_path' ) as $var ) {
                if ( file_exists( ${$var}['path'] ) ) {
                    return ${$var}['url'];
                }
            }
        }


        /**
         * Is the user allowed to access to frontend?
         *
         * @return bool
         * @since 1.0.0
         * @access protected
         */
        protected function _userIsAllowed() {
            //super admin
            if( current_user_can('manage_network') || current_user_can('administrator') ) {
                return true;
            }

            $allowed = get_option('yith_prelaunch_roles');
            $user_roles = yit_user_roles();

            $is_allowed = false;

            foreach( $user_roles as $role ) {
                if( in_array( $role, $allowed ) ) {
                    $is_allowed = true;
                    break;
                }
            }

            return $is_allowed;
        }


        /**
         * Is it a login page?
         *
         * @return bool
         * @since 1.0.0
         * @access protected
         */
        protected function _isLoginPage() {
            $login_url = site_url('wp-login.php', 'login');
            $admin_index = admin_url( 'index.php' );
            $pages = array( str_replace( site_url(), '', $login_url ), str_replace( site_url(), '', $admin_index ) );
            $current_page = $_SERVER['PHP_SELF'];
            $found = false;

            foreach( $pages as $page ) {
                if( strpos( $current_page, $page ) !== false ) {
                    $found = true;
                    break;
                }
            }

            return $found;
        }


        /**
         * Generate template vars
         *
         * @return array
         * @since 1.0.0
         * @access protected
         */
        protected function _vars() {
            $countdown_date = yith_prelaunch_unixstamp( get_option('yith_prelaunch_to_date') );
            $vars = array(
                'background' => array(
                    'color'      => get_option('yith_prelaunch_background_color'),
                    'image'      => get_option('yith_prelaunch_background_image'),
                    'repeat'     => get_option('yith_prelaunch_background_repeat'),
                    'position'   => get_option('yith_prelaunch_background_position'),
                    'attachment' => get_option('yith_prelaunch_background_attachment')
                ),
                'color' => array(
                    'border_top' => get_option('yith_prelaunch_border_top'),
                ),
                'logo' => array(
                    'image' => get_option('yith_prelaunch_logo_image'),
                    'tagline' => get_option('yith_prelaunch_logo_tagline'),
                    'tagline_font' => yit_typo_option_to_css( get_option('yith_prelaunch_logo_tagline_font') ),
                ),
                'mascotte' => get_option('yith_prelaunch_mascotte'),
                'message' => get_option('yith_prelaunch_message'),
                'title_font' => yit_typo_option_to_css( get_option('yith_prelaunch_title_font') ),
                'p_font' => yit_typo_option_to_css( get_option('yith_prelaunch_paragraph_font') ),
                'newsletter' => array(
                    'enabled' => get_option('yith_prelaunch_enable_newsletter_form') == 1,
                    'submit' => array(
                        'color' => get_option('yith_prelaunch_newsletter_submit_background'),
                        'hover' => get_option('yith_prelaunch_newsletter_submit_background_hover'),
                        'label' => get_option('yith_prelaunch_newsletter_submit_label'),
                        'font'  => yit_typo_option_to_css( get_option('yith_prelaunch_newsletter_submit_font') ),
                    ),
                    'form_action' => get_option('yith_prelaunch_newsletter_action'),
                    'form_method' => get_option('yith_prelaunch_newsletter_method'),
                    'email_label' => get_option('yith_prelaunch_newsletter_email_label'),
                    'email_name'  => get_option('yith_prelaunch_newsletter_email_name'),
                    'email_font'  => yit_typo_option_to_css( get_option('yith_prelaunch_newsletter_email_font') ),
                    'hidden_fields' => array(),
                ),
                'custom' => get_option('yith_prelaunch_custom_style'),
                'title' => get_option('yith_prelaunch_newsletter_title'),
                'socials' => array(
                    'facebook'  => get_option('yith_prelaunch_socials_facebook'),
                    'twitter'   => get_option('yith_prelaunch_socials_twitter'),
                    'gplus'     => get_option('yith_prelaunch_socials_gplus'),
                    'youtube'   => get_option('yith_prelaunch_socials_youtube'),
                    'rss'       => get_option('yith_prelaunch_socials_rss'),
                    'behance'   => get_option('yith_prelaunch_socials_behance'),
                    'dribble'   => get_option('yith_prelaunch_socials_dribble'),
                    'email'     => get_option('yith_prelaunch_socials_email'),
                    'flickr'    => get_option('yith_prelaunch_socials_flickr'),
                    'instagram' => get_option('yith_prelaunch_socials_instagram'),
                    'linkedin'  => get_option('yith_prelaunch_socials_linkedin'),
                    'pinterest' => get_option('yith_prelaunch_socials_pinterest'),
                    'skype'     => get_option('yith_prelaunch_socials_skype'),
                    'tumblr'    => get_option('yith_prelaunch_socials_tumblr'),
                ),
                'countdown' => array(
                    'enabled' => get_option('yith_prelaunch_countdown_enable'),
                    'to' => $countdown_date - time(),
                    'days' => yith_countdown_days( $countdown_date ),
                    'hours' => yith_countdown_hours( $countdown_date ) - yith_countdown_days( $countdown_date ) * 24,
                    'minutes' => yith_countdown_minutes( $countdown_date ) - yith_countdown_hours( $countdown_date ) * 60,
                    'seconds' => yith_countdown_seconds( $countdown_date ) - yith_countdown_minutes( $countdown_date ) * 60,
                    'num_font' => yit_typo_option_to_css( get_option('yith_prelaunch_numbers_font') ),
                    'label_font' => yit_typo_option_to_css( get_option('yith_prelaunch_labels_font') ),
                )
            );

            $hidden = get_option('yith_prelaunch_newsletter_hidden_fields');
            $hidden_fields = explode( '&', $hidden );
            foreach ( $hidden_fields as $field ) {
                if( !empty( $field ) ) {
                    list( $key, $val ) = explode( '=', $field );
                    $vars['newsletter']['hidden_fields'][$key] = $val;
                }
            }

            return $vars;
        }

        public static function userIsAllowed() {
            global $yith_prelaunch;
            return $yith_prelaunch->obj->_userIsAllowed();
        }
    }
}