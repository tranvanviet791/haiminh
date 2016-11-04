<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if( ! class_exists( 'BillingInfo' ) ):

    class BillingInfo{

        function __construct()
        {
            add_action( 'show_user_profile', array( $this, 'wpdmpp_add_billing_info_fields' ) );
            add_action( 'edit_user_profile', array( $this, 'wpdmpp_add_billing_info_fields' ) );
            add_action( 'personal_options_update', array( $this, 'wpdmpp_save_billing_info_fields' ) );
            add_action( 'edit_user_profile_update', array( $this, 'wpdmpp_save_billing_info_fields' ) );
        }

        function wpdmpp_add_billing_info_fields($user){
            global $wpdmpp_settings;
            $billing_shipping = unserialize(get_user_meta($user->ID, 'user_billing_shipping',true));
            if(is_array($billing_shipping))
                extract($billing_shipping);
            ?>
            <h3><?php _e('Customer Billing Address'); ?></h3>

            <table class="form-table">
                <tr>
                    <th>
                        <label for="billing_first_name"><?php echo __("Billing First Name", "wpdm-premium-package"); ?></label>
                    </th>
                    <td>
                        <input type="text" name="checkout[billing][first_name]" id="billing_first_name" value="<?php if ($billing['first_name']) echo $billing['first_name']; ?>" class="regular-text" /><br />
                        <span class="description"><?php _e('Enter your billing first name.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="billing_last_name"><?php echo __("Last Name", "wpdm-premium-package"); ?></label>
                    </th>
                    <td>
                        <input type="text" value="<?php if ($billing['last_name']) echo $billing['last_name']; ?>" placeholder="Last Name" id="billing_last_name" name="checkout[billing][last_name]" class="regular-text"><br />
                        <span class="description"><?php _e('Enter your billing last name.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="billing_company"><?php echo __("Company Name", "wpdm-premium-package"); ?></label>
                    </th>
                    <td>
                        <input type="text" value="<?php if ($billing['company']) echo $billing['company']; ?>" placeholder="Company (optional)" id="billing_company" name="checkout[billing][company]" class="regular-text"><br />
                        <span class="description"><?php _e('Enter your company name.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label class="" for="billing_address_1"><?php echo __("Address Line 1", "wpdm-premium-package"); ?> </label>
                    </th>
                    <td>
                        <input type="text" value="<?php if ($billing['address_1']) echo $billing['address_1']; ?>" placeholder="Address" id="billing_address_1" name="checkout[billing][address_1]" class="regular-text"><br />
                        <span class="description"><?php _e('Enter your billing address line 1.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label  class="" for="billing_address_2"><?php echo __("Address Line 2", "wpdm-premium-package"); ?></label>
                    </th>
                    <td>
                        <input type="text" value="<?php if ($billing['address_2']) echo $billing['address_2']; ?>" placeholder="Address 2 (optional)" id="billing_address_2" name="checkout[billing][address_2]" class="regular-text"><br />
                        <span class="description"><?php _e('Enter your billing address line 2.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label class="" for="billing_city"><?php echo __("Town/City", "wpdm-premium-package"); ?> </label>
                    </th>
                    <td>
                        <input type="text" value="<?php if ($billing['city']) echo $billing['city']; ?>" placeholder="Town/City" id="billing_city" name="checkout[billing][city]" class="regular-text"><br />
                        <span class="description"><?php _e('Enter your billing city name.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label class="" for="billing_postcode"><?php echo __("Postcode/Zip", "wpdm-premium-package"); ?> </label>
                    </th>
                    <td>
                        <input type="text" value="<?php if ($billing['postcode']) echo $billing['postcode']; ?>" placeholder="Postcode/Zip" id="billing_postcode" name="checkout[billing][postcode]" class="regular-text"><br />
                        <span class="description"><?php _e('Enter your billing post code.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>


                <tr>
                    <th>
                        <label class="" for="billing_country"><?php echo __("Country", "wpdm-premium-package"); ?> </label>
                    </th>
                    <td>
                        <?php
                        global $wpdb;
                        $countries = $wpdb->get_results("select * from {$wpdb->prefix}ahm_country order by country_name");
                        ?>
                        <select class="select" id="billing_country" name="checkout[billing][country]">
                            <option value="">--Select a country--</option>
                            <?php
                            foreach ($countries as $country) {
                                if($billing['country'] == $country->country_code) {
                                    $selected = ' selected="selected"';}
                                else {
                                    $selected = "";
                                }
                                if (isset($wpdmpp_settings['allow_country'])) {
                                    foreach ($wpdmpp_settings['allow_country'] as $ac) {
                                        if ($ac == $country->country_code) {
                                            echo '<option value="' . $country->country_code . '"'.$selected.'>' . $country->country_name . '</option>';
                                            break;
                                        }
                                    }
                                } else {
                                    echo '<option value="' . $country->country_code . '" '.$selected.'>' . $country->country_name . '</option>';
                                }
                            }
                            ?>
                        </select> <br />
                        <span class="description"><?php _e('Enter your billing country name.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label class="" for="billing_state"><?php echo __("State/County", "wpdm-premium-package"); ?> </label>
                    </th>
                    <td>
                        <input type="text" id="billing_state" name="checkout[billing][state]" placeholder="State/County" value="<?php if ($billing['state']) echo $billing['state']; ?>" class="regular-text"><br />
                        <span class="description"><?php _e('Enter your billing state.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label class="" for="billing_email"><?php echo __("Email Address", "wpdm-premium-package"); ?> </label>
                    </th>
                    <td>
                        <input type="text" value="<?php if (isset($billing['order_email'])) echo $billing['order_email']; ?>" placeholder="Email Address" id="billing_email" name="checkout[billing][order_email]" class="regular-text email"><br />
                        <span class="description"><?php _e('Enter your billing email address.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label class="" for="billing_phone"><?php echo __("Phone", "wpdm-premium-package"); ?> </label>
                    </th>
                    <td>
                        <input type="text" value="<?php if ($billing['phone']) echo $billing['phone']; ?>" placeholder="Phone" id="billing_phone" name="checkout[billing][phone]" class="regular-text"><br />
                        <span class="description"><?php _e('Enter your billing phone number.', 'wpdm-premium-package'); ?></span>
                    </td>
                </tr>
            </table>

            <?php
        }

        function wpdmpp_save_billing_info_fields($user_id){
            if ( !current_user_can( 'edit_user', $user_id ) )
                return false;
            update_user_meta($user_id, 'user_billing_shipping', serialize($_POST['checkout']));
        }

    }

endif;

new BillingInfo();
?>
