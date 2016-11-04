<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

//number of total sales
function wpdmpp_total_purchase($pid = '')
{
    global $wpdb;
    if (!$pid) $pid = get_the_ID();
    $sales = $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id and oi.pid='$pid' and o.payment_status='Completed'");

    return $sales;
}

function get_wpdmpp_option($name, $default = '')
{
    global $wpdmpp_settings;
    $name = explode('/', $name);

    if (count($name) == 1)
        return isset($wpdmpp_settings[$name[0]]) ? $wpdmpp_settings[$name[0]] : $default;
    else if (count($name) == 2)
        return isset($wpdmpp_settings[$name[0]]) && isset($wpdmpp_settings[$name[0]][$name[1]]) ? $wpdmpp_settings[$name[0]][$name[1]] : $default;
    else if (count($name) == 3)
        return isset($wpdmpp_settings[$name[0]]) && isset($wpdmpp_settings[$name[0]][$name[1]]) && isset($wpdmpp_settings[$name[0]][$name[1]][$name[2]]) ? $wpdmpp_settings[$name[0]][$name[1]][$name[2]] : $default;
    else
        return $default;
}

function wpdmpp_countries(){
    return array ( 'AF' => 'AFGHANISTAN', 'AL' => 'ALBANIA', 'DZ' => 'ALGERIA', 'AS' => 'AMERICAN SAMOA', 'AD' => 'ANDORRA', 'AO' => 'ANGOLA', 'AI' => 'ANGUILLA', 'AQ' => 'ANTARCTICA', 'AG' => 'ANTIGUA AND BARBUDA', 'AR' => 'ARGENTINA', 'AM' => 'ARMENIA', 'AW' => 'ARUBA', 'AU' => 'AUSTRALIA', 'AT' => 'AUSTRIA', 'AZ' => 'AZERBAIJAN', 'BS' => 'BAHAMAS', 'BH' => 'BAHRAIN', 'BD' => 'BANGLADESH', 'BB' => 'BARBADOS', 'BY' => 'BELARUS', 'BE' => 'BELGIUM', 'BZ' => 'BELIZE', 'BJ' => 'BENIN', 'BM' => 'BERMUDA', 'BT' => 'BHUTAN', 'BO' => 'BOLIVIA', 'BA' => 'BOSNIA AND HERZEGOVINA', 'BW' => 'BOTSWANA', 'BV' => 'BOUVET ISLAND', 'BR' => 'BRAZIL', 'IO' => 'BRITISH INDIAN OCEAN TERRITORY', 'BN' => 'BRUNEI DARUSSALAM', 'BG' => 'BULGARIA', 'BF' => 'BURKINA FASO', 'BI' => 'BURUNDI', 'KH' => 'CAMBODIA', 'CM' => 'CAMEROON', 'CA' => 'CANADA', 'CV' => 'CAPE VERDE', 'KY' => 'CAYMAN ISLANDS', 'CF' => 'CENTRAL AFRICAN REPUBLIC', 'TD' => 'CHAD', 'CL' => 'CHILE', 'CN' => 'CHINA', 'CX' => 'CHRISTMAS ISLAND', 'CC' => 'COCOS (KEELING) ISLANDS', 'CO' => 'COLOMBIA', 'KM' => 'COMOROS', 'CG' => 'CONGO', 'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'CK' => 'COOK ISLANDS', 'CR' => 'COSTA RICA', 'CI' => 'COTE DIVOIRE', 'HR' => 'CROATIA', 'CU' => 'CUBA', 'CY' => 'CYPRUS', 'CZ' => 'CZECH REPUBLIC', 'DK' => 'DENMARK', 'DJ' => 'DJIBOUTI', 'DM' => 'DOMINICA', 'DO' => 'DOMINICAN REPUBLIC', 'EC' => 'ECUADOR', 'EG' => 'EGYPT', 'SV' => 'EL SALVADOR', 'GQ' => 'EQUATORIAL GUINEA', 'ER' => 'ERITREA', 'EE' => 'ESTONIA', 'ET' => 'ETHIOPIA', 'FK' => 'FALKLAND ISLANDS (MALVINAS)', 'FO' => 'FAROE ISLANDS', 'FJ' => 'FIJI', 'FI' => 'FINLAND', 'FR' => 'FRANCE', 'GF' => 'FRENCH GUIANA', 'PF' => 'FRENCH POLYNESIA', 'TF' => 'FRENCH SOUTHERN TERRITORIES', 'GA' => 'GABON', 'GM' => 'GAMBIA', 'GE' => 'GEORGIA', 'DE' => 'GERMANY', 'GH' => 'GHANA', 'GI' => 'GIBRALTAR', 'GR' => 'GREECE', 'GL' => 'GREENLAND', 'GD' => 'GRENADA', 'GP' => 'GUADELOUPE', 'GU' => 'GUAM', 'GT' => 'GUATEMALA', 'GN' => 'GUINEA', 'GW' => 'GUINEA-BISSAU', 'GY' => 'GUYANA', 'HT' => 'HAITI', 'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS', 'VA' => 'HOLY SEE (VATICAN CITY STATE)', 'HN' => 'HONDURAS', 'HK' => 'HONG KONG', 'HU' => 'HUNGARY', 'IS' => 'ICELAND', 'IN' => 'INDIA', 'ID' => 'INDONESIA', 'IR' => 'IRAN, ISLAMIC REPUBLIC OF', 'IQ' => 'IRAQ', 'IE' => 'IRELAND', 'IL' => 'ISRAEL', 'IT' => 'ITALY', 'JM' => 'JAMAICA', 'JP' => 'JAPAN', 'JO' => 'JORDAN', 'KZ' => 'KAZAKHSTAN', 'KE' => 'KENYA', 'KI' => 'KIRIBATI', 'KP' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'KR' => 'KOREA, REPUBLIC OF', 'KW' => 'KUWAIT', 'KG' => 'KYRGYZSTAN', 'LA' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'LV' => 'LATVIA', 'LB' => 'LEBANON', 'LS' => 'LESOTHO', 'LR' => 'LIBERIA', 'LY' => 'LIBYAN ARAB JAMAHIRIYA', 'LI' => 'LIECHTENSTEIN', 'LT' => 'LITHUANIA', 'LU' => 'LUXEMBOURG', 'MO' => 'MACAO', 'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'MG' => 'MADAGASCAR', 'MW' => 'MALAWI', 'MY' => 'MALAYSIA', 'MV' => 'MALDIVES', 'ML' => 'MALI', 'MT' => 'MALTA', 'MH' => 'MARSHALL ISLANDS', 'MQ' => 'MARTINIQUE', 'MR' => 'MAURITANIA', 'MU' => 'MAURITIUS', 'YT' => 'MAYOTTE', 'MX' => 'MEXICO', 'FM' => 'MICRONESIA, FEDERATED STATES OF', 'MD' => 'MOLDOVA, REPUBLIC OF', 'MC' => 'MONACO', 'MN' => 'MONGOLIA', 'MS' => 'MONTSERRAT', 'MA' => 'MOROCCO', 'MZ' => 'MOZAMBIQUE', 'MM' => 'MYANMAR', 'NA' => 'NAMIBIA', 'NR' => 'NAURU', 'NP' => 'NEPAL', 'NL' => 'NETHERLANDS', 'AN' => 'NETHERLANDS ANTILLES', 'NC' => 'NEW CALEDONIA', 'NZ' => 'NEW ZEALAND', 'NI' => 'NICARAGUA', 'NE' => 'NIGER', 'NG' => 'NIGERIA', 'NU' => 'NIUE', 'NF' => 'NORFOLK ISLAND', 'MP' => 'NORTHERN MARIANA ISLANDS', 'NO' => 'NORWAY', 'OM' => 'OMAN', 'PK' => 'PAKISTAN', 'PW' => 'PALAU', 'PS' => 'PALESTINIAN TERRITORY, OCCUPIED', 'PA' => 'PANAMA', 'PG' => 'PAPUA NEW GUINEA', 'PY' => 'PARAGUAY', 'PE' => 'PERU', 'PH' => 'PHILIPPINES', 'PN' => 'PITCAIRN', 'PL' => 'POLAND', 'PT' => 'PORTUGAL', 'PR' => 'PUERTO RICO', 'QA' => 'QATAR', 'RE' => 'REUNION', 'RO' => 'ROMANIA', 'RU' => 'RUSSIAN FEDERATION', 'RW' => 'RWANDA', 'SH' => 'SAINT HELENA', 'KN' => 'SAINT KITTS AND NEVIS', 'LC' => 'SAINT LUCIA', 'PM' => 'SAINT PIERRE AND MIQUELON', 'VC' => 'SAINT VINCENT AND THE GRENADINES', 'WS' => 'SAMOA', 'SM' => 'SAN MARINO', 'ST' => 'SAO TOME AND PRINCIPE', 'SA' => 'SAUDI ARABIA', 'SN' => 'SENEGAL', 'CS' => 'SERBIA AND MONTENEGRO', 'SC' => 'SEYCHELLES', 'SL' => 'SIERRA LEONE', 'SG' => 'SINGAPORE', 'SK' => 'SLOVAKIA', 'SI' => 'SLOVENIA', 'SB' => 'SOLOMON ISLANDS', 'SO' => 'SOMALIA', 'ZA' => 'SOUTH AFRICA', 'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'ES' => 'SPAIN', 'LK' => 'SRI LANKA', 'SD' => 'SUDAN', 'SR' => 'SURINAME', 'SJ' => 'SVALBARD AND JAN MAYEN', 'SZ' => 'SWAZILAND', 'SE' => 'SWEDEN', 'CH' => 'SWITZERLAND', 'SY' => 'SYRIAN ARAB REPUBLIC', 'TW' => 'TAIWAN, PROVINCE OF CHINA', 'TJ' => 'TAJIKISTAN', 'TZ' => 'TANZANIA, UNITED REPUBLIC OF', 'TH' => 'THAILAND', 'TL' => 'TIMOR-LESTE', 'TG' => 'TOGO', 'TK' => 'TOKELAU', 'TO' => 'TONGA', 'TT' => 'TRINIDAD AND TOBAGO', 'TN' => 'TUNISIA', 'TR' => 'TURKEY', 'TM' => 'TURKMENISTAN', 'TC' => 'TURKS AND CAICOS ISLANDS', 'TV' => 'TUVALU', 'UG' => 'UGANDA', 'UA' => 'UKRAINE', 'AE' => 'UNITED ARAB EMIRATES', 'GB' => 'UNITED KINGDOM', 'US' => 'UNITED STATES', 'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS', 'UY' => 'URUGUAY', 'UZ' => 'UZBEKISTAN', 'VU' => 'VANUATU', 'VE' => 'VENEZUELA', 'VN' => 'VIET NAM', 'VG' => 'VIRGIN ISLANDS, BRITISH', 'VI' => 'VIRGIN ISLANDS, U.S.', 'WF' => 'WALLIS AND FUTUNA', 'EH' => 'WESTERN SAHARA', 'YE' => 'YEMEN', 'ZM' => 'ZAMBIA', 'ZW' => 'ZIMBABWE' );
}

function wpdmpp_tax_active(){
    $settings = get_option('_wpdmpp_settings');
    return isset($settings['tax']) && isset($settings['tax']['enable'])?true:false;
}

function wpdmpp_show_tax(){
    $settings = get_option('_wpdmpp_settings');
    return isset($settings['tax']) && isset($settings['tax']['tax_on_cart'])?true:false;
}


//Send notification before delete product
add_action('wp_trash_post', 'notify_product_rejected');
function notify_product_rejected($post_id)
{
    global $post_type;
    if ($post_type != 'wpdmpro') return;

    $post = get_post($post_id);
    $post_meta = get_post_meta($post_id, "_z_user_review", true);

    if ($post_meta != ""):
        $author = get_userdata($post->post_author);
        $author_email = $author->user_email;
        $email_subject = "Your product has been rejected.";

        ob_start(); ?>
        <html>
        <head>
            <title>New post at <?php bloginfo('name') ?></title>
        </head>
        <body>
        <p>
            Hi <?php echo $author->user_firstname ?>,
        </p>

        <p>
            Your product <?php the_title() ?> has not been approved by team.
        </p>
        </body>
        </html>
        <?php
        $message = ob_get_contents();
        ob_end_clean();

        wp_mail($author_email, $email_subject, $message);
    endif;
}

// Product accept notification email
function notify_product_accepted($post_id)
{
    global $post_type;
    if ($post_type != 'wpdmpro') return;

    if (($_POST['post_status'] == 'publish') && ($_POST['original_post_status'] != 'publish')) {
        $post = get_post($post_id);
        $post_meta = get_post_meta($post_id, "_z_user_review", TRUE);
        if ($post_meta != ""):

            $author = get_userdata($post->post_author);
            $author_email = $author->user_email;
            $email_subject = "Your post has been published.";

            ob_start(); ?>
            <html>
            <head>
                <title>Your Product Status at <?php bloginfo('name') ?></title>
            </head>
            <body>
                <p>Hi <?php echo $author->user_firstname ?>,</p>
                <p>Your product <a href="<?php echo get_permalink($post->ID) ?>"><?php the_title_attribute() ?></a> has been published.</p>
            </body>
            </html>
            <?php
            $message = ob_get_clean();

            wp_mail($author_email, $email_subject, $message);
        endif;
    }
}

//for withdraw request
function wpdmpp_withdraw_request()
{
    global $wpdb, $current_user;

    $uid = $current_user->ID;

    if (isset($_POST['withdraw'], $_POST['withdraw_amount']) && $_POST['withdraw'] == 1 && $_POST['withdraw_amount'] > 0) {

        $wpdb->insert(
            "{$wpdb->prefix}ahm_withdraws",
            array(
                'uid' => $uid,
                'date' => time(),
                'amount' => $_POST['withdraw_amount'],
                'status' => 0
            ),
            array(
                '%d',
                '%d',
                '%f',
                '%d'
            )
        );
        if (wpdm_is_ajax()) {
            _e("Withdraw Request Sent!", "wpdm-premium-package");
            die();
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        die();
    }

}

function wpdmpp_redirect($url)
{
    if (!headers_sent())
        header("location: " . $url);
    else
        echo "<script>location.href='{$url}';</script>";
    die();
}

function wpdmpp_js_redirect($url)
{
    echo "&nbsp;Redirecting...<script>location.href='{$url}';</script>";
    die();
}

function wpdmpp_members_page()
{
    $settings = get_option('_wpdmpp_settings');
    return get_permalink($settings['members_page_id']);
}

function wpdmpp_orders_page($part = '')
{
    $settings = get_option('_wpdmpp_settings');
    $url = get_permalink($settings['orders_page_id']);
    if ($part != '') {
        if (strpos($url, '?')) $url .= "&" . $part;
        else $url .= "?" . $part;
    }

    $udbpage = get_option('__wpdm_user_dashboard', 0);
    if($udbpage) {
        $udbpage = get_permalink($udbpage);
        $url = $udbpage."purchases/orders/";
        if($part != ''){
            $part = explode("=", $part);
            $url = $udbpage . "purchases/order/" . end($part) . "/";
        }
    }
    return $url;
}

function wpdmpp_guest_order_page($part = '')
{
    $settings = get_option('_wpdmpp_settings');
    $url = get_permalink($settings['guest_order_page_id']);
    if ($part != '') {
        if (strpos($url, '?')) $url .= "&" . $part;
        else $url .= "?" . $part;
    }
    return $url;
}

function wpdmpp_cart_page($part = '')
{
    $settings = get_option('_wpdmpp_settings');
    $url = get_permalink($settings['page_id']);
    if ($part != '') {
        if (strpos($url, '?')) $url .= "&" . $part;
        else $url .= "?" . $part;
    }
    return $url;
}

function wpdmpp_continue_shopping_url($part = '')
{
    $settings = get_option('_wpdmpp_settings');
    return $settings['continue_shopping_url'];
}

function wpdmpp_billing_info_form(){
    global $current_user;
    $billing = maybe_unserialize(get_user_meta($current_user->ID, 'user_billing_shipping', true));
    $billing = isset($billing['billing'])?$billing['billing']:array();
    include wpdm_tpl_path('billing-info.php', WPDMPP_BASE_DIR.'/templates/');
}

function wpdmpp_save_billing_info(){
    global $current_user;
    if(isset($_POST['checkout']) && isset($_POST['checkout']['billing'])){
        update_user_meta($current_user->ID, 'user_billing_shipping', serialize($_POST['checkout']));
    }
}

function wpdmpp_get_purchased_items(){
    if(!isset($_GET['wpdmppaction']) || $_GET['wpdmppaction'] != 'getpurchaseditems') return;
    $user = wp_signon(array('user_login' => $_GET['user'], 'user_password' => $_GET['pass']));
    if($user->ID) wp_set_current_user($user->ID);
    if(is_user_logged_in())
        echo json_encode(Order::getPurchasedItems());
    else
        echo json_encode(array('error' => '<a href="http://www.wpdownloadmanager.com/user-dashboard/?redirect_to=[redirect]">You need to login first!</a>'));
    die();
}

/**
 * Retrienve Site Commissions on User's Sales
 * @param null $uid
 * @return mixed
 */
function wpdmpp_site_commission($uid = null)
{
    global $current_user;
    $user = $current_user;
    if ($uid) $user = get_userdata($uid);
    $comission = get_option("wpdmpp_user_comission");
    $comission = $comission[$user->roles[0]];
    return $comission;
}

function wpdmpp_get_user_earning()
{

}

function wpdmpp_product_price($pid)
{
    $base_price = get_post_meta($pid, "__wpdm_base_price", true);
    $sales_price = get_post_meta($pid, "__wpdm_sales_price", true);
    $price = floatval($sales_price) > 0 && $sales_price < $base_price ? $sales_price : $base_price;
    if (floatval($price) == 0) return number_format(0, 2, ".", "");
    return number_format($price, 2, ".", "");
}

function wpdmpp_is_ajax()
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) return TRUE;
    return false;
}

