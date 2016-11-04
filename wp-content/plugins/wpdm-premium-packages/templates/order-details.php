<?php
global $wpdb, $sap, $wpdmpp_settings, $current_user;
$order_notes = '';
$order = new Order();
$orderurl = get_permalink(get_the_ID());
$loginurl = home_url("/wp-login.php?redirect_to=".urlencode($orderurl));

    $odetails   = __("All Orders","wpdm-premium-package");
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
    $link = get_permalink()."purchases/orders/";

        $o = $order;
        $order = $order->GetOrder($params[2]);
        if($order->uid == 0) {
            $order->uid = $current_user->ID;
            $o->update(array('uid' => $current_user->ID), $order->order_id);
        }
        if( $order->uid == $current_user->ID ) {
            $order->currency = maybe_unserialize($order->currency);
            $csign = isset($order->currency['sign']) ? $order->currency['sign'] : '$';
            $cart_data = unserialize($order->cart_data);
            $items = Order::GetOrderItems($order->order_id);

            if (count($items) == 0) {
                foreach ($cart_data as $pid => $noi) {
                    $newi = get_posts(array('post_type' => 'wpdmpro', 'meta_key' => '__wpdm_legacy_id', 'meta_value' => $pid));
                    if(count($newi) > 0) {
                        $new_cart_data[$newi[0]->ID] = array("quantity" => $noi, "variation" => "", "price" => get_post_meta($newi[0]->ID, "__wpdm_base_price", true));
                        $new_order_items[] = $newi[0]->ID;
                    }
                }

                Order::Update(array('cart_data' => serialize($new_cart_data), 'items' => serialize($new_order_items)), $order->order_id);
                Order::UpdateOrderItems($new_cart_data, $order->order_id);
                $items = Order::GetOrderItems($order->order_id);

            }

            $order->title = $order->title ? $order->title : 'Order # ' . $order->order_id;

            if ($order->order_status == 'Completed') {//Show invoice button


                $_ohtml = <<<OTH


<div class="panel panel-default panel-purchases dashboard-panel">
<div class="panel-heading"><span class="pull-right btn-group" style="margin-top: -5px;margin-right: -10px"><button id="btn-fullwidth" class="btn btn-xs btn-primary ttip" title="Toggle Full-Width"><i class="fa fa-arrows-h"></i></button> <a class="btn btn-info btn-xs white btn-invoice" href="#" onclick="window.open('?id={$order->order_id}&amp;wpdminvoice=1','Invoice','height=720, width = 750, toolbar=0'); return false;"><i class="fa fa-bars"></i> Invoice</a></span><b><a href="{$link}" class="pull-left">$odetails</a> &nbsp;<i class="fa fa-angle-double-right"></i>  &nbsp;{$order->title} </b></div>
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

<div class="panel panel-default panel-purchases dashboard-panel">
<div class="panel-heading"><span class="pull-right btn-group" style="margin-top: -5px;margin-right: -10px"><button id="btn-fullwidth" class="btn btn-xs btn-primary ttip" title="Toggle Full-Width"><i class="fa fa-arrows-h"></i></button></span><b><a href="{$link}" class="pull-left">$odetails</a> &nbsp;<i class="fa fa-angle-double-right"></i>  &nbsp;{$order->title}</b></div>
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
                if (!is_object($ditem) || get_post_type($item['pid']) != 'wpdmpro') {
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
            $vdlink_expired = sprintf(__("Get continuous support and update for another %d days", "wpdm-premium-package"), $wpdmpp_settings['order_validity_period']);
            $pnow = __("Pay Now", "wpdm-premium-package");
            $pnow_expired = __("Renew Now", "wpdm-premium-package");

            $usermeta = unserialize(get_user_meta($current_user->ID, 'user_billing_shipping', true));
            if(is_array($usermeta)) extract($usermeta);
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
        <p>One fine body&hellip;</p>
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
     <div class='pull-right'>$vdlink <div class="pull-right" style="margin-left:10px" id="proceed_{$order->order_id}"><a class='btn btn-success white btn-xs' onclick="return proceed2payment_{$order->order_id}(this)" href="#"><b>$pnow</b></a></div></div>
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

    $formaction = admin_url('admin-ajax.php');



    echo "{$_ohtml}{$order_notes}";

?>