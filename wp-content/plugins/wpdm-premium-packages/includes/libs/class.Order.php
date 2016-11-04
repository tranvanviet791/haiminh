<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

    class Order{
        var $oid;
        function __construct( $oid = '' ){
            if($oid) {
                $this->oid = $oid;
                $order = $this->GetOrder($oid);
                $order = (array)$order;
                if(is_array($order)) {
                    foreach ($order as $key => $val) {
                        $key = str_replace("_"," ", $key);
                        $key = ucwords($key);
                        $key = str_replace(" ","", $key);
                        $this->$key = maybe_unserialize($val);
                    }
                }
            }
        }

        function NewOrder($id, $title, $items, $total, $userid, $order_status = 'processing', $payment_status = 'processing', $cart_data = '', $order_notes="", $payment_method = ""){
            global $wpdb, $current_user;        
            
            $currency = array('sign' => wpdmpp_currency_sign(),'code' => wpdmpp_currency_code());
            $currency = serialize($currency);
            $ret = $wpdb->insert("{$wpdb->prefix}ahm_orders",array('order_id'=>$id, 'title'=>$title,'date'=>time(), 'items'=> $items,'total'=> $total, 'order_status'=>$order_status, 'payment_status'=> $payment_status, 'cart_data'=>$cart_data,'uid'=>$userid,'order_notes'=>$order_notes,'payment_method'=>$payment_method, 'download'=>0, 'IP'=>$_SERVER['REMOTE_ADDR'], 'currency'=> $currency));

            if(!$ret) dd($wpdb->show_errors());
            $_SESSION['orderid'] = $id;
            return $id;
        }

        static function Update($data, $id){
            global $wpdb;
            $res = $wpdb->update("{$wpdb->prefix}ahm_orders",$data,array('order_id'=>$id));
            return $res;
        }

        /**
         * @param $id Order ID
         * @param $data
         * @param string $type
         * @return bool
         */
        static function add_note($id, $data, $type = 'messages'){
            global $wpdb, $current_user;

            if(!is_user_logged_in()) return false;

            $order_info = $wpdb->get_row("select * from {$wpdb->prefix}ahm_orders where order_id='{$id}'");

            if($current_user->ID != $order_info->uid && !current_user_can(WPDMPP_MENU_ACCESS_CAP)) return false;

            $order_note = $order_info->order_notes;

            if(!isset($data['by'])) {
                $data['by'] = $current_user->ID == $order_info->uid?'Customer':'Seller';
            }

            $fromname = get_bloginfo('name');
            $frommail = "no-reply@".$_SERVER['HTTP_HOST'];

            $customer = get_user_by('id', $order_info->uid);

            $viewlink_customer = wpdmpp_orders_page('id='.$id);
            $viewlink_admin = admin_url("/edit.php?post_type=wpdmpro&page=orders&task=vieworder&id={$id}");

            if(isset($data['admin'])) wp_mail(get_option("admin_email"), "New Note: Order# {$id}", $data['note']."<br/>View Order: ".$viewlink_admin, "From: {$fromname} <{$frommail}>\r\nContent-type: text/html\r\n");
            if(isset($data['customer'])) wp_mail($customer->user_email, "New Note: Order# {$id}", $data['note']."<br/>View Order: ".$viewlink_customer, "From: {$fromname} <{$frommail}>\r\nContent-type: text/html\r\n");

            $order_note = maybe_unserialize($order_note);
            $order_note[$type][time()] = $data;
            order::Update(array('order_notes' => serialize($order_note)), $id);
            return true;
        }

        static function complete_order($id, $email_notify = true, $payment_method = null){
            
            global $wpdb;

            if(strpos($id, "renew")){
                $id = explode("_", $id);
                $id = $id[0];
            }

            $order_det = $wpdb->get_row("select * from {$wpdb->prefix}ahm_orders where order_id='$id'");
            $buyer_email = array();
            $billing_info = unserialize($order_det->billing_info);
            $buyer_email = isset($billing_info['order_email'])?$billing_info['order_email']:'';
            $name = "";

            $settings = get_option('_wpdmpp_settings');
            $guest_checkout = (isset($settings['guest_checkout']) && $settings['guest_checkout']==1)?1:0;
            $logo = isset($settings['logo_url'])&&$settings['logo_url']!=""?"<img src='{$settings['logo_url']}' alt='".get_bloginfo('name')."'/>":get_bloginfo('name');

            $expire_date = get_wpdmpp_option('order_validity_period', 0) > 0? strtotime("+".get_wpdmpp_option('order_validity_period', 0)." days"): 0;

            self::Update(array('order_status'=>'Completed','payment_status'=>'Completed','expire_date' => $expire_date), $id);

            if($order_det->order_status=='Expired'){
                $t = time();
                Order::add_note($id, $t, 'renews');
                Order::add_note($id, array('note'=>'Order Renewed Successfully <a onclick="window.open(\'?id='.$id.'&wpdminvoice=1&renew='.$t.'\',\'Invoice\',\'height=720, width = 750, toolbar=0\'); return false;" href="#" class="btn-invoice">Get Invoice</a>.','by'=>'Customer'));
            } else {
                Order::add_note($id, array('note'=>'Order Status: Completed / Payment Status: Completed / Paid with: '.$order_det->payment_method,'by'=>'Customer'));
            }

            //return if email notification set to false
            if($email_notify==false) return;

            // send email notifications
            $siteurl=home_url("/");           
            $from=home_url("/");

            $userid = $order_det->uid;

            if($userid && $buyer_email ==''){
            $user_info = get_userdata($userid);
            $name = $user_info->user_login;
            $buyer_email = $user_info->user_email;
            }

            $admin_email=get_bloginfo("admin_email");
            $email = array();
            $subject= "Thanks For Your Purchase";
            //$message="An order is made to {$siteurl}<br/> Order Id: ".$id."<br/>Customer Name: ".$user_info->display_name."<br/>Customer Email: ".$user_info->user_email;

            if($guest_checkout == 1)
                $message = file_get_contents(WPDMPP_BASE_DIR.'templates/email-templates/wpdm-pp-new-order-guest.html');
            else
                $message = file_get_contents(WPDMPP_BASE_DIR.'templates/email-templates/wpdm-pp-new-order-guest.html');

            $email['subject'] = $subject;
            $email['body'] = $message;
            $email['headers'] = 'From:  "'.get_bloginfo('name').'" <'.$admin_email.'>' . "\r\nContent-type: text/html\r\n";

            $params = array(
                '[#date#]' => date(get_option('date_format'),time()),
                '[#homeurl#]' => home_url('/'),
                '[#sitename#]' => get_bloginfo('name'),
                '[#order_link#]' => "<a href='".wpdmpp_orders_page('id='.$id)."'>".wpdmpp_orders_page('id='.$id)."</a>",
                '[#register_link#]' => "<a href='".wpdmpp_orders_page('orderid='.$id)."'>".wpdmpp_orders_page('orderid='.$id)."</a>",
                '[#name#]' => $name,
                '[#order_id#]' => $id,
                '[#logo#]' => $logo
            );

            $email['body'] = str_replace(array_keys($params), array_values($params), $email['body']);

            $email = apply_filters("order_confirmation_email_buyer", $email, $id);

            // to buyer
            wp_mail($buyer_email,$email['subject'],$email['body'],$email['headers']);
            // to admin     
            wp_mail($admin_email,"Copy: ".$email['subject'],$email['body'],$email['headers']);
            // to seller(s)
            $items = Order::GetOrderItems($id);
            foreach($items as $item){
                $product = get_post($item['pid']); 
                $udata = get_userdata($product->post_author); 
                $seller_emails[$product->post_author] = $udata->user_email;
                $product_by_seller[$product->post_author][] = "<a href='".get_permalink($product->ID)."'>{$product->post_title}</a>";
            }

            //confirm seller email content
            $subject="New Order Confirmation";
            $message="An order is made to {$siteurl}.\n OrderId is ".$id."<br/>Product List:<br/>[plist]<br/>Customer Name: ".$name."<br/>Customer Email: ".$buyer_email;

            $email['subject']=$subject;
            $email['body']=$message;
            $email['headers'] = 'From:  '.$admin_email.'' . "\r\nContent-type: text/html\r\n";
            $email = apply_filters("order_confirmation_email_seller", $email);    
            //send email
            if(is_array($seller_emails)) {
                foreach ($seller_emails as $seid => $seller_email) {
                    $prods = implode("<br/>", $product_by_seller[$seid]);
                    wp_mail($seller_email, $email['subject'], str_replace("[plist]", $prods, $email['body']), $email['headers']);
                }
            }

            do_action('wpdmpp_complete_order', $id);

        }

        static function CancelOrder($id){
            self::Update(array('order_status'=>'Cancelled','payment_status'=>'Cancelled'), $id);
        }

        static function UpdateOrderItems($cart_data, $id){
            global $wpdb;
            $cart_data = maybe_unserialize($cart_data);
            $wpdb->query("delete from {$wpdb->prefix}ahm_order_items where oid='$id'");
            if(!empty($cart_data))
                foreach($cart_data as $pid=>$cdt){
                    $variation = get_post_meta($pid,"__wpdm_variation",true);
                    $vrts = array();
                    $coupon = isset($cdt['coupon']) ? $cdt['coupon'] : '';
                    if(!isset($cdt['multi']) || $cdt['multi'] == 0) {
                        if (is_array($variation)) {
                            foreach ($variation as $key => $value) {
                                foreach ($value as $optionkey => $optionvalue) {
                                    if ($optionkey != "vname") {
                                        if (isset($cdt['variation']) && is_array($cdt['variation'])) {
                                            //echo "adfadf";
                                            foreach ($cdt['variation'] as $var) {
                                                //echo  $optionkey;
                                                if ($var == $optionkey) {
                                                    $vrts[$optionkey] = array('name' => $optionvalue['option_name'], 'price' => $optionvalue['option_price']);

                                                }
                                            }


                                        }
                                    }
                                }
                            }
                        }
                        $coupon_amount = isset($cdt['coupon_amount']) ? $cdt['coupon_amount'] : 0;
                        $wpdb->insert("{$wpdb->prefix}ahm_order_items", array('oid' => $id, 'pid' => $pid, 'quantity' => $cdt['quantity'], 'price' => $cdt['price'], 'variations' => serialize($vrts), 'coupon' => $coupon, 'coupon_discount' => floatval($coupon_amount)));
                    } else{
                        foreach($cdt['item'] as $mcdt) {
                            $vrts = array();
                            $quantity = $mcdt['quantity'];
                            $role_discount = $mcdt['discount_amount'];
                            $coupon_amount = $mcdt['coupon_amount'];
                            if (is_array($variation)) {
                                foreach ($variation as $key => $value) {
                                    foreach ($value as $optionkey => $optionvalue) {
                                        if ($optionkey != "vname") {
                                            if (isset($mcdt['variation']) && is_array($mcdt['variation'])) {
                                                //echo "adfadf";
                                                foreach ($mcdt['variation'] as $var) {
                                                    //echo  $optionkey;
                                                    if ($var == $optionkey) {
                                                        $vrts[$optionkey] = array('name' => $optionvalue['option_name'], 'price' => $optionvalue['option_price']);

                                                    }
                                                }


                                            }
                                        }
                                    }
                                }
                            }
                            $coupon = isset($coupon) ? $coupon : '';
                            $coupon_amount = isset($coupon_amount) ? $coupon_amount : 0;
                            $wpdb->insert("{$wpdb->prefix}ahm_order_items", array('oid' => $id, 'pid' => $pid, 'quantity' => $quantity, 'price' => $cdt['price'], 'variations' => serialize($vrts), 'coupon' => $coupon, 'coupon_discount' => floatval($coupon_amount), 'role_discount' => floatval($role_discount)));
                        }
                    }
            }
        }

        static function GetOrderItems($id){
            global $wpdb;
            $items = $wpdb->get_results("select * from {$wpdb->prefix}ahm_order_items where oid='{$id}'",ARRAY_A);
            return is_array($items)?$items:array();
        }



        function CalcOrderTotal($oid){
            global $wpdb;
            global $current_user;
            
            $role = is_user_logged_in()?$current_user->roles[0]:'guest';

            $total = 0;
            $orderdata = $this->GetOrder($oid);
             
            $cart_items = unserialize($orderdata->cart_data);
            $discount1= 0;
            if(is_array($cart_items)){

                foreach($cart_items as $pid=>$item)    {
                    $prices=0;

                    $variation = get_post_meta($pid,'__wpdm_variation', true);
                    if(isset($item['variation']) && is_array($item['variation']) && is_array($variation)){
                        foreach($variation as $key=>$value){
                            foreach($value as $optionkey=>$optionvalue){
                                if($optionkey!="vname"){
                                    foreach($item['variation'] as $var){                   
                                        if($var==$optionkey){
                                            $prices+=$optionvalue['option_price'];
                                        }
                                    }    
                                }
                            }
                        }     
                    }
                    if(isset($item['coupon']) && trim($item['coupon'])!=''){
                        $valid_coupon=check_coupon($pid,$item['coupon']);

                        if($valid_coupon!=0){
                            $total +=  (($item['price']+$prices)*$item['quantity'])-(($item['price']+$prices)*$item['quantity']*($valid_coupon/100));
                        } else {
                            $total +=  (($item['price']+$prices)*$item['quantity']);
                        }
                    }else {
                        $total +=  (($item['price']+$prices)*$item['quantity']);

                    }

                    //calculate discount
                    $discount = maybe_unserialize(get_post_meta($pid,'__wpdm_discount', true));
                    $discount[$role] = isset($discount[$role])?$discount[$role]:0;
                    $discount1 += (($item['price']*$item['quantity']*$discount[$role])/100);
            }}

            $total = apply_filters('wpdmpp_cart_subtotal',$total);

            $subtotal=$total;


            $tax_summery=$this->wpdmpp_calculate_tax();

            $tax = 0;
            if(count($tax_summery)>0){
                foreach($tax_summery as $taxrow){
                    $tax += $taxrow['rates'];
                }
            }
            $total += $tax;


            $total = $total-$discount1;

            return $total;
        }

        function wpdmpp_calculate_tax($oid = null){
            $taxr = array();
            $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
            $tax_summery = array();
            if(isset($_SESSION['orderid'])) $order_info=$this->GetOrder($_SESSION['orderid']);
            if($oid) $order_info = $this->GetOrder($oid);
            $bdata = unserialize($order_info->billing_info);
            if(isset($_SESSION['orderid'])) $cart_items = $this->GetOrderItems($_SESSION['orderid']);

            if(isset($settings['tax']['enable']) && $settings['tax']['enable']==1){
                if(is_array($cart_items)){
                    foreach($cart_items as $item){
                        $taxes = 0;
                        $tax_status = "";
                        $tax_class = "";
                        $tax_status = get_post_meta($item['pid'], '__wpdm_taxable', true);

                        if($tax_status=="taxable"){

                            if($settings['tax']['tax_rate']){
                                $temp_class = "";
                                $temp_label = "";
                                $taxes = 0;
                                foreach($settings['tax']['tax_rate'] as $key=> $rate){

                                    if($rate['tax_class']==$tax_class){
                                        $taxes = 0;
                                        if(in_array($bdata['shippingin']['country'], $rate['country'])){
                                            if($sales_price) $base_price = $sales_price;
                                            $taxes = (($rate['rate']*$base_price)/100);
                                            if($rate['shipping']==1){
                                                $taxes += (($rate['rate']*$order_info->shipping_cost)/100);
                                            }
                                            //product wise tax
                                            $taxr['label'][$item['pid']][]= $rate['label'];
                                            $taxr['rate'][$item['pid']]+= $taxes;     
                                            //class wise tax
                                            $tax_summery[$key]['label'] = $rate['label'];
                                            $tax_summery[$key]['rates'] += $taxes;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $tax_summery;
        }

        function Load(){

        }

        function GetOrder($id) {
            global $wpdb;
            if(strpos($id, "renew")){
                $id = explode("_", $id);
                $id = $id[0];
            }
            $id = esc_attr($id);
            return $wpdb->get_row("select * from {$wpdb->prefix}ahm_orders where order_id='$id' or trans_id='$id'");
        }  

        function GetOrders($id) {
            global $wpdb;
            return $wpdb->get_results("select * from {$wpdb->prefix}ahm_orders where uid='$id' order by `date` desc");
        }

        function GetAllOrders($qry="",$s=0, $l=20) {
            global $wpdb;
            return $wpdb->get_results("select * from {$wpdb->prefix}ahm_orders $qry order by `date` desc limit $s,$l");
        }

        public static function getPurchasedItems(){
            global $wpdb, $current_user;
            $uid = $current_user->ID;
            $purchased_items = $wpdb->get_results("select p.post_title,oi.*,o.date, o.order_status from {$wpdb->prefix}ahm_order_items oi,{$wpdb->prefix}ahm_orders o,{$wpdb->prefix}posts p where oi.pid = p.ID and o.order_id = oi.oid and o.uid = {$uid} and o.order_status IN ('Expired', 'Completed') order by `date` desc");
            foreach($purchased_items as &$item){
                $files = get_post_meta($item->pid, '__wpdm_files', true);
                foreach ($files as $index) {
                    $item->download_url[$index] = home_url("/?wpdmdl={$item->pid}&oid={$item->oid}&ind=" . WPDM_Crypt::Encrypt($index));
                }
            }
            return $purchased_items;
        }

        function totalOrders($qry=''){
            global $wpdb;
            return $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_orders $qry");
        }

        function Delete($id){
            global $wpdb;
            return $wpdb->query("delete from {$wpdb->prefix}ahm_orders where order_id='$id'");
        }


}