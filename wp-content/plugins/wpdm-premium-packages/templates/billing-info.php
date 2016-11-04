<div class="panel panel-default dashboard-panel">
    <div class="panel-heading"><b>Billing Address</b></div>
    <div class="panel-body">

        <div class="row row-fluid">
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_first_name"><?php echo __("First Name", "wpdm-premium-package"); ?> <i class="fa fa-star text-danger ttip" title="Required"></i></label>
                <input type="text" value="<?php if (isset($billing['first_name'])) echo $billing['first_name']; ?>" data-placeholder="First Name" id="billing_first_name" name="checkout[billing][first_name]" class="input-text required form-control">
                <span class="error help-block"></span>
            </div>
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_last_name"><?php echo __("Last Name", "wpdm-premium-package"); ?> <i class="fa fa-star text-danger ttip" title="Required"></i></label>
                <input type="text" value="<?php if (isset($billing['last_name'])) echo $billing['last_name']; ?>" data-placeholder="Last Name" id="billing_last_name" name="checkout[billing][last_name]" class="input-text required  form-control">
                <span class="error help-block"></span>
            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 span6">
                <label  class="" for="billing_company"><?php echo __("Company Name", "wpdm-premium-package"); ?></label>
                <input type="text" value="<?php if (isset($billing['company'])) echo $billing['company']; ?>" data-placeholder="Company (optional)" id="billing_company" name="checkout[billing][company]" class="input-text  form-control">
            </div>
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_address_1"><?php echo __("Address Line 1", "wpdm-premium-package"); ?> <i class="fa fa-star text-danger ttip" title="Required"></i></label>
                <input type="text" value="<?php if (isset($billing['address_1'])) echo $billing['address_1']; ?>" data-placeholder="Address" id="billing_address_1" name="checkout[billing][address_1]" class="input-text required  form-control">
                <span class="error help-block"></span>
            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 span6">
                <label  class="" for="billing_address_2"><?php echo __("Address Line 2", "wpdm-premium-package"); ?></label>
                <input type="text" value="<?php if (isset($billing['address_2'])) echo $billing['address_2']; ?>" data-placeholder="Address 2 (optional)" id="billing_address_2" name="checkout[billing][address_2]" class="input-text  form-control">
            </div>
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_city"><?php echo __("Town/City", "wpdm-premium-package"); ?> <i class="fa fa-star text-danger ttip" title="Required"></i></label>
                <input type="text" value="<?php if (isset($billing['city'])) echo $billing['city']; ?>" data-placeholder="Town/City" id="billing_city" name="checkout[billing][city]" class="input-text required  form-control">
                <span class="error help-block"></span>
            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_postcode"><?php echo __("Postcode/Zip", "wpdm-premium-package"); ?> <i class="fa fa-star text-danger ttip" title="Required"></i></label>
                <input type="text" value="<?php if (isset($billing['postcode'])) echo $billing['postcode']; ?>" data-placeholder="Postcode/Zip" id="billing_postcode" name="checkout[billing][postcode]" class="input-text required  form-control">
                <span class="error help-block"></span>
            </div>
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_country"><?php echo __("Country", "wpdm-premium-package"); ?> <i class="fa fa-star text-danger ttip" title="Required"></i></label>
                <?php
                global $wpdb;
                $countries = $wpdb->get_results("select * from {$wpdb->prefix}ahm_country order by country_name");
                ?>
                <select class="required form-control" id="billing_country" name="checkout[billing][country]">
                    <option value="">--Select a country--</option>
                    <?php

                    foreach ($countries as $country) {
                        if(isset($billing['country']) && $billing['country']==$country->country_code) {$selected=' selected="selected"';}
                        else {$selected="";}
                        if (isset($settings['allow_country'])) {
                            foreach ($settings['allow_country'] as $ac) {
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
                </select>
                <span class="error help-block"></span>
            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_state"><?php echo __("State/County", "wpdm-premium-package"); ?> <i class="fa fa-star text-danger ttip" title="Required"></i></label>
                <input type="text" id="billing_state" name="checkout[billing][state]" data-placeholder="State/County" value="<?php if (isset($billing['state'])) echo $billing['state']; ?>" class="input-text required  form-control">
                <span class="error help-block"></span>
            </div>
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_email"><?php echo __("Email Address", "wpdm-premium-package"); ?> <i class="fa fa-star text-danger ttip" title="Required"></i></label>
                <input type="text" value="<?php if (isset($billing['email'])) echo $billing['email']; ?>" data-placeholder="Email Address" id="billing_email" name="checkout[billing][email]" class="input-text required email  form-control">
                <span class="error help-block"></span>
            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_phone"><?php echo __("Phone", "wpdm-premium-package"); ?> <i class="fa fa-star text-danger ttip" title="Required"></i></label>
                <input type="text" value="<?php if (isset($billing['phone'])) echo $billing['phone']; ?>" data-placeholder="Phone" id="billing_phone" name="checkout[billing][phone]" class="input-text required  form-control">
                <span class="error help-block"></span>
            </div>
            <div class="form-group col-md-6 span6">
                <label class="" for="billing_tin"><?php echo __("Tax ID #", "wpdm-premium-package"); ?></label>
                <input type="text" value="<?php if (isset($billing['taxid'])) echo $billing['taxid']; ?>" data-placeholder="Tax ID" id="billing_tin" name="checkout[billing][taxid]" class="input-text required  form-control">
                <span class="error help-block"></span>
            </div>
        </div>

    </div>
</div>