<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$has_wishlist = yit_get_option( 'shop-view-wishlist-button' ) == 'yes' && shortcode_exists( 'yith_wcwl_add_to_wishlist' ) && get_option( 'yith_wcwl_enabled' ) == 'yes';
$hide_button  = get_post_meta( $product->id, 'shop-single-add-to-cart', true ) == 'no' || yit_get_option( 'shop-add-to-cart-button' ) == 'no' || yit_get_option( 'shop-enable' ) == 'no';
$in_stock     = $product->is_in_stock();
$is_wishlist  = function_exists( 'yith_wcwl_is_wishlist' ) && yith_wcwl_is_wishlist();

?>
<div class="product-actions-wrapper <?php echo ( $has_wishlist ) ? 'with-wishlist' : '' ?> border">

    <div class="product-action-button">

        <?php
        if ( apply_filters( 'yith_loop_add_to_cart_hide_button', $hide_button ) ) : ?>

            <a href="<?php echo apply_filters( 'yith_loop_view_details_permalink', get_permalink( $product->id ), $product ); ?>" class="view-details">
                <?php
                if ( yit_get_option( 'shop-enable-button-icon' ) == 'yes' ) {
                    echo '<img class="icon-add-to-cart" src="' . yit_get_option( 'shop-set-options-icon' ) . '"/>';
}
                echo '<span>' . apply_filters( 'yit_view_details_product_text', __( 'View Details', 'yit' ), $product ) . '</span>';
                ?>
            </a>

        <?php
        elseif ( ! $in_stock ) : ?>

            <span class="out-of-stock">
                <?php echo apply_filters( 'yit_out_of_stock_product_text', __( 'Out of stock', 'yit' ) ); ?>
            </span>

        <?php
        else :

            $link = array(
                'url'      => $product->add_to_cart_url(),
                'label'    => $product->add_to_cart_text(),
            );


            $handler = apply_filters( 'woocommerce_add_to_cart_handler', $product->product_type, $product );

            $icon_label_button = '';

            if( yit_get_option( 'shop-enable-button-icon' ) == 'yes' && ! $is_wishlist ) {
                if ( $handler == 'simple' ) {
                    $icon_label_button = yit_image( "echo=no&src=". yit_get_option( 'shop-add-to-cart-icon' ) ."&getimagesize=1&class=icon-add-to-cart&alt=" . $link['label'] );
                }
                else{
                    $icon_label_button = yit_image( "echo=no&src=". yit_get_option( 'shop-set-options-icon' ) ."&getimagesize=1&class=icon-add-to-cart&alt=" . $link['label'] );
                }
            }

            switch ( $handler ) {
                case "variable" :
                    $link['url'] = apply_filters( 'variable_add_to_cart_url', $link['url'] );
                    $link['label'] = '<span>' . apply_filters( 'variable_add_to_cart_text', $link['label'] ) . '</span>';
                    break;

                case "grouped" :
                    $link['url'] = apply_filters( 'grouped_add_to_cart_url', $link['url'] );
                    $link['label'] = '<span>' . apply_filters( 'grouped_add_to_cart_text', $link['label'] ) . '</span>';
                    break;

                case "external" :
                    $link['url'] = apply_filters( 'external_add_to_cart_url', $link['url'] );
                    $link['label'] = '<span>' . apply_filters( 'external_add_to_cart_text', $link['label'] ) . '</span>';
                    break;

                case "gift-card" :

                    $link['url'] = apply_filters( 'variable_add_to_cart_url', $link['url'] );
                    $link['label'] = '<span>' . apply_filters( 'yith_woocommerce_gift_cards_add_to_cart_text', $link['label'] ) . '</span>';
                    break;

                default :

                    if ( $product->is_purchasable() ) {
                        $link['url'] = apply_filters( 'add_to_cart_url', $link['url'] );
                        $link['label'] = '<span>' . apply_filters( 'add_to_cart_text', $link['label'] ) . '</span>';

                        $quantity = apply_filters( 'add_to_cart_quantity', isset( $quantity ) ? $quantity : 1 );
                    }
                    else {
                        $link['url'] = apply_filters( 'not_purchasable_url', $link['url'] );
                        $link['label'] = '<span>' . apply_filters( 'not_purchasable_text', $link['label'] ) . '</span>';
                    }
                    break;
            }

            echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
                    esc_url( $link['url'] ),
                    esc_attr( isset( $quantity ) ? $quantity : 1 ),
                    esc_attr( $product->id ),
                    esc_attr( $product->get_sku() ),
                    esc_attr( isset( $class ) ? $class : 'button' ),
                    $icon_label_button.$link['label']  ),
                $product );

        endif; ?>

    </div>

    <?php

    if ( yit_get_option( 'shop-view-wishlist-button' ) == 'yes' && shortcode_exists( 'yith_wcwl_add_to_wishlist' ) && get_option( 'yith_wcwl_enabled' ) == 'yes' && ! $is_wishlist ) {
        echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
    }

    ?>
</div>