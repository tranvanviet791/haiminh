<?php
global $payment_methods, $current_user;
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

if(is_user_logged_in())
    $sbilling = maybe_unserialize(get_user_meta(get_current_user_id(), 'user_billing_shipping', true));
$sbilling = is_array($sbilling) && isset($sbilling['billing'])?$sbilling['billing']:array();
$billing  = shortcode_atts($billing, $sbilling);
if($billing['order_email'] == '' && is_user_logged_in()) $billing['order_email'] = $current_user->user_email;
ob_start();
?>
<div id="select-payment-method">
    <form action="" name="payment_form" id="payment_form" method="post">
        <div class="panel panel-default">
            <?php if(get_wpdmpp_option('billing_address') == 1){ ?>
            <div class="panel-heading"><?php echo __("Billing Address","wpdm-premium-package"); ?></div>
            <div class="panel-body">

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
                                    <select id="region" name="billing[state]" type="text" class="form-control <?php echo wpdmpp_tax_active()?'calculate-tax':''; ?>"></select>
                                    <input id="region-txt" style="display:none;" name="billing[state]" value="<?php echo $billing['state']; ?>" type="text" placeholder="<?php echo __("state / province / region","wpdm-premium-package"); ?>" class="form-control <?php echo wpdmpp_tax_active()?'calculate-tax':''; ?>">
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
                                    <select id="country" name="billing[country]" required="required" class="form-control <?php echo wpdmpp_tax_active()?'calculate-tax':''; ?>"  data-live-search="true" x-moz-errormessage="<?php echo __("Please Select Your Country","wpdm-premium-package"); ?>">
                                        <option value=""><?php echo __("Select Country","wpdm-premium-package"); ?></option>
                                         <?php foreach($allowed_countries as $country_code){ ?>
                                             <option value="<?php echo $country_code; ?>"><?php echo $all_countries[$country_code]; ?></option>
                                         <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label><?php echo __("Enter Order Notification Email","wpdm-premium-package"); ?> <span class="required" title="<?php _e('Required','wpdm-premium-package'); ?>">*</span></label>
                                <input type="email"  value="<?php echo $billing['order_email']; ?>" required="required" class="form-control" name="billing[order_email]" id="email_m" placeholder="<?php echo __("Enter Order Notification Email","wpdm-premium-package"); ?>">

                            </div>
                        </div>

                    </div>

            </div>
            <?php } else { ?>
            <div class="panel-heading"><?php echo __("Please Enter Your Name & Email","wpdm-premium-package"); ?></div>
            <div class="panel-body">
                <!-- full-name input-->
                <div class="form-group">
                    <div class="controls row">
                        <div class="col-md-6">
                            <input id="f-name" value="<?php echo $billing['first_name']; ?>" name="billing[first_name]" required="required" type="text" placeholder="<?php echo __("Name","wpdm-premium-package"); ?>" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <input type="email"  value="<?php echo $billing['order_email']; ?>" required="required" class="form-control" name="billing[order_email]" id="email_m" placeholder="<?php echo __("Enter Order Notification Email","wpdm-premium-package"); ?>">
                        </div>

                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="panel-heading"><?php echo __("Select Payment Method:","wpdm-premium-package"); ?></div>
            <div class="panel-body" id="csp"><div class="list-group pm-list">


                    <?php
                    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
                    $payment_methods = apply_filters('payment_method', $payment_methods);
                    $payment_methods = isset($settings['pmorders']) && count($settings['pmorders']) == count($payment_methods)?$settings['pmorders']:$payment_methods;
                    foreach($payment_methods as $payment_method){
                        if(class_exists($payment_method)){
                            if(isset($settings[$payment_method]['enabled']) && $settings[$payment_method]['enabled'] == 1){
                                $obj = new $payment_method();
                                $name = isset($obj->GatewayName)?$obj->GatewayName:$payment_method;
                                echo '<label class="list-group-item"><input class="pull-right" type="radio" name="payment_method" value="'.$payment_method.'" > '.$name.'</label>';
                            }
                        }
                    }
                    ?>

                </div>
            </div>
            <div class="panel-footer text-right">
                <div class="pull-left hide cart-total-final panel-heading"><?php _e('Total:','wpdm-premium-package'); ?></div>
                <button id="pay_btn" class="button btn btn-success" type="submit"><i class="fa fa-check-square"></i> &nbsp; <?php echo __("Pay Now","wpdm-premium-package");?></button>
                <div class="hide pull-right" id="payment_w8"><img src='<?php echo admin_url('/images/loading.gif'); ?>' /></div>
            </div>
        </div>

    </form><br/>
    <div id="paymentform"></div>

</div>

<?php
$payment_html = ob_get_clean();
