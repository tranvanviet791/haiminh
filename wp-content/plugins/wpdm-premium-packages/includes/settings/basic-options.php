<div class="wpdmpp-settings-fields">
    <input type="hidden" name="action" value="wpdmpp_save_settings">
    <?php
    global $wpdb;
    $countries = $wpdb->get_results("select * from {$wpdb->prefix}ahm_country order by country_name");
    ?>
    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Base Country', 'wpdmpp'); ?></div>
        <div class="panel-body">
            <select class="chosen" name="_wpdmpp_settings[base_country]">
                <option><?php _e('---Select Country---', 'wpdm-premium-package'); ?></option>
                <?php
                foreach ($countries as $country) {
                    $country->country_name = strtolower($country->country_name);
                    ?>
                    <option value="<?php echo $country->country_code; ?>" <?php selected(isset($settings['base_country']) ? $settings['base_country'] : '', $country->country_code ); ?> >
                        <?php echo ucwords($country->country_name); ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading"><?php _e("Allowed Countries", "wpdm-premium-package"); ?></div>
        <div class="panel-body">
            <ul id="listbox" style="height: 200px;overflow: auto;">
                <li>
                    <label for="allowed_cn"><input type="checkbox" name="allowed_cn_all" id="allowed_cn"/> <?php _e('Select All/None','wpdmpp'); ?> </label>
                </li>
                <?php
                foreach ($countries as $country) {
                    $country->country_name = strtolower($country->country_name);
                    ?>
                    <li>
                        <label><input <?php
                            $select = '';
                            if (isset($settings['allow_country'])) {
                                foreach ($settings['allow_country'] as $ac) {
                                    if ($ac == $country->country_code) {
                                        $select = 'checked="checked"';
                                        break;
                                    } else
                                        $select = '';
                                }
                            }
                            echo $select;
                            ?> type="checkbox" class="ccb" name="_wpdmpp_settings[allow_country][]"
                               value="<?php echo $country->country_code; ?>"><?php echo " " . ucwords($country->country_name); ?>
                        </label>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading"><?php _e("Frontend Settings", "wpdm-premium-package"); ?></div>
        <div class="panel-body">
            <label>
                <input type="checkbox" name="_wpdmpp_settings[billing_address]" <?php if (isset($settings['billing_address']) && $settings['billing_address'] == 1) echo 'checked=checked' ?>
                       value="1"> <?php _e("Ask Billing Address When Checkout", "wpdm-premium-package"); ?>
            </label><br/>
            <label>
                <input type="checkbox" name="_wpdmpp_settings[guest_checkout]" <?php if (isset($settings['guest_checkout']) && $settings['guest_checkout'] == 1) echo 'checked=checked' ?>
                          value="1"> <?php _e("Enable Guest Checkout", "wpdm-premium-package"); ?>
            </label><br/>
            <input type="hidden" name="_wpdmpp_settings[guest_download]" value="0">
            <label>
                <input type="checkbox" name="_wpdmpp_settings[guest_download]" <?php if (isset($settings['guest_download']) && $settings['guest_download'] == 1) echo 'checked=checked' ?>
                          value="1"> <?php _e("Enable Guest Download", "wpdm-premium-package"); ?>
            </label>
            <hr/>

            <label><?php _e("Cart Page :", "wpdm-premium-package"); ?></label><br>
            <?php
            if ($settings['page_id'])
                $args = array(
                    'show_option_none' => __('None Selected','wpdm-premium-package'),
                    'name' => '_wpdmpp_settings[page_id]',
                    'selected' => $settings['page_id']
                );
            else
                $args = array(
                    'show_option_none' => __('None Selected','wpdm-premium-package'),
                    'name' => '_wpdmpp_settings[page_id]'
                );
            wp_dropdown_pages($args);
            ?>
            <hr/>

            <label><?php _e("Orders Page :", "wpdm-premium-package"); ?></label><br>
            <?php
            if (isset($settings['orders_page_id']))
                $args = array(
                    'name' => '_wpdmpp_settings[orders_page_id]',
                    'show_option_none' => __('None Selected','wpdm-premium-package'),
                    'selected' => $settings['orders_page_id']
                );
            else
                $args = array(
                    'show_option_none' => __('None Selected','wpdm-premium-package'),
                    'name' => '_wpdmpp_settings[orders_page_id]'
                );
            wp_dropdown_pages($args);
            ?>
            <hr/>

            <label><?php _e("Guest Order Page :", "wpdm-premium-package"); ?></label><br>
            <?php
            if (isset($settings['guest_order_page_id']))
                $args = array(
                    'name' => '_wpdmpp_settings[guest_order_page_id]',
                    'show_option_none' => __('None Selected','wpdm-premium-package'),
                    'selected' => $settings['guest_order_page_id']
                );
            else
                $args = array(
                    'show_option_none' => __('None Selected','wpdm-premium-package'),
                    'name' => '_wpdmpp_settings[guest_order_page_id]'
                );
            wp_dropdown_pages($args);
            ?>
            <hr/>

            <label><?php _e("Continue Shopping URL:", "wpdm-premium-package"); ?></label><br/>
            <input type="text" class="form-control" name="_wpdmpp_settings[continue_shopping_url]" size="50" id="continue_shopping_url" value="<?php echo $settings['continue_shopping_url'] ?>"/>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e("Purchase Settings", "wpdm-premium-package"); ?></div>
        <div class="panel-body">
            <label>
                <input type="checkbox" name="_wpdmpp_settings[license_key_validity]" <?php if (isset($settings['license_key_validity']) && $settings['license_key_validity'] == 1) echo 'checked=checked' ?>
                          value="1"> <?php echo __("Keep License Key Valid for Expired Orders", "wpdm-premium-package"); ?>
            </label><br/>
            <label>
                <input type="checkbox" name="_wpdmpp_settings[order_expiry_alert]" <?php if (isset($settings['order_expiry_alert']) && $settings['order_expiry_alert'] == 1) echo 'checked=checked' ?>
                          value="1"> <?php echo __("Send Order Expiration Alert to Customer", "wpdm-premium-package"); ?>
            </label><br/><br/>

            <label><?php _e("Order Validity Period:", "wpdm-premium-package"); ?></label><br>
            <div class="input-group col-md-5">
                <input type="text" class="form-control" value="<?php echo (isset($settings['order_validity_period'])) ? $settings['order_validity_period'] : 365; ?>"
                       name="_wpdmpp_settings[order_validity_period]"/>
                <span class="input-group-addon"><?php _e('Days','wpdmpp'); ?></span>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Invoice', 'wpdm-premium-package'); ?></div>
        <div class="panel-body">
            <div class="form-group">
                <label for="invoice-logo"><?php _e('Invoice Logo URL','wpdmpp'); ?></label>
                <div class="input-group">
                    <input type="text" name="_wpdmpp_settings[invoice_logo]" id="invoice-logo" class="form-control" value="<?php echo isset($settings['invoice_logo']) ? $settings['invoice_logo'] : ''; ?>"/>
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-media-upload" type="button" rel="#invoice-logo"><i class="fa fa-picture-o"></i></button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label for="company-address"><?php _e('Company Address', 'wpdm-premium-package'); ?></label>
                <textarea class="form-control" name="_wpdmpp_settings[invoice_company_address]" id="company-address"><?php echo isset($settings['invoice_company_address']) ? $settings['invoice_company_address'] : ''; ?></textarea>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Miscellaneous', 'wpdm-premium-package'); ?></div>
        <div class="panel-body">
            <label>
            <input type="checkbox" name="_wpdmpp_settings[disable_fron_end_css]" id="disable_fron_end_css"
                   value="1" <?php if (isset($settings['disable_fron_end_css']) && $settings['disable_fron_end_css'] == 1) echo "checked='checked'"; ?>> <?php _e("Disable plugin CSS from front-end", "wpdm-premium-package"); ?>
            </label><br>

            <label>
            <input type="checkbox" name="_wpdmpp_settings[wpdmpp_after_addtocart_redirect]" id="wpdmpp_after_addtocart_redirect"
                   value="1" <?php if ( isset($settings['wpdmpp_after_addtocart_redirect']) &&  $settings['wpdmpp_after_addtocart_redirect'] == 1 ) echo "checked='checked'"; ?>>
                <?php _e("Redirect to shopping cart after a product is added to the cart", "wpdm-premium-package"); ?>
            </label>
        </div>
    </div>
</div>

<style>
    .w3eden input[type="radio"], .w3eden input[type="checkbox"] {
        line-height: normal;
        margin: -2px 0 0;
    }
    .panel-body label{
        font-weight: 400 !important;
    }
    .wpdmpp-settings-fields{
        margin-top: 20px;
    }
</style>