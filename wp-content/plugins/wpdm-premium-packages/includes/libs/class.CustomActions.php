<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class CustomActions {

    /**
     * @usage Updates Payment Status
     */
    function UpdatePS(){
        global $wpdb;
        if(!current_user_can(WPDMPP_MENU_ACCESS_CAP)) return;

        $wpdb->update("{$wpdb->prefix}ahm_orders",array('payment_status'=>$_POST['status']),array('order_id'=>$_POST['order_id']));
        die(__('Payment status updated',"wpdm-premium-package"));
    }

    /**
     * @usage Updates Order Status
     */
    function UpdateOS(){
        global $wpdb;
        if(!current_user_can(WPDMPP_MENU_ACCESS_CAP)) return;

        //order status change hook, order_id, new_status_message
        apply_filters("order_status_updated",$_POST['order_id'],$_POST['status']);

        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

        $_POST['status'] = esc_attr($_POST['status']);

        $update_data = array();

        if($_POST['status'] == 'Renew'){
            $_POST['status'] = 'Completed';
            $update_data['payment_status'] = 'Completed';
            $update_data['expire_date'] = strtotime("+".$settings['order_validity_period']." days");
        }

        $update_data['order_status'] = $_POST['status'];

        $wpdb->update("{$wpdb->prefix}ahm_orders", $update_data,array('order_id'=>esc_attr($_POST['order_id'])));

        //Let the customer of that order know about order status change
        $siteurl = home_url("/");
        $order = $wpdb->get_row("select * from {$wpdb->prefix}ahm_orders where order_id='".esc_attr($_POST['order_id'])."'");
        $user_info = get_userdata($order->uid);
        $admin_email = get_bloginfo("admin_email");
        $email = array();
        $subject = "Order Status Changed";

        $message = "The order {$_POST['order_id']} is changed to {$_POST['status']}"."\n Customer Name is ".$user_info->user_firstname." ".$user_info->lastname."\n Email is ".$user_info->user_email;
        $email['subject'] = $subject;
        $email['body'] = $message;
        $email['headers'] = 'From:  <'.$admin_email.'>' . "\r\n";
        $email = apply_filters("order_status_change_email", $email);
        wp_mail($user_info->user_email,$email['subject'],$email['body'],$email['headers']);
        die(__('Order status updated',"wpdm-premium-package"));
    }

    /**
     * @usage Payment for Order
     * @param array $post_data
     */
    function PayNow($post_data = array()){
        global $wpdb, $current_user;
        if(count($post_data) == 0) $post_data = $_POST;
        
        $order = new Order();
        $corder = $order->GetOrder($post_data['order_id']);
        $payment = new Payment();
        wpdmpp_empty_cart();
        $_SESSION['orderid'] = $corder->order_id;
        if(!isset($post_data['payment_method']) || $post_data['payment_method'] == '')  $post_data['payment_method'] = $corder->payment_method;
        $payment->InitiateProcessor($post_data['payment_method']);
        $payment->Processor->OrderTitle = 'Order# '.$corder->order_id;
        if($corder->order_status == 'Expired')
        $payment->Processor->InvoiceNo = $corder->order_id."_renew_".date("Ymd");
        else
        $payment->Processor->InvoiceNo = $corder->order_id;
        $payment->Processor->Custom = $corder->order_id;
        $payment->Processor->Amount = number_format($corder->total,2,".","");
        echo $payment->Processor->ShowPaymentForm(1);
    }

    /**
     * Add Order Note ( Called through wpdmpp_ajax_call function )
     */
    function AddNote(){
        global $wpdb;
        $id = esc_attr($_REQUEST['order_id']);
        $data = array('note' => $_REQUEST['note']);
        if(isset($_REQUEST['admin'])) $data['admin'] = 1;
        if(isset($_REQUEST['seller'])) $data['seller'] = 1;
        if(isset($_REQUEST['customer'])) $data['customer'] = 1;
        if(isset($_REQUEST['file'])) $data['file'] = $_REQUEST['file'];

        if(Order::add_note($id, $data)) {

            $copy = array();
            if(isset($data['admin'])) $copy[] = '<input type=checkbox checked=checked disabled=disabled /> Admin &nbsp; ';
            if(isset($data['seller'])) $copy[] = '<input type=checkbox checked=checked disabled=disabled /> Seller &nbsp; ';
            if(isset($data['customer'])) $copy[] = '<input type=checkbox checked=checked disabled=disabled /> Customer &nbsp; ';
            $copy = implode("", $copy);
            ?>

            <div class="panel panel-default">
                <div class="panel-body">
                    <?php echo esc_attr($data['note']); ?>
                </div>
                <?php if(isset($_REQUEST['file'])){ ?>
                    <div class="panel-footer text-right">
                        <?php foreach($_REQUEST['file'] as $file){ ?>
                            <a href="#" style="margin-left: 10px"><i class="fa fa-paperclip"></i> <?php echo $file; ?></a> &nbsp;
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="panel-footer text-right"><small><em><i class="fa fa-clock-o"></i> <?php echo date(get_option('date_format') . " h:i", time()); ?></em></small>
                    <div class="pull-left"><small><em><?php if($copy!='') echo "Copy sent to ".$copy; ?></em></small></div>
                </div>
            </div>
        <?php }
        else
            echo "error";
    }

    /**
     * Verify License key
     */
    function VerifyLicense(){
        global $wpdb, $wpdmpp_setting;
        extract($_POST);
        $wpdmpp_setting = get_option("_wpdmpp_settings");
        $key = esc_sql($key);
        $data = $wpdb->get_row("select l.*,o.uid,o.payment_status,o.order_status, o.expire_date as order_expire_date from {$wpdb->prefix}ahm_licenses l,{$wpdb->prefix}ahm_orders o where l.licenseno='$key' and l.oid=o.order_id and o.order_status IN ('Completed','Expired')",ARRAY_A);

        if(!$data)
            die('invalid');

        if($data['order_expire_date'] == 0)
            $data['order_expire_date'] = $data['date'] + ($wpdmpp_setting['order_validity_period']*24*3600);

        if($data['order_expire_date'] < time()) $data['order_status'] = 'Expired';

        if($data['order_status'] === 'Expired' && !isset($wpdmpp_setting['license_key_validity']))
            die('invalid');


        if($data['domain'] == ''){
            $domain = serialize(array($domain));
            $dt = time();
            $copy = get_post_meta($data['pid'],'__wpdm_license_usage_limit', true);
            if(!$copy) $copy = 1;
            $wpdb->query("update {$wpdb->prefix}ahm_licenses set domain = '{$domain}',activation_date='{$dt}', copy='$copy' where licenseno='$key'");
            die('valid');
        }
        elseif((@in_array($domain,@maybe_unserialize($data['domain']))||$domain==$data['domain']))
            die('valid');
        elseif(count(unserialize($data['domain']))<$data['copy']&&!in_array($domain,@unserialize($data['domain']))){
            $data['domain'] = unserialize($data['domain']);
            $data['domain'][] = $domain;
            $domain = serialize($data['domain']);
            $wpdb->query("update {$wpdb->prefix}ahm_licenses set domain = '{$domain}' where licenseno='$key'");
            die('valid');
        }
        else
            die('invalid');
    }

    /**
     * Saves current cart
     */
    function SaveCart(){
        $cartdata = wpdmpp_get_cart_data();
        $cartdata = WPDM_Crypt::Encrypt($cartdata);
        $id = uniqid();
        file_put_contents(WPDM_CACHE_DIR.'saved-cart-'.$id.'.txt', $cartdata);
        echo $id;
        die();
    }

    /**
     * Email the current cart link to the provided email address
     */
    function EmailCart(){
        if(isset($_REQUEST['email']) && isset($_REQUEST['carturl'])){
            if(!is_email($_REQUEST['email'])) return;
            $admin_email = get_bloginfo("admin_email");
            $email = array();
            $subject = "Someone sent you a cart!";

            $message = file_get_contents(WPDMPP_BASE_DIR.'templates/email-templates/wpdm-pp-email-cart.html');

            $email['subject'] = $subject;
            $email['body'] = $message;
            $email['headers'] = 'From:  '.$admin_email.'' . "\r\nContent-type: text/html\r\n";

            $params = array(
                '[#date#]' => date(get_option('date_format'),time()),
                '[#homeurl#]' => home_url('/'),
                '[#sitename#]' => get_bloginfo('name'),
                '[#cartdata#]' => $_REQUEST['carturl'],
                '[#carturl#]' => $_REQUEST['carturl'],
                '[#support_email#]' => $admin_email,
                '[#logo#]' => has_site_icon() ? '<img src='.get_site_icon_url().'>' : ''
            );

            $email['body'] = str_replace(array_keys($params), array_values($params), $email['body']);

            $email = apply_filters("email_saved_cart", $email, $_REQUEST);

            wp_mail($_REQUEST['email'],$email['subject'],$email['body'],$email['headers']);

            die('sent');
        }
    }
}