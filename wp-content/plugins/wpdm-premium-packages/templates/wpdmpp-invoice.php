<?php
global $wpdb, $current_user;
$settings = get_option('_wpdmpp_settings');
$orderurl = $_SERVER['REQUEST_URI'];

if (!is_user_logged_in() && !isset( $_SESSION['guest_order'] )) {
    $_ohtml = <<<SIGNIN
<center>
Please <a href="/wp-login.php?redirect_to={$orderurl}" class="simplemodal-login"><b>Log In or Register</b></a> to access this page
</center>
SIGNIN;
} else {
    $order = new Order();
    $oid = is_user_logged_in()?$_GET['id']:$_SESSION['guest_order'];
    $order = $order->GetOrder($_GET['id']);
    $billing_info = unserialize($order->billing_info);
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
        'order_email' => '',
        'phone' => ''
    );
    $billing_info = shortcode_atts($billing, $billing_info);
    $cart_data = unserialize($order->cart_data);
    $items = unserialize($order->items);
    $order->currency = maybe_unserialize($order->currency);
    $currency_symbol = $order->currency['sign'];
    $discount_label = __('Discount', 'wpdmpro');
    $nettotal_label = __('Net Total', 'wpdmpro');
    $total_label = __('Total', 'wpdmpro');
    $vat_label = __('Tax', 'wpdmpro');
    $ordertotal = number_format($order->total, 2);
    $unit_prices = unserialize($order->unit_prices);
    $cart_discount = number_format($order->discount, 2);
    $tax = number_format($order->tax, 2);
    $item_table = <<<OTH
<table class="table table-striped table-bordered" id="invoice-amount" width="100%" cellspacing="0">
<thead>
<tr  id="header_row">
    <th>Item Name</th>
    <th>Quantity</th>     
    <th class='item_r' style="text-align: right;">Unit Price</th>
    <th class='item_r' style="text-align: right;">Net Subtotal</th>
</tr>
</thead>
<tfoot> 
      <tr id="discount_tr"> 
        <td colspan="1">&nbsp;</td> 
        <td colspan="2" class="item_r">{$discount_label}</td> 
        <td class="item_r text-right">{$currency_symbol}{$cart_discount}</td>
      </tr> 
      <tr id="net_total_tr"> 
        <td colspan="1">&nbsp;</td> 
        <td colspan="2" class="item_r">{$nettotal_label}</td> 
        <td class="item_r text-right">{$currency_symbol}{$ordertotal}</td>
      </tr> 
      <tr id="vat_tr"> 
        <td colspan="1">&nbsp;</td> 
        <td colspan="2" class="item_r">{$vat_label}</td> 
        <td class="item_r text-right">{$currency_symbol}{$tax}</td>
      </tr> 
      <tr id="total_tr"> 
        <td colspan="1">&nbsp;</td> 
        <td colspan="2" class="total" id="total_currency">{$total_label}</td> 
        <td class="total text-right">{$currency_symbol}{$ordertotal}</td>
      </tr> 
    </tfoot>
    <tbody>
OTH;

    foreach ($items as $itemid) {
        $item = get_post($itemid);
        $dk = md5($item->files);
        $count = count(maybe_unserialize(get_post_meta($itemid, '__wpdm_files', true)));
        $download_link = home_url("/?wpdmdl={$itemid}&oid={$order->order_id}");
        $cart_data[$itemid] = $cart_data[$itemid] ? $cart_data[$itemid] : 1;
        $netsubtotal = htmlentities(number_format($cart_data[$itemid]['quantity'] * $cart_data[$itemid]['price'], 2));
        $cart_data[$itemid]['ID'] = $itemid;
        $v_name = '';
        $item_table .= <<<ITEM
                    <tr class="item">
                        <td>{$item->post_title} <br/>{$v_name}&nbsp;&nbsp;<small><em>{$count} file(s)</em></small></td>
                        <td>{$cart_data[$itemid]['quantity']}</td>
                        <td class='item_r text-right'>{$currency_symbol}{$cart_data[$itemid]['price']}</td>
                        <td class='item_r text-right'>{$currency_symbol}{$netsubtotal}</td>
ITEM;
    }

    $item_table .= "</tbody></table>";
    $invoice['date'] = date("d M, Y", $order->date);
    @extract(maybe_unserialize(get_user_meta($current_user->ID, 'user_billing_shipping',true)));
    @extract($billing);
    $contact_name = isset($fir) ? $contact_name : '';
    $company_adrs1 = isset($address_1) ? $address_1 : '';
    $company_name = isset($company) ? $company : '';
    $company_adrs2 = isset($address_2) ? "<br/>".$address_2 : '';
    $city = isset($city) ? $city : '';
    $country = isset($country) ? $country : '';
    $phone = isset($phone) ? $phone : '';
    $invoice['client_info'] = <<<CINF

    <div class="vcard" id="client-details"> 
        <div class="fn">{$billing_info['first_name']} {$billing_info['last_name']}</div>
        <div class="org"><h3>{$billing_info['company']}</h3></div>
        <div class="adr">
            <div class="street-address">
            {$billing_info['address_1']}
            {$billing_info['address_2']}
            </div>
            <!-- street-address -->
            <div class="locality">{$billing_info['city']} {$billing_info['postcode']}, {$billing_info['state']}, {$billing_info['country']}</div>
            <div id="client-postcode"><span class="region"></span> <span class="postal-code">{$billing_info['order_email']}</span></div>
        </div>
        <!-- adr -->
    </div>
