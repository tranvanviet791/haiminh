<div class='panel panel-default dashboard-panel' style='margin: 10px 0'>
    <div class='panel-heading'>If you do not see your order:</div>
    <div class='panel-body'>
        <form id='resolveorder' method='post' style='height: 40px'>
            <input type='hidden' name='action' value='resolveorder'/>
            <div class='input-group'><input type='text' name='orderid' value='' placeholder='Enter Your Order/Invoice ID Here' class='form-control'>
                <span class='input-group-btn'>
                    <button class='btn btn-info' type='submit' style="height: 36px">Resolve</button>
                </span>
            </div>
        </form>
        <div id='w8o' class='text-danger' style='height: 40px;line-height: 40px;display: none;cursor: pointer'>
            <i class='fa fa-spinner fa-spin'></i> Please Wait...
        </div>
    </div>
</div>

<script>
    jQuery(function($){
        $('#resolveorder').submit( function(){
            $('#resolveorder').slideUp();
            $('#w8o').html("<i class='fa fa-spinner fa-spin' ></i> Tracking Order...").slideDown();
            $(this).ajaxSubmit({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                success: function(res){
                    if(res=='ok') {
                        $('#w8o').html('<span class="text-success"><i class="fa fa-check" ></i> Order is linked with your account successfully.</span>');
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

<?php
global $wpdb, $sap, $wpdmpp_settings, $current_user;
$order = new Order();
$myorders = $order->GetOrders($current_user->ID);
$order_notes = '';
$orderurl = get_permalink(get_the_ID());
$loginurl = home_url("/wp-login.php?redirect_to=".urlencode($orderurl));
       
if(!isset($_GET['id']) && !isset($_GET['item'])){
    $orderid=__("Order Id","wpdm-premium-package");
    $date=__("Date","wpdm-premium-package");
    $payment_status=__("Status","wpdm-premium-package");
    $_ohtml = <<<ROW
<div class="panel panel-default panel-purchases dashboard-panel">
<div class="panel-heading">All Orders</div>

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

$order_validity_period = $wpdmpp_settings['order_validity_period']*86400;
foreach($myorders as $order){ 
    $date = date("Y-m-d h:i a",$order->date);
    $items = unserialize($order->items);

    $expire_date = $order->expire_date;
    if(intval($expire_date) ==0 ) {
        $expire_date = $order->date + $order_validity_period;
        $ordcls->Update(array( 'expire_date' => $expire_date ), $order->order_id);
    }

    if( time() > $expire_date && $order->order_status != 'Expired' ){
        $ordcls->Update(array( 'order_status'=>'Expired','payment_status'=>'Expired' ),$order->order_id);
        $order->order_status = 'Expired';
        $order->payment_status = 'Expired';
    }

    $zurl = get_permalink(get_the_ID()).'/purchases/order/';

    $nonce = wp_create_nonce("delete_order");
    $del = $order->order_status=='Processing'?'<a href="#" data-toggle="tooltip" title="Delete Order" class="delete_order btn btn-xs btn-danger" order_id="'.$order->order_id.'" nonce="'.$nonce.'"><i class="fa fa-times"></i></a>':'<a href="#" class="btn btn-xs btn-success" disabled="disabled"><i class="fa fa-check"></i></a>';
    $_ohtml .= <<<ROW
                    <tr class="order" id="order_{$order->order_id}">
                        <td>{$del}</td>
                        <td><a href='{$zurl}{$order->order_id}/'>{$order->order_id}</a></td>
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
$link = admin_url('admin-ajax.php');
$_ohtml .=<<<SCRIPT
        <script type='text/javascript'>
jQuery(document).ready(function($){
   $('.delete_order').on('click',function(){
        var nonce = $(this).attr('nonce');
        var order_id = $(this).attr('order_id');
        var url = "$link";
        var th = $(this);
        jQuery('#order_'+order_id).fadeTo('0.5');
        if(confirm("Are you sure you want to delete this order ?")){
            $(this).html('<i class="fa fa-spinner fa-spin"></i>').css('outline','none');
            jQuery.ajax({
             type : "post",
             dataType : "json",
             url : url,
             data : {action: "wpdmpp_delete_frontend_order", order_id : order_id, nonce: nonce},
             success: function(response) {
            //console.log(response);
                if(response.type == "success") {
                   $('#order_'+order_id).slideUp();
                   //alert('successfull...');
                }
                else {
                   alert("Something went wrong during deleting...")
                }
             }
            }); 
        }
        return false;
   });
});
        </script>
SCRIPT;

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
$_order404 = "";

echo "{$_ohtml}{$order_notes}";

?>