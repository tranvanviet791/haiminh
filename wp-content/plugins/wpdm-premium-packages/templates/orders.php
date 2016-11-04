<?php
    global $wpdb;
    $osi = array('Pending'=>'ellipsis-h','Processing'=>'paw','Completed'=>'check','Cancelled'=>'times','Refunded'=>'retweet','Expired' => 'warning','Gifted' => 'gift','Disputed'=>'gavel');
?>
<style>
    .w3eden span.fa-stack,
    .admin-orders span.fa-stack{
        font-size: 0.85em;
        display: inline-block;
        height: 1.7em;
        line-height: 1.7em;
        position: relative;
        vertical-align: middle;
        width: 1.7em;
        box-sizing: content-box !important;
    }

    .admin-orders .fa-stack .fa-stack-2x{
        display: none;
    }
    .admin-orders .fa-stack .fa-stack-1x{
        border-radius: 500px;
        border: 1px solid #444444;
        font-size: 90%;
        box-sizing: content-box !important;
    }

    .admin-orders table .oa-Processing .fa-stack-1x,
    .admin-orders table .oa-Processing{
        color: #3498DB;
        border-color: #3498DB;
    }
    .fa-stack.oa-Refunded,
    .fa-stack.oa-Refunded .fa-stack-1x,
    .download-off .fa-stack-1x,
    .download-off{
        color: #cccccc;
        border-color: #cccccc !important;
    }

    .download-on,
    .download-on .fa-stack-1x,
    .admin-orders table .oa-Completed .fa-stack-1x,
    .admin-orders table .oa-Completed{
        color: #5CA572;
        border-color: #5CA572 !important;
    }
    .admin-orders table .oa-Cancelled .fa-stack-1x,
    .admin-orders table .oa-Cancelled{
        color: #C64F4D;
        border-color: #C64F4D;
    }
    .admin-orders table .oa-Disputed .fa-stack-1x,
    .admin-orders table .oa-Disputed{
        color: #F75D74;
        border-color: #F75D74;
    }
    .admin-orders table .oa-Expired .fa-stack-1x,
    .admin-orders table .oa-Expired{
        color: #F2983E;
        border-color: #F2983E;
    }
    .admin-orders table .oa-Gifted .fa-stack-1x,
    .admin-orders table .oa-Gifted{
        color: #CD6DED;
        border-color: #CD6DED;
    }
    .text-filter{
        margin-left: 10px;
        color: #B56CA4;
    }
    div.tooltip,
    div.tooltip-inner{
        border-radius: 2px !important;
        padding: 5px 10px !important;
    }
    .note { color: #888; }
</style>
<div class="wrap admin-orders">
    <h2><?php echo __("Orders","wpdm-premium-package");?></h2>
    <?php
    if(isset($msg)):
        if(is_array($msg)):
            foreach($msg as $a => $b):
                echo "<h3>$b</h3>";
            endforeach;
        else:
            echo "<h3>$msg</h3>";
        endif;
    endif;
    ?>
        
<form method="get" action="" id="posts-filter">
    <input type="hidden" name="post_type" value="wpdmpro">
    <input type="hidden" name="page" value="orders">
<div class="tablenav">
    <div class="alignleft actions">
   
    <select class="select-action" name="ost">
        <option value="">Order status:</option>
        <option value="Pending" <?php if(isset($_REQUEST['ost'])) echo $_REQUEST['ost']=='Pending'?'selected=selected':''; ?>>Pending</option>
        <option value="Processing" <?php if(isset($_REQUEST['ost'])) echo $_REQUEST['ost']=='Processing'?'selected=selected':''; ?>>Processing</option>
        <option value="Completed" <?php if(isset($_REQUEST['ost'])) echo $_REQUEST['ost']=='Completed'?'selected=selected':''; ?>>Completed</option>
        <option value="Cancelled" <?php if(isset($_REQUEST['ost'])) echo $_REQUEST['ost']=='Cancelled'?'selected=selected':''; ?>>Cancelled</option>
    </select>
    <select class="select-action" name="pst">
        <option value="">Payment status:</option>
        <option value="Pending" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Pending'?'selected=selected':''; ?>>Pending</option>
        <option value="Processing" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Processing'?'selected=selected':''; ?>>Processing</option>
        <option value="Completed" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Completed'?'selected=selected':''; ?>>Completed</option>
        <option value="Bonus" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Bonus'?'selected=selected':''; ?>>Bonus</option>
        <option value="Gifted" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Gifted'?'selected=selected':''; ?>>Gifted</option>
        <option value="Cancelled" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Cancelled'?'selected=selected':''; ?>>Cancelled</option>
        <option value="Disputed" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Disputed'?'selected=selected':''; ?>>Disputed</option>
        <option value="Refunded" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Refunded'?'selected=selected':''; ?>>Refunded</option>
    </select>

<?php echo __("Date","wpdm-premium-package");?><span class="ttip" title="(yyyy-mm-dd)">(?)</span> :
<?php echo __("from","wpdm-premium-package");?> <input size="10" type="text" name="sdate" value="<?php if(isset($_REQUEST['sdate'])) echo $_REQUEST['sdate']; ?>">
<?php echo __("to","wpdm-premium-package");?> <input size="10" type="text" name="edate" value="<?php if(isset($_REQUEST['edate'])) echo $_REQUEST['edate']; ?>">

<?php echo __("Order ID:","wpdm-premium-package");?> <input size="10" type="text" name="oid" value="<?php if(isset($_REQUEST['oid'])) echo $_REQUEST['oid']; ?>">

<input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Apply">
| <b><?php echo $t; ?> <?php echo __("order(s) found","wpdm-premium-package");?></b>
| <span style="color: #3498DB;font-weight: 900"><?php echo __("Total Sales:","wpdm-premium-package");?> <?php $total = $wpdb->get_var("select sum(total) as tamount from {$wpdb->prefix}ahm_orders where payment_status='Completed' or payment_status='Expired'"); echo '$'.number_format($total,2); ?></span>

</div>
<br class="clear">
</div>

<div class="clear"></div>

<table cellspacing="0" class="widefat fixed">
    <thead>
    <tr>
        <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
        <th style="width: 40px" class="manage-column" id="media" scope="col">
            <div class="w3eden">
                <span class="fa-stack ttip" title="<?php echo __("Order Status","wpdm-premium-package");?>">
                    <i class="fa fa-circle-thin fa-stack-2x"></i>
                    <i class="fa fa-bars fa-stack-1x"></i>
                </span>
            </div>
        </th>
        <th style="" class="manage-column" id="media" scope="col"><?php echo __("Order","wpdm-premium-package");?></th>
        <th style="width: 40px" class="manage-column" id="media" scope="col">
            <div class="w3eden">
                <span class="fa-stack ttip" title="<?php echo __("Payment Status","wpdm-premium-package");?>">
                    <i class="fa fa-circle-thin fa-stack-2x"></i>
                    <i class="fa fa-money fa-stack-1x"></i>
                </span>
            </div>
        </th>
        <th style="width: 150px" class="manage-column" id="author" scope="col"><?php echo __("Total","wpdm-premium-package");?></th>
        <th style="" class="manage-column" id="author" scope="col"><?php echo __("Customer","wpdm-premium-package");?></th>
        <th style="width: 200px" class="manage-column column-parent" id="parent" scope="col"><?php echo __("Order Date","wpdm-premium-package");?></th>
        <th style="width: 40px" class="manage-column" id="parent" scope="col">
            <div class="w3eden">
                <span class="fa-stack ttip" title="<?php echo __('Item Download Status','wpdm-premium-package'); ?>">
                    <i class="fa fa-circle-thin fa-stack-2x"></i>
                    <i class="fa fa-download fa-stack-1x"></i>
                </span>
            </div>
        </th>
    </tr>
    </thead>

    <tfoot>
    <tr>
        <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
        <th style="" class="manage-column" id="media" scope="col" title="<?php echo __("Order Status","wpdm-premium-package");?>">
            <span class="fa-stack">
                <i class="fa fa-circle-thin fa-stack-2x"></i>
                <i class="fa fa-bars fa-stack-1x"></i>
            </span>
        </th>
        <th style="" class="manage-column" id="media" scope="col"><?php echo __("Order","wpdm-premium-package");?></th>
        <th style="width: 40px" class="manage-column" id="media" scope="col" title="<?php echo __("Payment Status","wpdm-premium-package");?>">
            <span class="fa-stack">
                <i class="fa fa-circle-thin fa-stack-2x"></i>
                <i class="fa fa-money fa-stack-1x"></i>
            </span>
        </th>
        <th style="" class="manage-column" id="author" scope="col"><?php echo __("Total","wpdm-premium-package");?></th>
        <th style="" class="manage-column " id="author" scope="col"><?php echo __("Customer","wpdm-premium-package");?></th>
        <th style="" class="manage-column" id="parent" scope="col"><?php echo __("Order Date","wpdm-premium-package");?></th>
        <th style="width: 40px" class="manage-column" id="parent" scope="col">
            <div class="w3eden">
                <span class="fa-stack ttip" title="<?php echo __('Item Download Status','wpdm-premium-package'); ?>">
                    <i class="fa fa-circle-thin fa-stack-2x"></i>
                    <i class="fa fa-download fa-stack-1x"></i>
                </span>
            </div>
        </th>

    </tr>
    </tfoot>

    <tbody class="list:post" id="the-list">
    <?php
    $z = 'alternate';
    foreach($orders as $order) { 
            $user_info = get_userdata($order->uid);
            $z = $z=='alternate'?'':'alternate';
            $currency = maybe_unserialize($order->currency);
            $currency = is_array($currency) && isset($currency['sign'])?$currency['sign']:'$';
            $citems = maybe_unserialize($order->cart_data);
            $sbilling =  array
            (
                'first_name' => '',
                'last_name' => '',
                'company' => '',
                'address_1' => '',
                'address_2' => '',
                'city' => '',
                'postcode' => '',
                'country' => '',
                'state' => '',
                'email' => '',
                'order_email' => '',
                'phone' => ''
            );
            $billing = unserialize($order->billing_info);
            $billing = shortcode_atts($sbilling, $billing);
            $items = 0;
            if(is_array($citems)){
            foreach($citems as $ci){
                $items += (int)$ci['quantity'];
            }}
        ?>
    <tr valign="top" class="<?php echo $z;?> author-self status-inherit" id="post-8">
        <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $order->order_id; ?>" name="id[]"></th>
        <td class="">
            <div class="w3eden">
                <span title="<?php echo $order->order_status; ?>" class="fa-stack oa-<?php echo $order->order_status; ?>">
                    <i class="fa fa-circle-thin fa-stack-2x"></i>
                    <i class="fa fa-<?php echo $osi[$order->order_status]; ?> fa-stack-1x"></i>
                </span>
            </div>
        </td>
        <td class="">
            <strong>
                <a title="Edit" href="edit.php?post_type=wpdmpro&page=orders&task=vieworder&id=<?php echo $order->order_id; ?>"><?php echo $order->title; ?> #<?php echo $order->order_id; ?></a>
            </strong><br/>
            <small><?php echo $items; ?> <?php echo __("items","wpdm-premium-package");?></small>
        </td>
        <td class="">
            <div class="w3eden">
                <span title="<?php echo $order->payment_status; ?>" class="fa-stack oa-<?php echo $order->payment_status; ?>">
                    <i class="fa fa-circle-thin fa-stack-2x"></i>
                    <i class="fa fa-<?php echo $osi[$order->payment_status]; ?> fa-stack-1x"></i>
                </span>
            </div>
        </td>
        <td class=""><?php echo $currency?$currency:'$'; echo number_format($order->total,2); ?><br/>
            <small class="note"><?php _e('Via','wpdm-premium-package'); echo " ".$order->payment_method; ?></small>
        </td>
        <td class="">
            <?php if(is_object($user_info)){ ?>
                <b><a href="user-edit.php?user_id=<?php echo $user_info->ID; ?>"><?php echo $user_info->display_name; ?></a></b>
                <a class="text-filter" href="edit.php?post_type=wpdmpro&page=orders&customer=<?php echo $user_info->ID; ?>"><i class="fa fa-search"></i></a><br/>
                <a href="mailto:<?php echo $user_info->user_email; ?>"><?php echo $user_info->user_email; ?></a>
            <?php } else { ?>
                <b><?php echo $billing['first_name'].' '.$billing['last_name']; ?></b>
                <a class="text-filter" href="edit.php?post_type=wpdmpro&page=orders&customer=<?php echo $billing['order_email']; ?>"><i class="fa fa-search"></i></a><br/>
                <a href="mailto:<?php echo $billing['order_email']; ?>"><?php echo $billing['order_email']; ?></a>
            <?php }?>
        </td>
        <td class=""><?php echo date("M d, Y h:i a",$order->date); ?></td>
        <td style="" class="" id="parent" scope="col">
            <div class="w3eden">
                <span class="fa-stack download-<?php echo $order->download==0?'off':'on'; ?> ttip" title="<?php echo $order->download==0?__('New','wpdm-premium-package'):__('Downloaded','wpdm-premium-package'); ?>">
                    <i class="fa fa-circle-thin fa-stack-2x"></i>
                    <i class="fa fa-toggle-<?php echo $order->download==0?'off':'on'; ?> fa-stack-1x"></i>
                </span>
            </div>
        </td>
     </tr>
     <?php } ?>
    </tbody>
</table>
                    
<?php
$page_links = paginate_links( array(
    'base' => add_query_arg( 'paged', '%#%' ),
    'format' => '',
    'prev_text' => __('&laquo;'),
    'next_text' => __('&raquo;'),
    'total' => ceil($t/$l),
    'current' => $p
));
?>

<div id="ajax-response"></div>

<div class="tablenav">
    <?php if ( $page_links ) {
        if(!isset($_GET['paged'])) $_GET['paged'] = 1;
    ?>
    <div class="tablenav-pages">
        <?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
        number_format_i18n( ( $_GET['paged'] - 1 ) * $l + 1 ),
        number_format_i18n( min( $_GET['paged'] * $l, $t ) ),
        number_format_i18n( $t ),
        $page_links
        );
        echo $page_links_text; ?>
    </div>
    <?php } ?>

    <div class="alignleft actions">
        <input type="submit" class="button-primary action" id="delete_selected" name="delete_selected" value="Delete Selected">
        <input type="hidden" id="delete_confirm" name="delete_confirm" value="0" />
    </div>
    
    <select class="select-action" name="delete_all_by_payment_sts">
        <option value=""><?php _e('Payment Status:','wpdmpp'); ?></option>
        <option value="Pending"><?php _e('Pending','wpdmpp'); ?></option>
        <option value="Processing"><?php _e('Processing','wpdmpp'); ?></option>
        <option value="Cancelled"><?php _e('Cancelled','wpdmpp'); ?></option>
    </select>

    <input type="submit" class="button-primary action" name="delete_by_payment_sts" value="Delete All By Payment Status" />
    <br class="clear">