//delete product from front-end
function wpdmpp_delete_product()
{
    if (is_user_logged_in() && isset($_GET['dproduct'])) {
        global $current_user;
        $pid = intval($_GET['dproduct']);
        $pro = get_post($pid);

        if ($current_user->ID == $pro->post_author) {
            wp_update_post(array('ID' => $pid, 'post_status' => 'trash'));
            $settings = get_option('_wpdmpp_settings');
            if ($settings['frontend_product_delete_notify'] == 1) {
                wp_mail(get_option('admin_email'), "I had to delete a product", "Hi, Sorry, but I had to delete following product for some reason:<br/>{$pro->post_title}", "From: {$current_user->user_email}\r\nContent-type: text/html\r\n\r\n");
            }
            $_SESSION['dpmsg'] = 'Product Deleted';
            header("location: " . $_SERVER['HTTP_REFERER']);
            die();
        }
    }
}

function wpdmpp_order_completed_mail()
{

}

function wpdmpp_head()
{
    ?>
    <script>
        var wpdmpp_base_url = '<?php echo plugins_url('/wpdm-premium-packages/'); ?>';
    </script>
    <?php
}

add_action("wp_ajax_wpdmpp_delete_frontend_order", "wpdmpp_delete_frontend_order");
add_action("wp_ajax_nopriv_wpdmpp_delete_frontend_order", "wpdmpp_delete_frontend_order");

