<div class="w3eden">
<div id="gonotice"></div>
<form method="post" id="goform">
    <div class="panel panel-success">
        <div class="panel-heading text-lg"><?php _e('Guest Order Access','wpdm-premium-package'); ?></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php _e('Order Email:','wpdm-premium-package'); ?></label>
                        <input type="email" required="required" id="goemail" name="go[email]" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php _e('Order ID:','wpdmpp'); ?></label>
                        <input type="text" required="required" id="goorder" name="go[order]" value="<?php echo isset($_SESSION['last_order'])?$_SESSION['last_order']:''; ?>" class="form-control" />
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <button class="btn btn-primary btn-sm" id="goproceed"><?php _e('Proceed','wpdm-premium-package'); ?> &nbsp; <i class="fa fa-chevron-right"></i></button>
        </div>
    </div>
</form>

<?php
global $wpdmpp_settings;
if( isset( $_SESSION['guest_order'] ) ){

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

        $order = new Order();
        $order = $order->GetOrder($_SESSION['guest_order']);
        /* if($order->uid == 0) {
            $order->uid = $current_user->ID;
            $o->update(array('uid' => $current_user->ID), $order->order_id);
        }*/


            $order->currency = maybe_unserialize($order->currency);
            $csign = isset($order->currency['sign']) ? $order->currency['sign'] : '$';
            $cart_data = unserialize($order->cart_data);
            $items = Order::GetOrderItems($order->order_id);

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

            $order->title = $order->title ? $order->title : 'Order # ' . $order->order_id;

            if ($order->order_status == 'Completed') {//Show invoice button

                $_ohtml = <<<OTH
<div class="panel panel-default panel-purchases">
<div class="panel-heading">
<span class="pull-right"  style="margin-top:-3px;">
<button class="btn btn-primary btn-xs white btn-billing" id="edit-billing" data-toggle="modal" data-target="#billing-modal"><i class="fa fa-pencil"></i> Edit Billing Info</button>
<a class="btn btn-info btn-xs white btn-invoice" href="#" onclick="window.open('?id={$order->order_id}&amp;wpdminvoice=1','Invoice','height=720, width = 750, toolbar=0'); return false;"><i class="fa fa-bars"></i> Invoice</a></span>{$order->title} </b></div>
<div>
<table class="table" style="margin:0;border:0;">
<thead>
<tr>
    <th>$prdct</th>
    <th>$qnt</th>
    <th>$unit</th>
    <th>$coup</th>
    <th>$role_dis</th>
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

                        foreach ($files as $index => $ff) {
                            $data = get_post_meta($ditem->ID, '__wpdm_fileinfo', true);
                            $title = $data[$index]['title'] ? $data[$index]['title'] : basename($ff);
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


            $order->cart_discount = number_format($order->cart_discount, 2, ".", "");
            $order->total = number_format($order->total, 2, ".", "");
            $_ohtml .= <<<ITEM
                        <tr class="item">
                        <td colspan="5" class='text-right' align='right'><b>$dsct</b></td>
                        <td class='text-right' align='right'><b>{$csign}{$order->cart_discount}</b></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="item">
                        <td colspan="5" class='text-right' align='right'><b>$ttl</b></td>
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

echo $_ohtml;

}
?>
    <div class="modal fade" tabindex="-1" role="dialog" id="billing-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="billing-info-form" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title"><strong>Edit Billing Info</strong></h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        $billing = $sbilling =  array
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
                        $sbilling = unserialize($order->billing_info);
                        $billing  = shortcode_atts($billing, $sbilling);
                        ?>
                        <!-- full-name input-->
                        <div class="form-group">
                            <label class="control-label"><?php echo __("Full Name","wpdm-premium-package"); ?> <span class="required" title="<?php _e('Required','wpdm-premium-package'); ?>">*</span></label>
                            <div class="controls row">
                                <div class="col-md-6">
                                    <input id="f-name" value="<?php echo $billing['first_name']; ?>" name="billing[first_name]" required="required" type="text" placeholder="<?php echo __("First Name","wpdm-premium-package"); ?>" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input id="l-name" value="<?php echo $billing['last_name']; ?>" name="billing[last_name]" type="text" required="required" placeholder="<?php echo __("Last Name","wpdm-premium-package"); ?>" class="form-control">
                                </div>

                            </div>
                        </div>
                        <!-- company name input-->
                        <div class="form-group">
                            <label class="control-label"><?php echo __("Company Name","wpdm-premium-package"); ?></label>
                            <div class="controls">
                                <input id="address-line1" value="<?php echo $billing['company']; ?>" name="billing[company]" type="text" placeholder="<?php echo __("(Optional)","wpdm-premium-package"); ?>" class="form-control">

                            </div>
                        </div>
                        <!-- address-line1 input-->
                        <div class="form-group">
                            <label class="control-label"><?php echo __("Address Line 1","wpdm-premium-package"); ?> <span class="required" title="<?php _e('Required','wpdm-premium-package'); ?>">*</span></label>
                            <div class="controls">
                                <input id="address-line1" name="billing[address_1]" value="<?php echo $billing['address_1']; ?>" type="text" required="required" placeholder="<?php echo __("address line 1","wpdm-premium-package"); ?>" class="form-control">

                            </div>
                        </div>
                        <!-- address-line2 input-->
                        <div class="form-group">
                            <label class="control-label"><?php echo __("Address Line 2","wpdm-premium-package"); ?></label>
                            <div class="controls">
                                <input id="address-line2" name="billing[address_2]" value="<?php echo $billing['address_2']; ?>" type="text" placeholder="<?php echo __("address line 2","wpdm-premium-package"); ?>" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <!-- city input-->
                                <div class="col-md-4">
                                    <label class="control-label"><?php echo __("City / Town","wpdm-premium-package"); ?> <span class="required" title="<?php _e('Required','wpdm-premium-package'); ?>">*</span></label>
                                    <div class="controls">
                                        <input id="city" value="<?php echo $billing['city']; ?>" name="billing[city]" type="text" required="required" placeholder="<?php echo __("city","wpdm-premium-package"); ?>" class="form-control">
                                        <p class="help-block"></p>
                                    </div>
                                </div>
                                <!-- region input-->
                                <div class="col-md-4">
                                    <label class="control-label"><?php echo __("State / Province","wpdm-premium-package"); ?> <span class="required" title="<?php _e('Required','wpdm-premium-package'); ?>">*</span></label>
                                    <div class="controls">
                                        <input id="region" name="billing[state]" value="<?php echo $billing['state']; ?>" type="text" placeholder="<?php echo __("state / province / region","wpdm-premium-package"); ?>"
                                               class="form-control">
                                        <p class="help-block"></p>
                                    </div>
                                </div>
                                <!-- postal-code input-->
                                <div class="col-md-4">
                                    <label class="control-label"><?php echo __("Zip / Postal Code","wpdm-premium-package"); ?> <span class="required" title="<?php _e('Required','wpdm-premium-package'); ?>">*</span></label>
                                    <div class="controls">
                                        <input id="postal-code" name="billing[postcode]" value="<?php echo $billing['postcode']; ?>" type="text" required="required" placeholder="<?php echo __("zip or postal code","wpdm-premium-package"); ?>"
                                               class="form-control">
                                        <p class="help-block"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- country select -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label"><?php echo __("Country","wpdm-premium-package"); ?> <span class="required" title="<?php _e('Required','wpdm-premium-package'); ?>">*</span></label>
                                    <div class="controls">
                                        <?php
                                        $allowed_countries = get_wpdmpp_option('allow_country');
                                        $all_countries = wpdmpp_countries();
                                        ?>
                                        <select id="country" name="billing[country]" required="required" class="form-control"  data-live-search="true" x-moz-errormessage="<?php echo __("Please Select Your Country","wpdm-premium-package"); ?>">
                                            <option value=""><?php echo __("Select Country","wpdm-premium-package"); ?></option>
                                            <?php foreach($allowed_countries as $country_code){ ?>
                                                <option value="<?php echo $country_code; ?>"><?php echo $all_countries[$country_code]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label><?php echo __("Enter Order Notification Email","wpdm-premium-package"); ?> <span class="required" title="<?php _e('Required','wpdm-premium-package'); ?>">*</span></label>
                                    <input type="email"  value="<?php echo $billing['email']; ?>" required="required" class="form-control" name="billing[order_email]" id="email_m" placeholder="<?php echo __("Enter Order Notification Email","wpdm-premium-package"); ?>">

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <span id="bists" class="pull-left" style="display: none"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
    jQuery(function($){
        var goerrors = new Array();
        goerrors['nosess'] = "<?php _e('Session was expired. Please try again','wpdm-premium-package'); ?>";
        goerrors['noordr'] = "<?php _e('Order not found, Please re-check your info','wpdm-premium-package'); ?>";
        goerrors['nogues'] = "<?php _e('Order is already associated with an account. Please login using that account to get access','wpdm-premium-package'); ?>";

        $('#billing-info-form').submit(function(){
            $('#bists').html('<i class="fa fa-spin fa-refresh"></i> Saving...').fadeIn();
            $(this).ajaxSubmit({
                url: '<?php echo admin_url('admin-ajax.php?action=update_guest_billing'); ?>',
                success: function(res){
                    $('#bists').html('<i class="fa fa-check-circle"></i> '+res);
                    $('#billing-modal').modal('hide');
                }
            });
            return false;
        });

        $('#goform').submit(function(){
            var gop = $('#goproceed').html();
            $('#goproceed').html("<i class='fa fa-spinner fa-spin'></i>");
            $(this).ajaxSubmit({
                success: function(res){
                    if(res.match(/nosess/))  $('#gonotice').html('<div class="alert alert-danger">' + goerrors['nosess'] + '</div>');
                    else if(res.match(/noordr/))  $('#gonotice').html('<div class="alert alert-danger">' + goerrors['noordr'] + '</div>');
                    else if(res.match(/nogues/))  $('#gonotice').html('<div class="alert alert-danger">' + goerrors['nogues'] + '</div>');
                    else if(res.match(/success/)) { location.href = '<?php echo wpdmpp_guest_order_page(); ?>'; gop = "<i class='fa fa-refresh fa-spin'></i>"; }
                    $('#goproceed').html(gop);
                }
            });
            return false;
        });

    });
</script>

<style>
    .list-group{ padding: 0 !important; }
    .list-group, .list-group li{ margin: 0 !important; }
    .inline{ display: inline; }
</style>

</div>