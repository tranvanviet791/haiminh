<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

$woocommerce_loop['shown_product'] = true;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();

// view
if ( ! isset( $woocommerce_loop['view'] ) ) {
    $woocommerce_loop['view'] = yit_get_option( 'shop-view-type', 'grid' );
}

//product countdown compatibility

global $ywpc_loop;
if ( $ywpc_loop && $ywpc_loop == 'ywpc_widget' ) {
    $woocommerce_loop['view'] = 'grid';
}
//--------------------------

$classes[] = $woocommerce_loop['view'];

// check if is mobile
$isMobile = YIT_Mobile()->isMobile();
$isPhone = $isMobile && ! YIT_Mobile()->isTablet();
$isIPad = wp_is_mobile() && preg_match( '/iPad/', $_SERVER['HTTP_USER_AGENT'] );

// Set column
if ( ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) && ! $isMobile && yit_get_option( 'shop-custom-num-column' ) == 'yes' ) {
    $column_value = apply_filters( 'loop_shop_columns', intval( yit_get_option( 'shop-num-column' ) ) );
    $classes[] = 'col-sm-' . intval( 12 / $column_value );
    $woocommerce_loop['columns']    =  $column_value;
}
elseif ( isset( $product_in_a_row ) ){
    $classes[] = 'col-sm-' . intval( 12 / intval( $product_in_a_row ) ) . ' col-xs-4';
    $woocommerce_loop['columns']    = intval( $product_in_a_row );
}
elseif( isset( $featured_widget ) ) {
    $woocommerce_loop['columns'] = '1';
}
else {

    $sidebar = yit_get_sidebars();

    if ( $sidebar['layout'] == 'sidebar-double' ) {
        $classes[] = 'col-sm-4 col-xs-4';
        $woocommerce_loop['columns']    = '3';
    }
    elseif ( $sidebar['layout'] == 'sidebar-right' || $sidebar['layout'] == 'sidebar-left' ) {
        $classes[] = 'col-sm-3 col-xs-4';
        $woocommerce_loop['columns']    = '4';
    }
    else {

        if( yit_get_option( 'shop-custom-num-column' ) == 'yes' ) {

            $column_value = apply_filters( 'loop_shop_columns', intval( yit_get_option( 'shop-num-column' ) ) );
            $classes[] = 'col-sm-' . intval( 12 / $column_value );
            $woocommerce_loop['columns']    =  $column_value;

        } else {

            $classes[] = 'col-sm-2 col-xs-4';
            $woocommerce_loop['columns']    = '6';

        }

    }
}

if ( 0 === ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 === $woocommerce_loop['columns'] ) {
    $classes[] = 'first';
}
if ( 0 === $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
    $classes[] = 'last';
}


//Set columns and class mobile phone
$row_mobile_value = yit_get_option( 'shop-products-per-row-mobile' );
$row_mobile = intval( ! empty( $row_mobile_value ) ? $row_mobile_value : 2 );


if( $isPhone && ! isset( $featured_widget ) ) {
    $classes[]   = 'col-xxs-' . intval( 12 / $row_mobile );
    $woocommerce_loop['columns']      = $row_mobile;
}
?>
<li <?php post_class( $classes ); ?> >


    <div class="clearfix product-wrapper border">

        <?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

        <div class="thumb-wrapper">

            <?php
            /**
             * woocommerce_before_shop_loop_item_title hook
             *
             * @hooked woocommerce_show_product_loop_sale_flash - 10
             * @hooked woocommerce_template_loop_product_thumbnail - 10
             */
            do_action( 'woocommerce_before_shop_loop_item_title' );
            ?>

        </div>

        <div class="product-meta-wrapper border">

                 <?php

                /**
                 * woocommerce_shop_loop_item_title hook
                 *
                 * @hooked woocommerce_template_loop_product_title - 10
                 */
                do_action( 'woocommerce_shop_loop_item_title' );

                /**
                 * woocommerce_after_shop_loop_item_title hook
                 *
                 * @hooked woocommerce_template_loop_rating - 5
                 * @hooked woocommerce_template_loop_price - 10
                 */
                do_action( 'woocommerce_after_shop_loop_item_title' ); ?>

        </div>
        <div class="product_actions_container">
            <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
        </div>

    </div>

</li>