CINF;

    $invoice['company_name'] = get_option('_wpdm_company_name');
    $invoice['company_address_line1'] = get_option('_wpdm_company_adrs1');
    $invoice['company_address_line2'] = get_option('_wpdm_company_adrs2');
    $invoice['company_address_city'] = get_option('_wpdm_company_city');
    $invoice['company_address_country'] = get_option('_wpdm_company_country');
    $invoice['company_address_phone'] = get_option('_wpdm_company_phone');
    $invoice['transaction_id'] = get_option('_wpdm_company_phone');
    $invoice['company_info'] = <<<CINF
<img alt="Mainlogo_large" class="logo screen" src="http://www.wpdownloadmanager.com/wp-content/uploads/2012/02/wpdm-logo.png" style="max-width:300px" />   
            <div class="vcard" id="company-address"> 
      <div class="fn org"><strong>{$invoice['company_name']}</strong></div>
      <div class="adr"> 
        <div class="street-address">{$invoice['company_address_line1']}<br/>
          {$invoice['company_address_line2']}<br /> 
        </div> 
        <!-- street-address --> 
        <div class="locality">{$invoice['company_address_city']}</div> 
        <div id="company-postcode">{$invoice['company_address_country']}</div> 
      </div> 
      <!-- adr -->        
    <div id="sales-tax-reg-number">Phone: {$invoice['company_address_phone']}</div> 
    </div> 
CINF;


$data = file_get_contents(WPDMPP_BASE_DIR . '/templates/invoice-template/invoice.html');
foreach ($invoice as $var => $val) {
    $data = str_replace("[%{$var}%]", $val, $data);
}
}
//echo $data;die();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href='https://fonts.googleapis.com/css?family=Varela|Montserrat:700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/front.css" />
    <style>
        .w3eden{
            font-family: Varela, serif;
            font-size: 9pt;
        }
        .w3eden th{
            border-bottom: 0 !important;
        }
        .w3eden th,
        .w3eden td{
            font-size: 9pt;
        }
        .w3eden .alert.alert-success:before{
            padding-top: 14px;
        }
        .w3eden h3{
            font-family: Montserrat, serif;
            margin: 0;
            font-size: 11pt;
        }
        .w3eden em{
            color: #888;
            margin-bottom: 8px;
        }
        .w3eden .panel{
            border-radius: 0;
        }
        .w3eden .panel.info-panel .panel-body{
            height: 145px;
        }
        .w3eden .panel .panel-heading{
            border-radius: 0;
        }
        .w3eden .panel-default .panel-heading{
            background: #fafafa;
            border-radius: 0;
        }
        .w3eden h3.invoice-no{
            font-family: Courier, monospace;
            font-size: 14pt;
            font-weight: bold;
            color: #349ADE;
        }
        .w3eden .frow .panel-body{
            height: 50px;
        }
        .w3eden .frow #btn-print{
            margin-top: -5px;
            margin-right: -8px;
        }
        @media print {
            #btn-print {
                display: none;
            }
        }
    </style>
</head>
<body class="w3eden" onload="window.print();">
<div class="container-fluid">
 <br/>
    <div class="row frow">
        <div class="col-xs-<?php echo isset($_GET['renew'])?4:6; ?>">
            <div class="panel panel-default"><div class="panel-heading">
                    <button class="btn btn-primary btn-xs pull-right" id="btn-print" type="button" onclick="window.print();"><i class="fa fa-print"></i> Print Invoice</button><strong>Invoice No</strong>
                </div>
                <div class="panel-body">
                    <h3 class="text-info invoice-no"><?php echo $order->order_id; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-xs-<?php echo isset($_GET['renew'])?4:6; ?> text-right">
            <div class="panel panel-default"><div class="panel-heading">
                    <strong>Order Date</strong>
                </div>
                <div class="panel-body">
                    <?php echo date(get_option('date_format'),$order->date); ?>
                </div>
            </div>
        </div>
        <?php if(isset($_GET['renew'])){ ?>
        <div class="col-xs-4 text-right">
            <div class="panel panel-default"><div class="panel-heading">
                    <strong>Order Renewed On</strong>
                </div>
                <div class="panel-body">
                    <?php echo date(get_option('date_format'),(int)$_GET['renew']); ?>
                </div>
            </div>
        </div>
    <?php } ?>

    </div>

    <div class="row">
        <div class="col-xs-6">
            <div class="panel panel-default info-panel">
                <div class="panel-heading"><strong>From:</strong></div>
                <div class="panel-body">

                    <div class="media">
                        <div class="media-left">
                            <?php if($settings['invoice_logo'] != ""){ ?>
                                <img style="width: auto; height: 50px;" class="media-object" src="<?php echo $settings['invoice_logo']; ?>">
                            <?php } ?>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?php bloginfo('sitename'); ?></h4>
                            <p><?php echo nl2br($settings['invoice_company_address']); ?></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="panel panel-default info-panel">
                <div class="panel-heading"><strong>To:</strong></div>
                <div class="panel-body">
                    <?php echo $invoice['client_info']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php echo $item_table; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel panel-default"><div class="panel-heading">
                    <strong>Payment Method</strong>
                </div>
                <div class="panel-body">
                    <?php echo $order->payment_method; ?>
                </div>
            </div>
        </div>
        <div class="col-xs-6 text-right">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Payment Status</strong>
                </div>
                <div class="panel-body">
                    <?php echo $order->payment_status; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
die();
?>