function wpdmpp_delete_frontend_order()
{
    if (!wp_verify_nonce($_REQUEST['nonce'], "delete_order")) {
        exit("No naughty business please");
    }

    $result['type'] = 'failed';
    global $wpdb;
    $order_id = esc_attr($_REQUEST['order_id']);

    $ret = $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_orders WHERE order_id = %s", $order_id));

    if ($ret) {
        $ret = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_order_items WHERE oid = %s", $order_id));

        if ($ret) $result['type'] = 'success';
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
    } else {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }

    die();
}

add_action("wp_ajax_nopriv_update_guest_billing", "wpdmpp_update_guest_billing");
function wpdmpp_update_guest_billing(){
    $billinginfo = array
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
        'email' => '',
        'phone' => ''
    );
    $sbillinginfo  = $_POST['billing'];
    $billinginfo = shortcode_atts($billinginfo, $sbillinginfo);
    Order::Update(array('billing_info'=> serialize($billinginfo)), $_SESSION['guest_order'] );
    die('Saved!');
}

function wpdmpp_recalculate_sales()
{
    if (!isset($_POST['id'])) return;
    global $wpdb;
    $id = (int)$_POST['id'];
    $sql = "select sum(quantity*price) as sales_amount, sum(quantity) as sales_quantity from {$wpdb->prefix}ahm_order_items oi, {$wpdb->prefix}ahm_orders o where oi.oid = o.order_id and oi.pid = {$id} and o.order_status IN ('Completed', 'Expired')";
    $data = $wpdb->get_row($sql);

    header('Content-type: application/json');
    update_post_meta($id, '__wpdm_sales_amount', $data->sales_amount);
    update_post_meta($id, '__wpdm_sales_count', $data->sales_quantity);
    $data->sales_amount = wpdmpp_currency_sign() . floatval($data->sales_amount);
    $data->sales_quantity = intval($data->sales_quantity);
    echo json_encode($data);
    die();
}