</div>
    <div style="display: none;" class="find-box" id="find-posts">
        <div class="find-box-head" id="find-posts-head"><?php echo __("Find Posts or Pages","wpdm-premium-package");?></div>
        <div class="find-box-inside">
            <div class="find-box-search">
                
                <input type="hidden" value="" id="affected" name="affected">
                <input type="hidden" value="3a4edcbda3" name="_ajax_nonce" id="_ajax_nonce">                <label  for="find-posts-input" class="screen-reader-text"><?php echo __("Search","wpdm-premium-package"); ?></label>
                <input type="text" value="" name="ps" id="find-posts-input">
                <input type="button" class="button" value="Search" onclick="findPosts.send();"><br>

                <input type="radio" value="posts" checked="checked" id="find-posts-posts" name="find-posts-what">
                <label  for="find-posts-posts"><?php echo __("Posts","wpdm-premium-package"); ?></label>
                <input type="radio" value="pages" id="find-posts-pages" name="find-posts-what">
                <label  for="find-posts-pages"><?php echo __("Pages","wpdm-premium-package"); ?></label>
            </div>
            <div id="find-posts-response"></div>
        </div>
        <div class="find-box-buttons">
            <input type="button" value="Close" onclick="findPosts.close();" class="button alignleft">
            <input type="submit" value="Select" class="button-primary alignright" id="find-posts-submit">
        </div>
    </div>
</form>
<br class="clear">
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
        $("#delete_selected").on('click',function(){
            $("#delete_confirm").val("1");
        });
        $('span.fa-stack').tooltip({placement:'bottom', padding: 10, template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'});
    });
</script>
 