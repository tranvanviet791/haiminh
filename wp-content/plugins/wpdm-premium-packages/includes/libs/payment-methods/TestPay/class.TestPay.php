<?php
if (!class_exists('TestPay')) {

    class TestPay extends CommonVars
    {
        var $GatewayUrl = '';
        var $GatewayName = 'Test Pay';
        var $ReturnUrl;
        var $CancelUrl;
        var $Enabled;
        var $Currency;
        var $ClientEmail;
        var $order_id;
        var $buyer_email;

        function __construct($Mode = 0)
        {
            global $current_user;
            $this->GatewayUrl = home_url('/?wpdmpp_test_payment=1');
            $this->Enabled = get_wpdmpp_option('TestPay/enabled');
            $this->ReturnUrl = get_wpdmpp_option('TestPay/return_url', wpdmpp_orders_page());
            $this->CancelUrl = get_wpdmpp_option('TestPay/cancel_url', home_url('/'));
            $this->NotifyUrl = home_url('?action=wpdmpp-payment-notification&class=Paypal');
            $this->Currency = wpdmpp_currency_code();
            if (is_user_logged_in()) {
                $this->ClientEmail = $current_user->user_email;
            }
        }

        function ConfigOptions()
        {
            if ($this->Enabled) $enabled = 'checked="checked"'; else $enabled = "";
            $options = array(
                'cancel_url' => array(
                    'label' => __("Cancel Url:", "wpdm-premium-package"),
                    'type' => 'text',
                    'placeholder' => '',
                    'value' => $this->CancelUrl
                ),
                'return_url' => array(
                    'label' => __("Return Url:", "wpdm-premium-package"),
                    'type' => 'text',
                    'placeholder' => '',
                    'value' => $this->ReturnUrl
                ),
            );
            return $options;
        }

        function ShowPaymentForm($AutoSubmit = 0)
        {
            Order::complete_order($this->InvoiceNo);
            do_action("wpdm_after_checkout", $this->InvoiceNo);
            return "<div class='alert alert-success'><i class='fa fa-spinner fa-spin'></i> Redirecting...</div><script>location.href='{$this->ReturnUrl}';</script>";
        }

        function VerifyPayment()
        {
            return true;
        }
    }
}
?>