function wpdmpp_effective_price($pid)
{
    global  $current_user;

    $base_price = get_post_meta($pid, "__wpdm_base_price", true);
    $sales_price = get_post_meta($pid, "__wpdm_sales_price", true);
    $price = intval($sales_price) > 0 ? $sales_price : $base_price;

    $role = is_user_logged_in()?$current_user->roles[0]:'guest';
    $discount = maybe_unserialize(get_post_meta($pid,'__wpdm_discount', true));
    $discount[$role] = isset($discount[$role])?$discount[$role]:0;
    $user_discount = (($price*$discount[$role])/100);
    $price -= $user_discount;

    if(!$price) $price = 0;
    return number_format($price, 2, ".", "");
}

function wpdmpp_currency_sign()
{
    $settings = get_option('_wpdmpp_settings');
    $currency = isset($settings['currency']) ? $settings['currency'] : 'USD';
    $cdata = Currencies::GetCurrency($currency);
    $sign = is_array($cdata) ? $cdata['symbol'] : '$';
    return $sign;
}

function wpdmpp_currency_code()
{
    $settings = get_option('_wpdmpp_settings');
    $currency = isset($settings['currency']) ? $settings['currency'] : 'USD';
    return $currency;
}

/**
 * Validating download request using 'wpdm_onstart_download' WPDM hook
 * @param $package
 * @return mixed
 */
