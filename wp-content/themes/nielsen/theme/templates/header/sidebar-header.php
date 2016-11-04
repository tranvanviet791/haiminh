<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists('WC') || 'no' == yit_get_option('shop-mini-cart-show-in-header') || apply_filters('yit_show_header_sidebar', false )) {
	return;
}
?>

<!-- START HEADER SIDEBAR -->
<div id="header-sidebar" class="nav">
	<?php do_action('yit_header_sidebar_before_mini_cart') ?>
	<?php the_widget( 'YIT_Widget_Cart' ); ?>
	<?php do_action('yit_header_sidebar_after_mini_cart') ?>
</div>
<!-- END HEADER SIDEBAR -->
