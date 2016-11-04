<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(

    array(
        'name'         => 'YITH Prelaunch',
        'slug' 		   => 'yith-pre-launch',
        'required' 	   => false,
        'version'      => '1.0.7',
    ),

    array(
        'name'               => 'Revolution Slider',
        'slug'               => 'revslider',
        'source'             => YIT_THEME_PLUGINS_PATH . '/revslider.zip',
        'required'           => false,
        'version'            => '4.6.5',
        'force_activation'   => false,
        'force_deactivation' => true,
    ),

	array(
		'name'               => 'WPBakery Visual Composer',
		'slug'               => 'js_composer',
		'source'             => YIT_THEME_PLUGINS_PATH . '/js_composer.zip',
		'required'           => true,
		'version'            => '4.4',
		'force_activation'   => false,
		'force_deactivation' => true,
	),

    array(
        'name'               => 'Essential_Grid',
        'slug'               => 'essential-grid',
        'source'             => YIT_THEME_PLUGINS_PATH . '/essential-grid.zip',
        'required'           => false,
        'version'            => '2.0.4',
        'force_activation'   => false,
        'force_deactivation' => true,
    ),

    array(
        'name'         => 'WooCommerce',
        'slug' 		   => 'woocommerce',
        'required' 	   => false,
        'version'      => '2.2.10',
    ),

    defined( 'YITH_YWSL_PREMIUM' ) ? array() : array(
        'name'         => 'YITH WooCommerce Social Login',
        'slug'         => 'yith-woocommerce-social-login',
        'required'     => false,
        'version'      => '1.0.0',
    ),

    defined( 'YITH_YWPI_PREMIUM' ) ? array() : array(
        'name'      => 'YITH WooCommerce PDF Invoice and Shipping List',
        'slug'      => 'yith-woocommerce-pdf-invoice',
        'required'  => false,
        'version'   => '1.0.0'
    ),

    array(
        'name'         => 'YITH Essential Kit for WooCommerce #1',
        'slug' 		   => 'yith-essential-kit-for-woocommerce-1',
        'required' 	   => false,
        'version'      => '1.0.9',
    ),

);