function wpdmpp_validate_download($package)
{
    global $current_user, $wpdb;

    
    $order = new Order();

    $price = wpdmpp_effective_price($package['ID']);
    if (floatval($price) == 0) return $package;
    $oid = isset($_GET['oid']) ? $_GET['oid'] : "";
    $ord = $order->getOrder($oid);
    if(isset($_GET['customerkey'])){
        //$customerkey =
    }
    if (($oid == "" || !is_object($ord)) && $price > 0) wp_die('You do not have permission to download this file');

    $settings = get_option('_wpdmpp_settings');
    $order = new Order();
    $odata = $order->GetOrder($_GET['oid']);
    $items = unserialize($odata->items);

    if (@in_array($_GET['wpdmdl'], $items)
        && isset($_GET['oid'])
        && $_GET['oid'] != ''
        && !is_user_logged_in()
        && $odata->uid == 0
        && $odata->order_status == 'Completed'
        && isset($settings['guest_download'])
        && isset($_SESSION['guest_order'])) {
        //for guest download
        return $package;
    }

    if ((is_user_logged_in() && $current_user->ID != $ord->uid && $price > 0) || (!is_user_logged_in() && $price > 0)) wp_die('You do not have permission to download this file');
    return $package;
}

/**
 * Assign an order to specific user
 */
function wpdmpp_assign_user_2order()
{
    if (isset($_REQUEST['assignuser']) && isset($_REQUEST['order'])) {
        $u = get_user_by('login', $_REQUEST['assignuser']);
        $order = new Order();
        $order->Update(array('uid' => $u->ID), $_REQUEST['order']);
        die('Done!');
    }
}

function wpdmpp_download_order_note_attachment()
{
    global $current_user;
    if (!isset($_GET['_atcdl']) || !is_user_logged_in()) return;
    $key = WPDM_Crypt::Decrypt($_GET['_atcdl']);
    $key = explode("|||", $key);
    $order = new Order($key[0]);
    if ($order->Uid != $current_user->ID && !current_user_can('manage_options')) wp_die('Unauthorized Access');
    $files = $order->OrderNotes['messages'][$key[1]]['file'];
    $filename = preg_replace("/^[0-9]+?wpdm_/", "", $key[2]);
    if (in_array($key[2], $files)) {
        wpdm_download_file(UPLOAD_DIR . $key[2], $filename);
        die();
    }
}

/**
 * Return Premium Package Template Directory
 * @return string
 */
function wpdmpp_tpl_dir(){
    return WPDMPP_BASE_DIR."/templates/";
}