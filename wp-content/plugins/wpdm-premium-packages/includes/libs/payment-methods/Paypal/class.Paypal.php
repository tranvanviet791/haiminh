<?php
if(!class_exists('Paypal')){
class Paypal extends CommonVars{

    public $TestMode;

    public $GatewayUrl = "https://www.Paypal.com/cgi-bin/webscr";
    public $GatewayUrl_TestMode = "https://www.sandbox.Paypal.com/cgi-bin/webscr";
    public $Business;
    public $ReturnUrl;
    public $NotifyUrl;
    public $CancelUrl;
    public $Custom;
    public $Enabled;
    public $Currency;
    public $ClientEmail;
    public $buyer_email;
    
    
    function __construct($Mode = 0){
        global $current_user;

        if($Mode==1)
        $this->GatewayUrl = $this->GatewayUrl_TestMode;

        $this->Enabled = get_wpdmpp_option('Paypal/enabled');
        $this->ReturnUrl = get_wpdmpp_option('Paypal/return_url', wpdmpp_orders_page());
        $this->NotifyUrl = home_url('?action=wpdmpp-payment-notification&class=Paypal');
        $this->CancelUrl = get_wpdmpp_option('Paypal/cancel_url', home_url('/'));
        $this->Business =   get_wpdmpp_option('Paypal/Paypal_email');
        $this->TestMode =   get_wpdmpp_option('Paypal/Paypal_mode', 'live');
        $this->PayPalMode =  get_wpdmpp_option('Paypal/Paypal_mode', 'live');
        $this->Currency =  wpdmpp_currency_code();
        if(is_user_logged_in()){
            $this->ClientEmail = $current_user->user_email;
        }
        
        if($this->PayPalMode=='sandbox')
        $this->GatewayUrl = $this->GatewayUrl_TestMode;
    }
    
    
    function ConfigOptions(){    
        
        
        
        if($this->Enabled)$enabled='checked="checked"';
        else $enabled = "";

        $options = array(

            'Paypal_mode'=> array(
                'label'         =>      __("Paypal Mode:","wpdm-premium-package"),
                'type'          =>      'select',
                'options'       =>      array('live'=>'Live','sandbox'=>'Test'),
                'selected'      =>      $this->PayPalMode
            ),
            'Paypal_email'=> array(
                'label'         =>      __("Paypal Email:","wpdm-premium-package"),
                'type'          =>      'text',
                'options'       =>      array('live'=>'Live','sandbox'=>'SandBox(Test Mode)'),
                'placeholder'   => '',
                'value'         =>      $this->Business
            ),
            'cancel_url'=> array(
                'label'         =>      __("Cancel Url:","wpdm-premium-package"),
                'type'          =>      'text',
                'placeholder'   =>     '',
                'value'         =>      $this->CancelUrl
            ),
            'return_url'=> array(
                'label'         =>      __("Return Url:","wpdm-premium-package"),
                'type'          =>      'text',
                'placeholder'   =>      '',
                'value'         =>      $this->ReturnUrl
            ),
        );

        return $options;
    }
    
    function ShowPaymentForm($AutoSubmit = 0){

        if($AutoSubmit==1) $hide = "display:none;'";
        $Paypal = plugins_url().'/wpdm-premium-packages/images/Paypal.png';
        $Form = " 
                    <form method='post' style='margin:0px;' name='_wpdm_bnf_{$this->InvoiceNo}' id='_wpdm_bnf_{$this->InvoiceNo}' action='{$this->GatewayUrl}'>

                    <input type='hidden' name='business' value='{$this->Business}' />

                    <input type='hidden' name='cmd' value='_xclick' />
                    <!-- the next three need to be created -->
                    <input type='hidden' name='return' value='{$this->ReturnUrl}' />
                    <input type='hidden' name='cancel_return' value='{$this->CancelUrl}' />
                    <input type='hidden' name='notify_url' value='{$this->NotifyUrl}' />
                    <input type='hidden' name='rm' value='2' />
                    <input type='hidden' name='currency_code' value='{$this->Currency}' />
                    <input type='hidden' name='lc' value='US' />
                    <input type='hidden' name='bn' value='W3Eden_SP' />

                    <input type='hidden' name='cbt' value='Continue' />
                    
                    <!-- Payment Page Information -->
                    <input type='hidden' name='no_shipping' value='' />
                    <input type='hidden' name='no_note' value='1' />
                    <input type='hidden' name='cn' value='Comments' />
                    <input type='hidden' name='cs' value='' />
                    
                    <!-- Product Information -->
                    <input type='hidden' name='item_name' value='{$this->OrderTitle}' />
                    <input type='hidden' name='amount' value='{$this->Amount}' />

                    <input type='hidden' name='quantity' value='1' />
                    <input type='hidden' name='item_number' value='{$this->InvoiceNo}' />
                    <input type='hidden' name='email' value='{$this->ClientEmail}' />
                    <input type='hidden' name='custom' value='{$this->Custom}' />
                    
                    <!-- Shipping and Misc Information -->
                     
                    <input type='hidden' name='invoice' value='{$this->InvoiceNo}' />

                    <noscript><p>Your browser doesn't support Javscript, click the button below to process the transaction.</p>
                    <button type='submit' class='btn btn-success'>Buy Now</button></noscript>
                    </form>
         
        
        ";


        if($AutoSubmit==1)
        $Form .= "<div class='alert alert-success'>".__("Proceeding to Paypal....","wpmarktplace")."</div><script language=javascript>setTimeout('document._wpdm_bnf_{$this->InvoiceNo}.submit()',1000);</script>";

        if( $this->Business=='' || $this->Currency == '' ){
            $Form = "<div class='alert alert-danger'>".__("There are some problems with PayPal setup, please notify site admin","wpmarktplace")."</div>";
        }

        return $Form;
        
        
    }
    
    
    function VerifyPayment() {

          // parse the Paypal URL
          $url_parsed=parse_url($this->GatewayUrl);        

          // generate the post string from the _POST vars aswell as load the
          // _POST vars into an arry so we can play with them from the calling
          // script.
          //print_r($_POST);
          
          $this->InvoiceNo = $_POST['invoice'];
          $order = new Order();
          $orderdata = $order->GetOrder($this->InvoiceNo);
          $this->buyer_email = $_POST['payer_email'];
          if(floatval($orderdata->total) != floatval($_POST['mc_gross'])) return false;
          
          $post_string = '';    
          foreach ($_POST as $field=>$value) { 
             $this->ipn_data["$field"] = $value;
             $post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
          }
          $post_string.="cmd=_notify-validate"; // append ipn command


         if(function_exists('curl_init')){
             $ch = curl_init($this->GatewayUrl);
             curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
             curl_setopt($ch, CURLOPT_POST, 1);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
             curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
             curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
             $this->ipn_response = curl_exec($ch);
             curl_close($ch);

           } else {

          // open the connection to Paypal
          $fp = fsockopen($url_parsed['host'],"80",$err_num,$err_str,30);
          if(!$fp) {
              
             // could not open the connection.  If loggin is on, the error message
             // will be in the log.
             $this->last_error = "fsockopen error no. $errnum: $errstr";
             $this->log_ipn_results(false);       
             return false;
             
          } else { 
     
             // Post the data back to Paypal
             fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
             fputs($fp, "Host: $url_parsed[host]\r\n"); 
             fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
             fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
             fputs($fp, "Connection: close\r\n\r\n"); 
             fputs($fp, $post_string . "\r\n\r\n"); 

             // loop through the response from the server and append to variable
             while(!feof($fp)) { 
                $this->ipn_response .= fgets($fp, 1024); 
             } 

             fclose($fp); // close connection

          }}

          if (strpos($this->ipn_response, "ERIFIED")) {
      
             // Valid IPN transaction.             
             return true;       
             
          } else {
      
             // Invalid IPN transaction.  Check the log for details.
             $this->VerificationError = 'IPN Validation Failed.';             
             return false;
         
      }
      
   }
   
   function VerifyNotification(){
       
       if($_POST){
           $this->InvoiceNo = $_POST['invoice'];
           return $this->VerifyPayment();
       }
       else die("Problem occured in payment.");
   }
    
    
}
}
?>