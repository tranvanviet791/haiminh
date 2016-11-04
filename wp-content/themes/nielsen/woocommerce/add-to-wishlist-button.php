<?php
/**
 * Add to wishlist button template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.0
 */

global $product;

?>

<a href="<?php echo esc_url( add_query_arg( 'add_to_wishlist', $product_id ) )?>" data-product-id="<?php echo $product_id ?>" data-product-type="<?php echo $product_type?>" class="<?php echo $link_classes ?> with-tooltip add_to_wishlist" data-toggle="tooltip" data-placement="bottom" title="<?php echo $label?>" >
	<span data-icon="&#xe3e9;" data-font="retinaicon-font"></span>
</a>