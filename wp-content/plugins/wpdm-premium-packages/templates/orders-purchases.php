<?php  
global $wpdb, $sap, $wpdmpp_settings, $current_user;
$order_notes = '';
$orderurl = get_permalink(get_the_ID());
$loginurl = home_url("/wp-login.php?redirect_to=".urlencode($orderurl));

if ( !is_user_logged_in() ) {
    $_ohtml = wpdm_login_form(array('redirect'=>$orderurl));
} else {
    if(!isset($_GET['id']) && !isset($_GET['item'])){
        $orderid=__("Order Id","wpdm-premium-package");
        $date=__("Date","wpdm-premium-package");
        $payment_status=__("Payment Status","wpdm-premium-package");
        $_ohtml = <<<ROW
<div class="panel panel-default panel-purchases">
<div class="panel-heading">Purchases</div>
<table class="table" style="margin:0;">
<thead>
    <tr>
        <th style="width:50px;"></th>
        <th>$orderid</th>
        <th>$date</th>
        <th style="width: 180px;">$payment_status</th>
    </tr>
</thead>
ROW;

$ordcls = new Order();

$order_validity_period = $wpdmpp_settings['order_validity_period'] * 86400;

foreach($myorders as $order){
    $date = date("Y-m-d h:i a",$order->date);
    $items = unserialize($order->items);
    $expire_date = $order->expire_date;

    if(intval($expire_date) == 0 ) {
        $expire_date = $order->date + $order_validity_period;
        $ordcls->Update(array( 'expire_date' => $expire_date ), $order->order_id);
    }

    if( time() > $expire_date && $order->order_status != 'Expired' ){
        $ordcls->Update(array( 'order_status'=>'Expired','payment_status'=>'Expired' ),$order->order_id);
        $order->order_status = 'Expired';
        $order->payment_status = 'Expired';
    }

    $zurl = $orderurl . $sap;
    $nonce = wp_create_nonce("delete_order");
    $del = ( $order->order_status == 'Processing' ) ? '<a href="#" data-toggle="tooltip" title="Delete Order" class="delete_order btn btn-xs btn-danger" order_id="'.$order->order_id.'" nonce="'.$nonce.'"><i class="fa fa-times"></i></a>' : '<a href="#" class="btn btn-xs btn-success" disabled="disabled"><i class="fa fa-check"></i></a>';
    $_ohtml .= <<<ROW
                <tr class="order" id="order_{$order->order_id}">
                    <td>{$del}</td>
                    <td><a href='{$zurl}id={$order->order_id}'>{$order->order_id}</a></td>
                    <td>{$date}</td>
                    <td>{$order->payment_status}</td>
                </tr>
ROW;
}

        $homeurl = home_url('/');
        $_ohtml .=<<<END
</table></div>
END;

$_ohtml .= <<<STYLE
<style>
.row-actions {
    padding: 2px 0 0;
    visibility: hidden;
}
tr:hover .row-actions{
    visibility: visible;
}
</style>        
STYLE;



}

$odetails   = __("Purchases","wpdm-premium-package");
$ostatus    = __("Order Status","wpdm-premium-package");
$prdct      = __("Product","wpdm-premium-package");
$qnt        = __("Quantity","wpdm-premium-package");
$unit       = __("Unit Price","wpdm-premium-package");
$coup       = __("Coupon Discount","wpdm-premium-package");
$role_dis   = __("Role Discount","wpdm-premium-package");
$ttl        = __("Total","wpdm-premium-package");
$dnl        = __("Download","wpdm-premium-package");
$licns      = __("License","wpdm-premium-package");
$csign =  wpdmpp_currency_sign();
$link = get_permalink();

//Order Details
if(isset($_GET['id']) && $_GET['id'] != '' && !isset($_GET['item'])){
    $o = $order;
    $order = $order->GetOrder($_GET['id']);
    if($order->uid == 0) {
        $order->uid = $current_user->ID;
        $o->update(array('uid' => $current_user->ID), $order->order_id);
    }

    if( $order->uid == $current_user->ID ) {
        $order->currency = maybe_unserialize($order->currency);
        $csign = isset($order->currency['sign']) ? $order->currency['sign'] : '$';
        $cart_data = unserialize($order->cart_data);
        $items = Order::GetOrderItems($order->order_id);

        /*
        if (count($items) == 0) {
            foreach ($cart_data as $pid => $noi) {
                $newi = get_posts(array('post_type' => 'wpdmpro', 'meta_key' => '__wpdm_legacy_id', 'meta_value' => $pid));
                $new_cart_data[$newi[0]->ID] = array("quantity" => $noi, "variation" => "", "price" => get_post_meta($newi[0]->ID, "__wpdm_base_price", true));
                $new_order_items[] = $newi[0]->ID;
            }

            Order::Update(array('cart_data' => serialize($new_cart_data), 'items' => serialize($new_order_items)), $order->order_id);
            Order::UpdateOrderItems($new_cart_data, $order->order_id);
            $items = Order::GetOrderItems($order->order_id);
        }
        */
        $order->title = $order->title ? $order->title : 'Order # ' . $order->order_id;

        if ($order->order_status == 'Completed') {//Show invoice button

            $_ohtml = <<<OTH
<div class="panel panel-default panel-purchases">
<div class="panel-heading">
<span class="pull-right">
    <a class="btn btn-info btn-xs white btn-invoice" href="#" onclick="window.open('?id={$order->order_id}&amp;wpdminvoice=1','Invoice','height=720, width = 750, toolbar=0'); return false;"><i class="fa fa-bars"></i> Invoice</a>
</span>
<b><a href="{$link}" style="display:inline;with:auto;">$odetails</a> &nbsp;<i class="fa fa-angle-double-right"></i>  &nbsp;{$order->title} </b></div>
<div>
<table class="table" style="margin:0;border:0;">
<thead>
<tr>
    <th>$prdct</th>
    <th>$qnt</th>     
    <th>$unit</th>     
    <th>$coup</th>
    <th>$role_dis</th>
    <th>$licns</th>    
    <th class='text-right' align='right'>$ttl</th>     
    <th class='text-right' align='right'>$dnl</th>    
</tr>
</thead>
OTH;
        } else {
            $_ohtml = <<<OTH

<div class="panel panel-default panel-purchases">
<div class="panel-heading"><b><a href="{$link}">$odetails</a> &nbsp;<i class="fa fa-angle-double-right"></i>  &nbsp;{$order->title}</b></div>
<div class="panel-body1">
<table class="table" style="margin:0">
<thead>
<tr>
    <th>$prdct</th>
    <th>$qnt</th>     
    <th>$unit</th>     
    <th>$coup</th>
    <th>$role_dis</th>
    <th>$licns</th>    
    <th class='text-right' align='right'>$ttl</th>     
    <th class='text-right' align='right'>$dnl</th>    
</tr>
</thead>
OTH;

        }
        $total = 0;

        foreach ($items as $item) {

            $ditem = get_post($item['pid']);
            if (!is_object($ditem)) {
                $ditem = new stdClass();
                $ditem->ID = 0;
                $ditem->post_title = "[Item Deleted]";
            }
            $meta = get_post_meta($ditem->ID, 'wpdmpp_list_opts', true);
            $price = $item['price'] * $item['quantity'];

            $discount_r = $item['role_discount'];
            //$discount = $price*($discount_r/100);
            //$aprice = $price - $discount;


            $prices = 0;
            $variations = "";
            $discount = $discount_r;

            $_variations = unserialize($item['variations']);
            foreach ($_variations as $vr) {
                $variations .= "{$vr['name']}: +$" . number_format(floatval($vr['price']), 2);
                $prices += number_format(floatval($vr['price']), 2);
            }

            $itotal = number_format(((($item['price'] + $prices) * $item['quantity']) - $discount - $item['coupon_discount']), 2, ".", "");
            $total += $itotal;
            $download_link = home_url("/?wpdmdl={$item['pid']}&oid={$order->order_id}");
            $licenseurl = home_url("/?task=getlicensekey&file={$item['pid']}&oid={$order->order_id}");
            $order_item = "";
            if ($order->order_status == 'Completed') {
                if (get_post_meta($item['pid'], '__wpdm_enable_license', true) == 1) {
                    //<a id="lic_{$item['pid']}_{$order->order_id}_view" onclick="return viewlic('{$item['pid']}','{$order->order_id}');" class="btn btn-success btn-xs" data-placement="top" href="#"><i class="fa fa-copy white"></i></a>
                    $licenseg = <<<LIC
<a id="lic_{$item['pid']}_{$order->order_id}_btn" onclick="return getkey('{$item['pid']}','{$order->order_id}');" class="btn btn-primary btn-xs" data-placement="top" data-toggle="popover" href="#"><i class="fa fa-key white"></i></a>
LIC;
                } else $licenseg = "&mdash;";

                $indf = "";
                $files = maybe_unserialize(get_post_meta($ditem->ID, '__wpdm_files', true));

                if (count($files) > 1 && $order->order_status == 'Completed') {
                    $index = 0;

                    foreach ($files as $ind => $ff) {
                        $data = get_post_meta($ditem->ID, '__wpdm_fileinfo', true);
                        $title = $data[$ind]['title'] ? $data[$ind]['title'] : basename($ff);
                        $index = WPDM_Crypt::Encrypt($ff);
                        $ff = "<li class='list-group-item' style='padding:10px 15px;'>" . $title . " <a class='pull-right' href=\"{$download_link}&ind={$index}\"><i class='fa fa-download'></i></a></li>";
                        $indf .= "$ff";
                    }
                }
                $discount = number_format(floatval($discount), 2);
                $item['price'] = number_format($item['price'], 2);
                $_ohtml .= <<<ITEM
                    <tr class="item">
                        <td>{$ditem->post_title} <br> {$variations}</td>
                        <td>{$item['quantity']}</td>
                        <td>{$csign}{$item['price']}</td>
                        <td>{$csign}{$item['coupon_discount']}</td>
                        <td>{$csign}{$discount}</td>
                        <td id="lic_{$item['pid']}_{$order->order_id}" >{$licenseg}</td>
                        <td class='text-right' align='right'>{$csign}{$itotal}</td>
ITEM;
            } else {
                $discount = number_format(floatval($discount), 2);
                $item['price'] = number_format($item['price'], 2);
                $_ohtml .= <<<ITEM
                    <tr class="item">
                        <td>{$ditem->post_title} <br> {$variations}</td>
                        <td>{$item['quantity']}</td>
                        <td>{$csign}{$item['price']}</td>
                        <td>{$csign}{$item['coupon_discount']}</td>
                        <td>{$csign}{$discount}</td>
                        <td>&mdash;</td>
                        <td class='text-right' align='right'>{$csign}{$itotal}</td>
ITEM;


            }

            //@extract(get_post_meta($item['pid'],"wpdmpp_list_opts",true));


            if ($order->order_status == 'Completed') {
                $spec = "";
                if (count($files) > 1) $spec = <<<SPEC
<a class="btn btn-xs btn-success btn-group-item" href="#" data-toggle="modal" data-target="#dpop" onclick="jQuery('#dpop .modal-body').html(jQuery('#indvd-{$ditem->ID}').html());"><i class="fa fa-list"></i></a></div><div  id="indvd-{$ditem->ID}" style="display:none;"><ul class='list-group'>{$indf}</ul>
SPEC;


                $_ohtml .= <<<ITEM
                                           
                        <td class='text-right' align='right'><div class="btn-group"><a href="{$download_link}" class="btn btn-xs btn-success btn-group-item"><i class="fa fa-download white"></i></a>{$spec}</div></td>
                    </tr>
ITEM;
            } else {
                $_ohtml .= <<<ITEM
                        <td  class='text-right' align='right'>&mdash;</td>
                    </tr>
ITEM;
            }


            $order_item = apply_filters("wpdmpp_order_item", "", $item);
            if ($order_item != '') $_ohtml .= "<tr><td colspan='7'>" . $order_item . "</td></tr>";


        }
        $dsct = __("Discount", "wpdm-premium-package");
        $cdetails = __("Customer details", "wpdm-premium-package");
        $eml = __("Email", "wpdm-premium-package");
        $bling = __("Billing Address", "wpdm-premium-package");
        $vdlink = __("If you still want to complete this order ", "wpdm-premium-package");
        $vdlink_expired = sprintf(__("If you want to get continuous support and update for another %d days", "wpdm-premium-package"), $wpdmpp_settings['order_validity_period']);
        $pnow = __("Pay Now", "wpdm-premium-package");
        $pnow_expired = __("Renew Now", "wpdm-premium-package");

        $usermeta = unserialize(get_user_meta($current_user->ID, 'user_billing_shipping', true));
        @extract($usermeta);
        $order->cart_discount = number_format($order->cart_discount, 2, ".", "");
        $order->total = number_format($order->total, 2, ".", "");
        $_ohtml .= <<<ITEM
                        <tr class="item">
                        <td colspan="6" class='text-right' align='right'><b>$dsct</b></td>                        
                        <td class='text-right' align='right'><b>{$csign}{$order->cart_discount}</b></td>
                        <td>&nbsp;</td>                       
                    </tr>
                    <tr class="item">
                        <td colspan="6" class='text-right' align='right'><b>$ttl</b></td>                        
                        <td class='text-right' align='right'><b>{$csign}{$order->total}</b></td>
                        <td>&nbsp;</td>
                    </tr>
                    </table>
                  </div>
                                   <div class="modal fade" id="dpop">
  <div class="modal-dialog" style="margin-top:100px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close pull-right" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" style="display:inline;">Download Specific Item</h4>
      </div>
      <div class="modal-body">
         &nbsp;
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
ITEM;

        if ($order->order_status != 'Completed') {
            if ($order->order_status == 'Expired') {
                $vdlink = $vdlink_expired;
                $pnow = $pnow_expired;
            }
            $purl = home_url('/?pay_now=' . $order->order_id);
            $_ohtml .= <<<PAY
    <div class="panel-footer" style="line-height: 30px !important;"><b>$ostatus : <span class='label label-danger'>{$order->order_status}</span></b>&nbsp;&nbsp;
     <div class='pull-right'>$vdlink <div class="pull-right" style="margin-left:10px" id="proceed_{$order->order_id}"><a class='btn btn-success white btn-sm' onclick="return proceed2payment_{$order->order_id}(this)" href="#"><b>$pnow</b></a></div></div>
    </div>
        <script>
           function proceed2payment_{$order->order_id}(ob){
            jQuery('#proceed_{$order->order_id}').html('Processing...');             
            jQuery.post('{$purl}',{action:'wpdm_pp_ajax_call',execute:'PayNow',order_id:'{$order->order_id}'},function(res){
                jQuery('#proceed_{$order->order_id}').html(res);
                });                
                return false;
         }
        </script>
PAY;
        }

        $homeurl = home_url('/');
        $_ohtml .= <<<EOT
</div>        
<script language="JavaScript">
  function getkey(file, order_id){
      jQuery('#lic_'+file+'_'+order_id+'_btn').html("<i class='fa fa-spin fa-spinner white'></i>");  
      jQuery.post('{$homeurl}',{execute:'getlicensekey',fileid:file,orderid:order_id},function(res){
           res = "Copy the following key<br/><input class='form-control' style='cursor:text' onfocus='this.select()' type=text readonly=readonly value='"+res+"' />";
           jQuery('#lic_'+file+'_'+order_id+'_btn').popover({html: true, title: "License Key <button class='pull-right btn btn-danger btn-xs cpo' rel='#lic_"+file+"_"+order_id+"_btn' style='line-height:14px;margin-right:-5px;margin-top:-1px' id='cppo'>&times;</button>", content: res}).popover('show');
           jQuery('#lic_'+file+'_'+order_id+'_btn').html("<i class='fa fa-key white'></i>");
           
           jQuery('.cpo').on("click",function(e) {
 
                jQuery(jQuery(this).attr("rel")).popover('destroy');
                return false;
            });    
   });
    return false;
   }
   
   //To show license TaCs   
   function viewlic(file, order_id){
      var res = " You have to accept these Terms and Conditions before using this product.";
      jQuery('#lic_'+file+'_'+order_id+'_view').html("<i class='fa fa-spin fa-spinner white'></i>");  
      jQuery('#lic_'+file+'_'+order_id+'_view').popover({html: true, title: "Terms and Conditions<button class='pull-right btn btn-danger btn-xs xx' rel='#lic_"+file+"_"+order_id+"_view' id='cppo'>&times;</button>", content: res}).popover('show');
      jQuery('#lic_'+file+'_'+order_id+'_view').html("<i class='fa fa-copy white'></i>");
      
      jQuery('.xx').on("click",function(e) {
 
                jQuery(jQuery(this).attr("rel")).popover('destroy');
                return false;
            });
   
   return false;
   }
   
</script>
      <style>.white{ color: #ffffff !important; } </style>
EOT;

        ob_start();
        include(dirname(__FILE__) . '/order-notes.php');
        $order_notes = ob_get_clean();

    } else {
        $_ohtml .= "<div class='alert alert-danger'>Order doesn't belong to you!</div>";
    }
}

    $formaction = admin_url('admin-ajax.php');

    $_order404 = "<div class='panel panel-default' style='margin: 10px 0'><div class='panel-heading'>If you do not see your order:</div><div class='panel-body'>
    <form id='resolveorder' method='post' style='height: 40px'>
    <input type='hidden' name='action' value='resolveorder' />
    <div class='input-group'><input type='text' name='orderid' value='' placeholder='Enter Your Order/Invoice ID Here' class='form-control' style='border-right: 0'>
     <span class='input-group-btn'>
        <button class='btn btn-primary' type='submit'>Resolve</button>
      </span>
    </div>
    </form>
    <div id='w8o' class='text-danger' style='height: 40px;line-height: 40px;display: none;cursor: pointer'><i class='fa fa-spinner fa-spin'></i> Please Wait...</div>
    </div></div>
    <script>
    jQuery(function($){
            $('#resolveorder').submit( function(){
            $('#resolveorder').slideUp();
            $('#w8o').html(\"<i class='fa fa-spinner fa-spin' ></i> Tracking Order...\").slideDown();
                $(this).ajaxSubmit({
                    url: '{$formaction}',
                    success: function(res){
                    if(res=='ok') {
                      $('#w8o').html('<span class=\"text-success\"><i class=\"fa fa-check\" ></i> Order is linked with your account successfully.</span>');
                      location.href = location.href;
                      }
                    else
                       $('#w8o').html(res);
                    }
                });
                return false;
            });
            $('#w8o').click(function(){
            jQuery(this).slideUp();
            $('#resolveorder').slideDown();
            });
    });
    </script>
    <style>
    td{ vertical-align: middle !important;}
    .panel-footer .alert{font-size: 9pt; line-height: 28px; padding: 0 10px; }
    .panel-footer .btn{ border: 0 !important; margin-top: -3px;}
    </style>
    ";


    if(isset($_GET['id'])) $_order404 = '<br/>';

    $_ohtml = "{$_order404}{$_ohtml}{$order_notes}";
}
?>