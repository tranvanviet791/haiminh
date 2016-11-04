<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Notifications Area Constants definition
 *
 * @package Yithemes
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 * @since 1.0.0
 *
 */


/**
 * The name of the YIT theme
 */
define( 'YIT_THEME_NAME', 'nielsen' );

/**
 * URL to check a theme update
 */
define( 'YIT_THEME_NOTIFIER_URL', 'http://update.yithemes.com/' . YIT_THEME_NAME . '.xml');

/**
 * Define the marketplace: ThemeForest (tf) Yithemes (yit) or free
 *
 */
define( 'YIT_MARKETPLACE', 'tf' );

/**
 * Link to the theme documentation
 */
define( 'YIT_DOCUMENTATION_URL', 'http://docs.yithemes.com/' . YIT_THEME_NAME . '/');

/**
 * Define if the theme is a shop, to add supporto to woocommerce plugin
 */
define( 'YIT_IS_SHOP', true );

/**
 * Define if the theme support the skins system
 */
define( 'YIT_HAS_SKINS', false );

/**
 * Link to the support platform
 */
define( 'YIT_SUPPORT_URL', 'http://support.yithemes.com/');

if( ! defined( 'YIT_DEBUG' ) ) {
    /**
     * Define if Debug Mode is enabled (Default: disabled)
     */
    define( 'YIT_DEBUG', false );
}


/**
 * The options below allows you to remove all Yithemes brand details in Theme Options.
 * It's highly recommended to define those constants within your wp-config.php
 * in order to preserve the settings even after theme update.
 */
if( !defined( 'YIT_SHOW_PANEL_HEADER' ) ) {
    define( 'YIT_SHOW_PANEL_HEADER', 1 );
}

if( !defined( 'YIT_SHOW_PANEL_HEADER_LINKS' ) ) {
    define( 'YIT_SHOW_PANEL_HEADER_LINKS', 1 );
}

/**
 * If true show notification icon in admin area when an update are available
 */
if( !defined( 'YIT_SHOW_UPDATES' ) ) {
    define( 'YIT_SHOW_UPDATES', true );
}

/**
 * Default Dummy Data Link
 */
if( ! defined( 'YIT_DEFAULT_DUMMY_DATA' ) ) {
    define( 'YIT_DEFAULT_DUMMY_DATA', 'https://www.dropbox.com/s/s6ld8e6g1qxzkam/nielsen_export.gz?dl=1' );
}

/**
 * Default Dummy Data Images Link
 */
if( ! defined( 'YIT_DEFAULT_DUMMY_DATA_IMAGES' ) ) {
    define( 'YIT_DEFAULT_DUMMY_DATA_IMAGES', 'https://www.dropbox.com/s/7rpezgq04knsg8z/nielsen.zip?dl=1' );
}

/**
 * Add recommended jetpack modules
 */
function yit_recommended_jetpack_modules( $modules ){

    $modules= array();

    $modules[]= 'yith-woocommerce-colors-labels-variations' ;
    $modules[]= 'yith-woocommerce-zoom-magnifier' ;
    $modules[]= 'yith-woocommerce-wishlist' ;
    $modules[]= 'yith-woocommerce-ajax-search' ;
    $modules[]= 'yith-woocommerce-compare' ;
    $modules[]= 'yith-woocommerce-ajax-navigation' ;
    $modules[]= 'yith-woocommerce-product-vendors' ;

    return $modules;
}
add_filter( 'yith_jetpack_recommended_list', 'yit_recommended_jetpack_modules' );

/**
 * Remove unused framework modules
 */
function yit_unset_theme_modules( $modules ){
    unset( $modules['feature-tabs'] );
    unset( $modules['services'] );
    return $modules;
}
add_filter( 'yit_framework_modules', 'yit_unset_theme_modules' );

/**
 * Remove jetpack modules
 */
function yit_remove_jetpack_modules1( $modules ){

    unset( $modules['yith-woocommerce-quick-view'] );

    return $modules;

}
add_filter( 'yith_jetpack_modules1' , 'yit_remove_jetpack_modules1' );

add_filter( 'redirect_canonical' , 'yit_redirect_canonical' , 10 ,2) ;
if( ! function_exists('yit_redirect_canonical') ) {
    function yit_redirect_canonical( $redirect_url, $requested_url ) {
        if( is_front_page() && ( is_page_template( 'blog.php' ) || is_page_template( 'home.php' ) ) ) {
            return false;
        } else {
            return $redirect_url;
        }
    }
}
