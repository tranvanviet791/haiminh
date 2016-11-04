<?php
/**
 * Plugin Name:  WPDM - Premium Packages
 * Plugin URI: http://www.wpdownloadmanager.com/download/premium-package-complete-digital-store-solution/
 * Description: Complete solution for selling digital products
 * Author: Shaon
 * Version: 3.5.7
 * Text Domain: wpdm-premium-package
 * Author URI: http://www.wpdownloadmanager.com/
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('WPDMPremiumPackage')):
	/**
	 * Main Premium Package Class
	 *
	 * @class WPDMPremiumPackage
	 */

	class WPDMPremiumPackage
	{

		function __construct()
		{
			$this->init();
			$this->init_hooks();
		}

		private function init()
		{
			if( ! isset( $_SESSION ) ) session_start();

			global $sap; // Seperator
			if ( function_exists( 'get_option' ) ) {
			    $sap = ( get_option( 'permalink_structure' ) != '') ? '?' : '&';
			}

			define('WPDMPP_Version', '3.5.2');
			define('WPDMPP_BASE_DIR', dirname(__FILE__).'/');
			define('WPDMPP_BASE_URL', plugins_url('wpdm-premium-packages/'));
			define('WPDMPP_MENU_ACCESS_CAP', 'manage_categories');
			define('WPDMPP_ADMIN_CAP', 'manage_categories');

			$this->include_files();
			$this->wpdmpp_shortcodes();
		}

		private function init_hooks()
		{
		    register_activation_hook( __FILE__, array( 'InstallWPDMPP', 'wpdmpp_install' ) );

		    add_action( 'wpdm-package-form-left', array( $this, 'wpdmpp_meta_box_pricing' ) );
			add_filter( 'wpdm_package_settings_tabs', array( $this, 'wpdmpp_meta_boxes' ) );
            add_filter( 'add_wpdm_settings_tab', array( $this, 'wpdmpp_settings_tab' ) );
			add_action( 'save_post', array( $this, 'wpdmpp_save_meta_data' ), 10, 2);
			add_action( 'wpdm_template_editor_menu', array( $this, 'template_editor_menu' ));

			add_action( 'init', array( $this, 'wpdmpp_languages' ) );
			add_action( 'init', array( $this, 'wpdmpp_invoice' ) );
			add_action( 'init', array( $this, 'wpdmpp_process_guest_order' ) );
			add_action( 'init', array( $this, 'wpdmpp_download' ), 0);
			add_action( 'init', array( $this, 'wpdmpp_paynow' ) );
			add_action( 'init', array( $this, 'wpdmpp_payment_notification' ) );
			add_action( 'init', array( $this, 'wpdmpp_withdraw_paypal_notification' ) );
			add_action( 'init', array( $this, 'wpdmpp_ajax_payfront' ) );
			add_action( 'init', array( $this, 'wpdmpp_execute' ) );
			add_action( 'init', array( $this, 'wpdmpp_update_profile' ) );

			add_action( 'wpdm_login_form', array( $this, 'wpdmpp_invoide_field' ) );
            add_action( 'wpdm_register_form', array( $this, 'wpdmpp_invoide_field' ) );
            add_action( 'wp_login', array( $this, 'wpdmpp_associate_invoice' ), 10, 2 );
            add_action( 'user_register', array( $this, 'wpdmpp_associate_invoice_signup' ), 10, 1 );
            add_action( 'wp_ajax_resolveorder', array( $this, 'wpdmpp_resolveorder' ) );

            add_action( 'wp_ajax_nopriv_gettax', array( $this, 'calculate_tax' ) );
            add_action( 'wp_ajax_gettax', array( $this, 'calculate_tax' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'wpdmpp_enqueue_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'wpdmpp_enqueue_scripts' ) );

			if ( is_admin() ) {
				add_action( 'admin_menu', array( $this, 'wpdmpp_menu' ) );
				add_action( 'wp_ajax_wpdmpp_save_settings', array( $this, 'wpdmpp_save_settings' ) );
				add_action( 'wp_ajax_wpdmpp_ajax_call', array( $this, 'wpdmpp_ajax_call' ) );
			}

			if( ! is_admin() ) {
                add_action( 'init', array( $this, 'wpdmpp_execute' ) );
            }

			add_filter( 'wpdm_user_dashboard_menu', array( $this, 'wpdmpp_user_dashboard_menu' ) );
			add_filter( 'wpdm_frontend', array( $this, 'wpdmpp_frontend_tabs' ) );
			add_filter( 'wpdm_after_prepare_package_data', array( $this, 'fetch_template_tag' ) );
            add_filter( 'wdm_before_fetch_template', array( $this, 'fetch_template_tag' ) );
            add_filter( 'wpdm_check_lock', array( $this, 'wpdmpp_lock_download' ), 10, 2 );
            add_filter( 'wpdm_single_file_download_link', array( $this, 'hide_single_file_download_link' ), 10, 3 );
		}

		function wpdmpp_languages()
		{
			load_plugin_textdomain('wpdm-premium-package', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		}

		function include_files()
		{
			include(dirname(__FILE__) . "/includes/libs/class.InstallWPDMPP.php");
			include(dirname(__FILE__) . "/includes/libs/class.LicenseManager.php");
			include(dirname(__FILE__) . "/includes/libs/class.Order.php");
			include(dirname(__FILE__) . "/includes/libs/class.Payment.php");
			include(dirname(__FILE__) . "/includes/libs/class.CustomActions.php");
			include(dirname(__FILE__) . "/includes/libs/class.CustomColumns.php");
			include(dirname(__FILE__) . "/includes/libs/class.Currencies.php");
			include(dirname(__FILE__) . "/includes/libs/class.BillingInfo.php");
			include(dirname(__FILE__) . "/includes/libs/functions.php");
			include(dirname(__FILE__) . "/includes/libs/cart.php");
			include(dirname(__FILE__) . "/includes/libs/hooks.php");

			include(dirname(__FILE__) . "/includes/widget-cart.php");

			/**
			 * Auto load default payment mothods
			 */
			global $payment_methods, $wpdmpp_settings;
			$pdir = WPDMPP_BASE_DIR . "includes/libs/payment-methods/";
			$methods = scandir($pdir, 1);

			foreach ($methods as $method) {
				if (!strpos("_".$method, '.')) {
				$path = realpath($pdir . $method . "/class.{$method}.php");
					if (file_exists($path)) {
						$payment_methods[] = $method;
						include_once($path);
					}
				}
			}

			$wpdmpp_settings = maybe_unserialize(get_option('_wpdmpp_settings'));
		}
		
		function calculate_tax(){
		    $cartsubtotal = wpdmpp_get_cart_subtotal();
		    $tax_total = wpdmpp_calculate_tax2();
		    $total_including_tax = $cartsubtotal + $tax_total;

		    $currency_sign = wpdmpp_currency_sign();
            $tax_str = $currency_sign.number_format((double)str_replace(',','',$tax_total),2);
            $total_str = $currency_sign.number_format((double)str_replace(',','',$total_including_tax),2);

            $updates = array( 'tax' => $tax_str, 'total' => $total_str );

            $_SESSION['tax'] = $tax_total;
            $_SESSION['subtotal'] = $cartsubtotal;

            die( json_encode($updates) );
		}


		/**
		 * Metabox content for Pricing and other Premium Pckage Settings
		 */
		function wpdmpp_meta_box_pricing()
		{
			global $post;
			include(dirname(__FILE__) . '/templates/metaboxes/wpdm-pp-settings.php');
		}

		/**
		 * @param $tabs
		 * @return mixed
		 * @usage Adding Premium Package Settings Metabox by applying WPDM's 'wpdm_package_settings_tabs' filter
		 */
		function wpdmpp_meta_boxes($tabs)
		{
			if(is_admin())
				$tabs['pricing'] = array('name' => __('Pricing & Discounts', "wpdm-premium-package"), 'callback' => array( $this, 'wpdmpp_meta_box_pricing' ) );

			return $tabs;
		}

		/**
		 * @param $postid
		 * @param $post
		 * @usage
		 */
		function wpdmpp_save_meta_data($postid, $post)
		{
			if (isset($_POST['post_author'])) {
				$userinfo = get_userdata($_POST['post_author']);

				if ($userinfo->roles[0] != "administrator") {
					if ($_POST['original_post_status'] == "draft" && $_POST['post_status'] == "publish") {
						global $current_user;
						$siteurl = home_url("/");
						$admin_email = get_bloginfo("admin_email");
						$to = $userinfo->user_email; //post author
						$from = $current_user->user_email;
						$link = get_permalink($post->ID);

						$subject = "Product Approved!";
						$message = "Your product {$post->post_title} {$link} is approved to {$siteurl} ";
						$email['subject'] = $subject;
						$email['body'] = $message;
						$email['headers'] = 'From:  <' . $from . '>' . "\r\n";
						$email = apply_filters("product_approval_email", $email);
						wp_mail($to, $email['subject'], $email['body'], $email['headers']);
					}
				}
			}
		}

		/**
		 *  Premium Package Settings Page
		 */
		function wpdmpp_settings()
		{
			include("includes/settings/settings.php");
		}

		function wpdmpp_settings_tab($tabs){
			$tabs['ppsettings'] = wpdm_create_settings_tab('ppsettings', 'Premium Package', array( $this, 'wpdmpp_settings' ), $icon = 'fa fa-shopping-cart');
			return $tabs;
		}

		/**
		 * Generate Order Invoice op request
		 */
		function wpdmpp_invoice()
		{
			if (isset($_GET['id']) && $_GET['id'] != '' && isset($_GET['wpdminvoice'])) {
				include(WPDMPP_BASE_DIR . 'templates/wpdmpp-invoice.php');
				die();
			}
		}

		/**
		 * Menu for the Premium Package
		 */
		function wpdmpp_menu()
		{
			add_submenu_page('edit.php?post_type=wpdmpro', __('Payouts', "wpdm-premium-package"), __('Payouts', "wpdm-premium-package"), WPDMPP_MENU_ACCESS_CAP, 'payouts', array( $this, 'wpdmpp_all_payouts' ) );
			add_submenu_page('edit.php?post_type=wpdmpro', __('Orders &lsaquo; Marketplace', "wpdm-premium-package"), __('Orders', "wpdm-premium-package"), WPDMPP_MENU_ACCESS_CAP, 'orders', array( $this, 'wpdmpp_orders' ) );
			add_submenu_page('edit.php?post_type=wpdmpro', __('License Manager', "wpdm-premium-package"), __('License Manager', "wpdm-premium-package"), WPDMPP_MENU_ACCESS_CAP, 'pp-license', array( $this, 'wpdmpp_license' ) );
		}

		/**
		 * All Orders list
		 */
		function wpdmpp_orders()
		{
			if(!current_user_can(WPDMPP_MENU_ACCESS_CAP)) return;
			$order1 = new Order();
			global $wpdb;
			$l = 15;
			$currency_sign = get_option('_wpdmpp_curr_sign', '$');
			$p = isset($_GET['paged']) ? $_GET['paged'] : 1;
			$s = ($p - 1) * $l;

			if (isset($_GET['task']) && $_GET['task'] == 'vieworder') {
				$order = $order1->getOrder($_GET['id']);
				include('templates/view-order.php');
			} else {
				if (isset($_GET['task']) && $_GET['task'] == 'delete_order') {
					$order_id = esc_attr($_GET['id']);
					$ret = $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_orders WHERE order_id = %s", $order_id ));

					if ($ret) {
						$ret = $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_order_items WHERE oid = %s", $order_id ));
						if ($ret) $msg = "Record Deleted for Order ID $order_id...";
					}

				} else if (isset($_GET['delete_selected'], $_GET['delete_confirm']) && $_GET['delete_confirm'] == 1) {
					$order_ids = $_GET['id'];

					if (!empty($order_ids) && is_array($order_ids)) {
						foreach ($order_ids as $key => $order_id) {
							$order_id = esc_attr($order_id);
							$ret = $wpdb->query(
								$wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_orders WHERE order_id = %s", $order_id));
							if ($ret) {
								$ret = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_order_items WHERE oid = %s",$order_id ));

								if ($ret) $msg[] = "Record Deleted for Order ID $order_id...";
							}
						}
					}
				} else if (isset($_GET['delete_by_payment_sts'], $_GET['delete_all_by_payment_sts']) && $_GET['delete_all_by_payment_sts'] != "") {
					$payment_status = esc_attr($_GET['delete_all_by_payment_sts']);

					$order_ids = $wpdb->get_results(
						"SELECT order_id
								FROM {$wpdb->prefix}ahm_orders
								WHERE payment_status = '$payment_status'"
						, ARRAY_A);
					if ($order_ids) {
						foreach ($order_ids as $row) {
							$order_id = $row['order_id'];
							$ret = $wpdb->query(
								$wpdb->prepare(
									"DELETE FROM {$wpdb->prefix}ahm_orders
							 WHERE order_id = %s",
									$order_id
								)
							);
							if ($ret) {

								$ret = $wpdb->query(
									$wpdb->prepare(
										"DELETE FROM {$wpdb->prefix}ahm_order_items
								 WHERE oid = %s",
										$order_id
									)
								);

								if ($ret) $msg[] = "Record Deleted for Order ID $order_id...";
							}
						}
					}
				}


				if (isset($_REQUEST['oid']) && $_REQUEST['oid'])
					$qry[] = "order_id='$_REQUEST[oid]'";
				if (isset($_REQUEST['customer']) && intval($_REQUEST['customer'])>0)
					$qry[] = "uid='$_REQUEST[customer]'";
				if (isset($_REQUEST['ost']) && $_REQUEST['ost'])
					$qry[] = "order_status='$_REQUEST[ost]'";
				if (isset($_REQUEST['pst']) && $_REQUEST['pst'])
					$qry[] = "payment_status='$_REQUEST[pst]'";
				if (isset($_REQUEST['sdate'], $_REQUEST['edate']) && ($_REQUEST['sdate'] != '' || $_REQUEST['edate'] != '')) {
					$_REQUEST['edate'] = $_REQUEST['edate'] ? $_REQUEST['edate'] : $_REQUEST['sdate'];
					$_REQUEST['sdate'] = $_REQUEST['sdate'] ? $_REQUEST['sdate'] : $_REQUEST['edate'];
					$sdate = strtotime("$_REQUEST[sdate] 00:00:00");
					$edate = strtotime("$_REQUEST[edate] 23:59:59");
					$qry[] = "(`date` >=$sdate and `date` <=$edate)";
				}

				if (isset($qry))
					$qry = "where " . implode(" and ", $qry);
				else $qry = "";
				$t = $order1->totalOrders($qry);
				$orders = $order1->GetAllOrders($qry, $s, $l);
				include('templates/orders.php');
			}
		}

		/**
		 * payouts section
		 */
		function wpdmpp_all_payouts()
		{
			include_once("templates/payouts.php");
		}


		function wpdmpp_license()
		{
			global $wpdb;
			$l = 15;
			$p = isset($_GET['paged']) ? $_GET['paged'] : 1;
			$s = ($p - 1) * $l;

			if (isset($_GET['task']) && $_GET['task'] == 'editlicense') {
				$lid = intval($_GET['id']);
				$license = $wpdb->get_row("select * from {$wpdb->prefix}ahm_licenses where id='{$lid}'");
				include('templates/edit-license.php');
			} else {
				$qry = array();
				if (isset($_REQUEST['licenseno']))
					$qry[] = "licenseno='$_REQUEST[licenseno]'";
				if (isset($_REQUEST['oid']))
					$qry[] = "oid='$_REQUEST[oid]'";
				if (isset($_REQUEST['pid']))
					$qry[] = "pid='$_REQUEST[pid]'";
				if (count($qry) > 0)
					$qry = "and " . implode(" and ", $qry);
				else $qry = "";

				$t = $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_licenses where 1 $qry");
				$licenses = $wpdb->get_results("select l.*,f.post_title as productname from {$wpdb->prefix}ahm_licenses l,{$wpdb->prefix}posts f where l.pid=f.ID $qry limit $s, $l");

				include("templates/manage-license.php");
			}
		}

        /**
         * Shortcodes
         */
		function wpdmpp_shortcodes()
		{
			add_shortcode( 'wpdm-pp-purchases', array( $this, 'wpdmpp_user_purchases' ) );
			add_shortcode( 'wpdm-pp-guest-orders', array( $this, 'wpdmpp_guest_orders' ) );
			add_shortcode( 'wpdm-pp-earnings', array( $this, 'wpdmpp_earnings' ) );
			add_shortcode( 'wpdm-pp-edit-profile' , array( $this, 'wpdmpp_edit_profile' ) );
		}

		/**
		 * Frontend user profile
		 * @return string
		 */
		function wpdmpp_user_purchases()
		{
			global $current_user, $_ohtml;

			$_ohtml = '';
			$dashboard = true;
			$wpdmpp_settings = get_option('_wpdmpp_settings');

			ob_start();
			?>
			<div class="w3eden">
			<?php
			if( ! is_user_logged_in() ) {

				// Guest User
				include(wpdm_tpl_path('wpdm-be-member.php'));
				?>
				<?php if( isset($_SESSION['last_order']) && $_SESSION['last_order'] != '' && isset($wpdmpp_settings['guest_download']) && $wpdmpp_settings['guest_download'] == 1){ ?>
				<div class="panel panel-info" style="width: 350px;max-width: 98%;margin: 50px auto">
					<div class="panel-heading"><?php _e('Guest Order','wpdmpp'); ?></div>
					<div class="panel-body">
					We strongly recommend your to signup/login to get access to your order and product support.
					But, if you don't want to signup now, please go to <a class="label label-primary" href="<?php echo wpdmpp_guest_order_page("orderid=".$_SESSION['last_order']); ?>">Guest Order</a> page
					</div>
				</div>
				<?php }
			}
			else {

			// Logged In User

			$order = new Order();
			$myorders = $order->GetOrders($current_user->ID);

			include('templates/orders-purchases.php');
			echo $_ohtml;

			}
			echo '</div>';
			$tabs = ob_get_clean();

			return $tabs;
		}

		function wpdmpp_user_dashboard_menu($menu){
			$menu = array_merge(array_splice($menu, 0, 1), array('purchases' => array('name' => 'Purchases', 'callback' => array( $this, 'wpdmpp_purchased_items' ) ) ), $menu);
			return $menu;
		}

		function wpdmpp_purchased_items($params = array()){
			global $wpdb, $current_user;
			$uid = $current_user->ID;
			$purchased_items = $wpdb->get_results("select oi.*,o.date, o.order_status from {$wpdb->prefix}ahm_order_items oi,{$wpdb->prefix}ahm_orders o where o.order_id = oi.oid and o.uid = {$uid} and o.order_status IN ('Expired', 'Completed') order by `date` desc");
			ob_start();
			if(isset($params[2]) && $params[1] == 'order')
			include wpdm_tpl_path('order-details.php', WPDMPP_BASE_DIR.'/templates/');
			else if(isset($params[1]) && $params[1] == 'orders')
			include wpdm_tpl_path('purchase-orders.php', WPDMPP_BASE_DIR.'/templates/');
			else
			include wpdm_tpl_path('purchased-items.php', WPDMPP_BASE_DIR.'/templates/');
			return ob_get_clean();
		}

		function wpdmpp_guest_orders(){
			ob_start();
			global $post;
			if(is_object($post) && get_the_permalink() == wpdmpp_guest_order_page() && !isset($_SESSION['guest_order_init'])) $_SESSION['guest_order_init'] = uniqid();
			include dirname(__FILE__).'/templates/guest-orders.php';
			return ob_get_clean();
		}

		/**
		 * Process Guest Orders
		 */
		function wpdmpp_process_guest_order(){


			if(isset($_POST['go'])) {

				if(!isset($_SESSION['guest_order_init'])) { $_SESSION['guest_order_init'] = uniqid(); die('nosess'); }

				$orderid = $_POST['go']['order'];
				$orderemail = $_POST['go']['email'];

				$o = new Order();
				$order = $o->GetOrder($orderid);
                $billing_info = unserialize($order->billing_info);
                $billing_email = isset($billing_info['order_email'])?$billing_info['order_email']:'';
				if( ! is_object($order) || ! isset($order->order_id) || $order->order_id != $orderid) die('noordr');

				if(is_email($orderemail) && $orderemail == $billing_email && $order->uid <=0){
					$_SESSION['guest_order'] = $orderid;
					die('success');
				}

				if($order->uid > 0) die('nogues');

				die('noordr');
			}

		}

		function wpdmpp_frontend_tabs($tabs){
			$tabs['sales'] = array('label'=>'Sales','shortcode' => '[wpdm-pp-earnings]');
			return $tabs;
		}

		/**
		 * Save admin settings options
		 */
		function wpdmpp_save_settings()
		{
			update_option('_wpdmpp_settings', $_POST['_wpdmpp_settings']);
			die(__('Settings Saved Successfully', "wpdm-premium-package"));
		}

		function wpdmpp_download()
		{
			if ( ! isset($_GET['wpdmdl']) || ! isset($_GET['oid']) ) return;

			if(wpdm_query_var('preact') == 'login'){
				$user = wp_signon(array('user_login' => wpdm_query_var('user'), 'user_password' => wpdm_query_var('pass') ));
				if(!$user->ID)
				die('Error!');
				else
				wp_set_current_user($user->ID);
			}

			global $wpdb, $current_user;
			$settings = get_option('_wpdmpp_settings');

			$order = new Order();
			$odata = $order->GetOrder($_GET['oid']);
			$items = unserialize($odata->items);

			if($odata->uid != $current_user->ID) wp_die(__("Dailing 911! You better run now!!","wpdm-premium-package"));
			if($odata->order_status == 'Expired') wp_die(__("Sorry! Support and Update Access Period is Already Expired","wpdm-premium-package"));

			$base_price = get_post_meta($_GET['wpdmdl'], '__wpdm_base_price', true);

			$package = get_post($_GET['wpdmdl'], ARRAY_A);
			$package['files'] = maybe_unserialize(get_post_meta($package['ID'], '__wpdm_files', true));
			$package['individual_file_download'] = maybe_unserialize(get_post_meta($package['ID'], '__wpdm_individual_file_download', true));

			if ($base_price == 0 && (int)$_GET['wpdmdl'] > 0) {
				//for free items
				include(WPDM_BASE_DIR . "/wpdm-start-download.php");
			}
			if (@in_array($_GET['wpdmdl'], $items) && $_GET['oid'] != '' && is_user_logged_in() && $current_user->ID == $odata->uid && $odata->order_status == 'Completed') {
				//for premium item
				include(WPDM_BASE_DIR . "/wpdm-start-download.php");
			}

			if (@in_array($_GET['wpdmdl'], $items)
				&& isset($_GET['oid'])
				&& $_GET['oid'] != ''
				&& !is_user_logged_in()
				&& $odata->uid == 0
				&& $odata->order_status == 'Completed'
				&& isset($settings['guest_download'])
				&& isset($_SESSION['guest_order'])) {
					//for guest download
					include(WPDM_BASE_DIR . "/wpdm-start-download.php");

			}

		}

		/**
		 * Create new Order
		 */
		function create_order()
		{
			global $current_user;

			if(floatval(wpdmpp_get_cart_total()) <=0 ) return;

			$order = new Order();
			if (isset($_SESSION['orderid']) && $_SESSION['orderid'] != '') {
				$order_info = $order->GetOrder($_SESSION['orderid']);
				if ($order_info->order_id) {
					$data = array(
						'cart_data' => serialize(wpdmpp_get_cart_data()),
						'items' => serialize(array_keys(wpdmpp_get_cart_data()))
					);
					$order->UpdateOrderItems(wpdmpp_get_cart_data(), $_SESSION['orderid']);
					$insertid = $order->Update($data, $_SESSION['orderid']);
				} else {
					$cart_data = serialize(wpdmpp_get_cart_data());
					$items = serialize(array_keys(wpdmpp_get_cart_data()));
					$order->NewOrder($_SESSION['orderid'], "", $items, 0, $current_user->ID, 'Processing', 'Processing', $cart_data);
					$order->UpdateOrderItems($cart_data, $_SESSION['orderid']);
				}
			} else {
				$cart_data = serialize(wpdmpp_get_cart_data());
				$items = serialize(array_keys(wpdmpp_get_cart_data()));
				$insertid = $order->NewOrder(uniqid(), "", $items, 0, $current_user->ID, 'Processing', 'Processing', $cart_data);
				$order->UpdateOrderItems($cart_data, $_SESSION['orderid']);
			}
		}

		/**
		 * Saving payment method info from checkout process
		 */
		function wpdmpp_paynow()
		{
			if (isset($_REQUEST['task']) && $_REQUEST['task'] == "paynow") {

				if(floatval(wpdmpp_get_cart_total()) <= 0 ) die('Empty Cart!');

				global $current_user;

				$this->create_order();

				$data = array(
					'payment_method' => $_POST['payment_method'],
					'billing_info' => serialize($_POST['billing'])
				);

				$order = new Order();
				$od = $order->Update($data, $_SESSION['orderid']);

				if(is_user_logged_in()){
				    $billing_info = $_POST['billing'];
				    $billing_info['email'] = $_POST['billing']['order_email'];
    				$billing_info['phone'] = '';
    				$cb = get_user_meta($current_user->ID, 'user_billing_shipping', true);
    				if(!$cb)
				    update_user_meta($current_user->ID, 'user_billing_shipping', serialize(array('billing' => $billing_info)));;
				}

				$order_info = $order->GetOrder($_SESSION['orderid']);
				$this->wpdmpp_place_order();
				die();
			}
		}

		/**
		 * Placing order from checkout process
		 */
		function wpdmpp_place_order()
		{
			if(floatval(wpdmpp_get_cart_total()) <= 0 ) return;

			$order = new Order();
			$order_total = $order->CalcOrderTotal($_SESSION['orderid']);
			$tax = 0;

            if(wpdmpp_tax_active()){
                $tax = $_SESSION['tax'];
                $subtotal = $_SESSION['subtotal'];
                $order_total = $subtotal + $tax;
            }

			$data = array(
				'total' => $order_total,
				'order_notes' => '',
				'cart_discount' => 0,
				'tax' => $tax
			);
			$od = $order->Update($data, $_SESSION['orderid']);
			do_action("wpdm_before_placing_order", $_SESSION['orderid']);

			// If order total is not 0 then go to payment gateway
			if ($order_total > 0) {
				$payment = new Payment();
				$payment->InitiateProcessor($_POST['payment_method']);
				$payment->Processor->OrderTitle = 'Order# ' . $_SESSION['orderid'];
				$payment->Processor->InvoiceNo = $_SESSION['orderid'];
				$payment->Processor->Custom = $_SESSION['orderid'];
				$payment->Processor->Amount = number_format($order_total,2);
				echo $payment->Processor->ShowPaymentForm(1);
				if(!isset($payment->Processor->EmptyCartOnPlaceOrder) || $payment->Processor->EmptyCartOnPlaceOrder == true)
				wpdmpp_empty_cart();
				die();
			} else {
				// if order total is 0 then empty cart and redirect to home
				wpdmpp_empty_cart();
				wpdmpp_js_redirect(home_url('/'));
			}
		}

		/**
		 * Payment notification process
		 */
		function wpdmpp_payment_notification()
		{
			if (isset($_REQUEST['action']) && $_REQUEST['action'] == "wpdmpp-payment-notification") {
				$payment_method = new $_REQUEST['class']();

				if ($payment_method->VerifyNotification()) {
					global $wpdb;
					Order::complete_order($payment_method->InvoiceNo, true, $payment_method);
					do_action("wpdm_after_checkout",$payment_method->InvoiceNo);
					die('OK');
				}
				die("FAILED");
			}
		}

		/**
         * Withdraw money from paypal notification
         */
        function wpdmpp_withdraw_paypal_notification()
        {
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == "withdraw_paypal_notification" && current_user_can(WPDMPP_MENU_ACCESS_CAP)) {

                if (isset($_POST["txn_id"]) && isset($_POST["txn_type"]) && $_POST["status"] == "Completed") {
                    global $wpdb;
                    $wpdb->update(
                        "{$wpdb->prefix}ahm_withdraws",
                        array(
                            'status' => 1
                        ),
                        array('id' => $_POST['custom']),
                        array(
                            '%d'
                        ),
                        array('%d')
                    );
                }
            }
        }

        /**
         * Payment using ajax
         */
        function wpdmpp_ajax_payfront()
        {
            if (isset($_POST['task'], $_POST['action']) && $_POST['task'] == "paymentfront" && $_POST['action'] == "wpdmpp_ajax_call") {
                $data['order_id'] = $_POST['order_id'];
                $data['payment_method'] = $_POST['payment_method'];
                PayNow($data);
                die();
            }
        }

        /**
         * Dynamic function call using AJAX
         */
        function wpdmpp_ajax_call()
        {
            $CustomActions = new CustomActions();
            if (method_exists($CustomActions, $_POST['execute'])) {
                $method = esc_attr($_POST['execute']);
                echo $CustomActions->$method();
                die();
            } else
            die("Function doesn't exist");
        }

        /**
         * Execute Custom Action
         */
        function wpdmpp_execute()
        {
            $CustomActions = new CustomActions();
            if(isset($_POST['action']) && $_POST['action']=='wpdm_pp_ajax_call'){
                if (method_exists($CustomActions, $_POST['execute'])) {
                    $method = esc_attr($_POST['execute']);
                    echo $CustomActions->$method();
                    die();
                }
            }
        }

        /**
         * Function for earnings using shortcode
         */
        function wpdmpp_earnings()
        {
            include("templates/earnings.php");
        }

        /**
         * Edit Profile Shortcode Function
         */
        function wpdmpp_edit_profile()
        {
            include(dirname(__FILE__) . '/templates/edit-profile.php');
        }

        /**
         * Update User Profile
         */
        function wpdmpp_update_profile()
        {
            global $current_user;
            if (!is_user_logged_in() || !isset($_POST['profile'])) return;

            $userdata = $_POST['profile'];
            $userdata['ID'] = $current_user->ID;
            if ($_POST['password'] == $_POST['cpassword']) {
                wp_update_user($userdata);
                $userdata['user_pass'] = $_POST['password'];
                update_user_meta($current_user->ID, 'payment_account', $_POST['payment_account']);
                update_user_meta($current_user->ID, 'phone', $_POST['phone']);
                $_SESSION['member_success'] = __("Profile Updated Successfully", "wpdm-premium-package");

            } else {
                $_SESSION['member_error'][] = __("Confirm Password Not Matched. Profile Update Failed!", "wpdm-premium-package");
            }
            update_user_meta($current_user->ID, 'user_billing_shipping', serialize($_POST['checkout']));

            wpdmpp_redirect($_SERVER['HTTP_REFERER']);
            die();

        }

        /**
         * Load Scripts and Styles
         * @param $hook
         */
        function wpdmpp_enqueue_scripts($hook)
        {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-form');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-accordion');

            if( (is_admin() && get_query_var('post_type') == 'wpdmpro' ) || $hook == 'wpdmpro_page_settings'){
                wp_enqueue_script('wpdmpp-admin-js', WPDMPP_BASE_URL.'assets/js/wpdmpp-admin.js', array('jquery'));
            }

            if(is_admin() && wpdm_query_var('post_type') == 'wpdmpro' && in_array(wpdm_query_var('page'), array('settings','payouts','orders', 'pp-license'))){
                wp_enqueue_style('wpdm-bootstrap', plugins_url('/download-manager/assets/bootstrap/css/bootstrap.css'));
                wp_enqueue_script('wpdm-bootstrap', plugins_url('/download-manager/assets/bootstrap/js/bootstrap.min.js'), array('jquery'));
            }

            // Load Download Manager Scripts
            wp_enqueue_script('wpdm-jquery-validate', WPDM_BASE_URL.'assets/js/jquery.validate.min.js',  array('jquery'));
            wp_enqueue_script('wpdm-bootstrap-select', WPDM_BASE_URL.'assets/js/bootstrap-select.min.js',  array('jquery', 'wpdm-bootstrap'));
            wp_enqueue_style('wpdm-bootstrap-select', WPDM_BASE_URL.'assets/css/bootstrap-select.min.css');

            if(!is_admin()){
                wp_enqueue_script('wpdm-pp-js', plugins_url('/wpdm-premium-packages/assets/js/wpdmpp-front.js'), array('jquery'));
            }

            $settings = get_option('_wpdmpp_settings');

            if( get_the_ID() == $settings['orders_page_id'] ){
                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
                wp_enqueue_script('media-upload');
                wp_enqueue_media();
            }
        }

        function wpdmpp_is_purchased($pid, $uid = 0){
            global $current_user, $wpdb;
            if(!is_user_logged_in() && !$uid) return false;
            $uid = $uid?$uid:$current_user->ID;
            $orderid = $wpdb->get_var("select o.order_id from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi  where uid='{$uid}' and o.order_id = oi.oid and oi.pid = {$pid} and order_status='Completed'");
            return $orderid;
        }

        /**
         * Generate Download URL
         * @param $id
         * @return string|void
         */
        function wpdmpp_customer_download_link($id){
            $orderid = $this->wpdmpp_is_purchased($id);
            if($orderid)
                return $orderid ? wpdm_download_url($id, "&oid=$orderid") : "";
        }

        function hide_single_file_download_link($link, $url, $file){
            $effective_price = wpdmpp_effective_price($file['ID']);
            if($effective_price > 0) $link = '';
            return $link;
        }

        function fetch_template_tag($vars)
        {
            global $wpdb;
            //$vars['base_price'] = get_post_meta($vars['ID'], '__wpdm_base_price', true);
            //$vars['sales_price'] = get_post_meta($vars['ID'], '__wpdm_sales_price', true);
            $effective_price = wpdmpp_effective_price($vars['ID']);
            $vars['effective_price'] = $effective_price;
            $vars['currency'] = wpdmpp_currency_sign();
            $vars['currency_code'] = wpdmpp_currency_code();
            if ($effective_price > 0) {
                $vars['addtocart_url'] = home_url("?addtocart={$vars['ID']}");
                $vars['addtocart_link'] = wpdmpp_waytocart($vars);
                $vars['addtocart_button'] = $vars['addtocart_link'];
                $vars['addtocart_form'] = wpdmpp_add_to_cart_html($vars['ID']);
                $vars['customer_download_link'] = $this->wpdmpp_customer_download_link($vars['ID']);
                $vars['download_link'] = $vars['addtocart_form'];
                $vars['download_link_extended'] = $vars['addtocart_form'];
                $vars['download_link_popup'] = $vars['addtocart_button'];
            } else {
                $vars['addtocart_url'] = $vars['download_url'];
                $vars['addtocart_link'] = $vars['download_link'];
                $vars['addtocart_form'] = $vars['download_link'];
                $vars['customer_download_link'] = $vars['download_link'];
            }
            return $vars;
        }

        function template_editor_menu(){
            ?>

                                        <li class="dropdown">
                                            <a href="#" id="droppp" role="button" class="dropdown-toggle" data-toggle="dropdown">Premium Package <b class="caret"></b></a>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="droppp">
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_url]">AddToCart URL</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_link]">AddToCart Link</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_form]">AddToCart Form</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[customer_download_link]">Customer Download Link</a></li>
                                            </ul>
                                        </li>

            <?php
        }

        function template_tag_row(){
            ?>

                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_url]">AddToCart URL</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_link]">AddToCart Link</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_form]">AddToCart Form</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[customer_download_link]">Customer Download Link</a></li>

            <?php
        }

        /**
         * Required for guest checkout
         */
        function wpdmpp_invoide_field(){
            if(isset($_GET['orderid'])){
                echo "<input type='hidden' name='invoice' value='{$_GET['orderid']}' />";
            }
        }

        /**
         * Link Guest Order when user logging in
         * @param $user_login
         * @param $user
         */
        function wpdmpp_associate_invoice($user_login, $user){
            if(isset($_POST['invoice'])){
               $order = new Order();
               $orderdata = $order->GetOrder($_POST['invoice']);
                if($orderdata && intval($orderdata->uid) == 0){
                    Order::Update(array('uid'=>$user->ID), $_POST['invoice']);
                }
            }
        }

        /**
         * Link Guest Order when user Signing Up
         * @param $user_id
         */
        function wpdmpp_associate_invoice_signup($user_id){
            if(isset($_POST['invoice'])){
               $order = new Order();
               $orderdata = $order->GetOrder($_POST['invoice']);
                if($orderdata && intval($orderdata->uid) == 0){
                    Order::Update(array('uid'=>$user_id), $_POST['invoice']);
                }
            }
        }

        /**
         * Resolve unassigned Order
         */
        function wpdmpp_resolveorder(){
            global $current_user;
            $order = new Order();
            $data = $order->GetOrder($_REQUEST['orderid']);
            if(!$data) die("Order not found!");
            if($data->uid!=0) {
                if($data->uid==$current_user->ID)
                die("Order is already linked with your account!");
                else
                die("Order is already linked with an account!");
            }
            Order::Update(array('uid'=>$current_user->ID), $data->order_id);
            die("ok");
        }

        /**
         * Filter for locked Downloads
         * @param $lock
         * @param $id
         * @return string
         */
        function wpdmpp_lock_download($lock, $id){
            $effective_price = wpdmpp_effective_price($id);
            if( intval($effective_price) > 0 )
                $lock = 'locked';

            return $lock;
        }

	}

endif;

if(defined('WPDM_Version'))
new WPDMPremiumPackage();
