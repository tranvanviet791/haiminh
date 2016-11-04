<?php
/**
 * YITH WooCommerce Recently Viewed Products
 */

if ( ! defined( 'YITH_WRVP' ) ) {
    exit; // Exit if accessed directly
}

global $woocommerce_loop;

if( $columns )
    $woocommerce_loop['columns'] = $columns;
else
    $columns = 4;

// set slider
$slider = ( $slider == 'yes' && $products->post_count > $columns );
?>

<div class="woocommerce yith-similar-products <?php echo $class ?>" data-slider="<?php echo $slider ? '1' : '0' ?>"
     data-autoplay="<?php echo $autoplay == 'yes' ? '1' : '0' ?>" data-columns="<?php echo $columns ?>">

    <?php if ( ! empty( $title ) && shortcode_exists( 'box_title' ) ) {
        $subtitle = '<a href="' . $page_url . '" class="shop-link">' . $view_all . '</a>';
        echo do_shortcode("[box_title class='yith-similar-products-title' font_size='18' border_color='#f2f2f2' font_alignment='center' border='middle' subtitle='" . $subtitle . "']" . $title . "[/box_title]");
    }
    else { ?>
        <h2>
            <?php echo $title ?>
        <a href="<?php echo $page_url ?>" class="shop-link"><?php echo $view_all ?></a>
        </h2>
    <?php }

     woocommerce_product_loop_start(); ?>

    <?php while ($products->have_posts()) : $products->the_post(); ?>

        <?php wc_get_template_part('content', 'product'); ?>

    <?php endwhile; // end of the loop. ?>

    <?php woocommerce_product_loop_end(); ?>

</div>