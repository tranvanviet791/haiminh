<?php

if( !is_admin()):
    $task = get_query_var('adb_page');
    $task = explode("/", $task);
    if($task[0] == 'edit-package') $pid = $task[1];

    if(isset($pid))
        $post = get_post($pid);
    else {
        $post = new stdClass();
        $post->ID = 0;
        $post->post_title = '';
        $post->post_content = '';
    }
endif;

?>
<div class="w3eden">
    <div class="row">
        <div class="col-md-6 wpdm-full-front">
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo __('Pricing','wpdm-premium-package'); ?></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="base-price-field"><?php echo __('Base Price','wpdm-premium-package'); ?></label>
                            <input type="text" size="16" class="form-control" id="price_label1" name="file[base_price]"  value="<?php $base_price = get_post_meta($post->ID,'__wpdm_base_price',true); if(isset($base_price)) echo number_format((double)$base_price,2,'.','');?>">
                        </div>
                        <div class="col-md-6">
                            <label for="base-price-field"><?php echo __('Sales Price','wpdm-premium-package'); ?></label>
                            <input type="text" class="form-control" size="16" id="price_label2" name="file[sales_price]"  value="<?php $sales_price = get_post_meta($post->ID,'__wpdm_sales_price',true); if(isset($sales_price)) echo number_format((double)$sales_price,2,'.','');?>">
                        </div>
                    </div>
                </div>
                <?php $price_variation = get_post_meta($post->ID,'__wpdm_price_variation',true);  ?>
                <div class="panel-heading">
                    <label>
                        <input style="margin: 0;line-height: 10px" type="checkbox" <?php if($price_variation!='') echo "checked='checked'"; else echo "";?> name="file[price_variation]" id="price_variation" name="price_variation" > <?php echo __('Activate Variable Pricing','wpdm-premium-package'); ?>
                    </label>
                </div>
                <div id="price_dis_table" style="<?php if($price_variation != '') echo ""; else echo "display: none;";?>">
                <div class="panel-body">
                    <div id="vdivs">
                        <?php
                        $variation =  get_post_meta($post->ID,'__wpdm_variation',true);
                        if(is_array($variation)){
                            foreach($variation as $key=>$vname){?>
                                <div id="variation_div_<?php echo $key;?>" class="panel panel-default">
                                    <div class="panel-heading">
                                        <?php _e('Variation ID#','wpdm-premium-package');  ?> <?php echo $key;?>
                                        <a class="delet_vdiv pull-right" rel="variation_div_<?php echo $key;?>" title="delete this variation"><i class="fa fa-times-circle text-danger"></i></a>
                                    </div>
                                    <table class="table table-v" id="voption_table_<?php echo $key;?>">
                                        <tr><td colspan="3"><label><input style="margin: 0" type="checkbox" name="file[variation][<?php echo $key;?>][multiple]" placeholder="Multiple Select" <?php if(isset($vname['multiple'])) echo "checked='checked'"; ?> > &nbsp;<?php echo __('Multiple Select','wpdm-premium-package'); ?></label></td></tr>
                                        <tr><td colspan="3"><input class="form-control" type="text" name="file[variation][<?php echo $key;?>][vname]" id="" placeholder="<?php _e('Variation Name','wpdm-premium-package');  ?>" title="<?php _e('Enter a Variation Name','wpdm-premium-package');  ?>" value="<?php echo $vname['vname'];?>"></td></tr>
                                        <tr><th><?php _e('Option Name','wpdm-premium-package'); ?></th><th><?php _e('Extra Cost','wpdm-premium-package'); ?></th><th><?php _e('Delete','wpdm-premium-package'); ?></th></tr>
                                        <?php
                                        if($vname){
                                            foreach($vname as $optionkey=>$optionval){
                                                if($optionkey!="vname" && $optionkey != "multiple"){?>
                                                    <tr id="voption<?php echo $optionkey;?>"><td><input type="text" name="file[variation][<?php echo $key;?>][<?php echo $optionkey;?>][option_name]"  placeholder="Option Name" class="form-control input-sm" value="<?php echo $optionval['option_name'];?>"></td><td><div class="input-group input-group-sm"><span class="input-group-addon"><i class="fa fa-plus-circle"></i></span><input style="max-width: 70px" min="0" name="file[variation][<?php echo $key;?>][<?php echo $optionkey;?>][option_price]" id="" size="5" class="form-control" type="number" placeholder="price" value="<?php echo $optionval['option_price'];?>"></div></td><td><i class="delet_voption fa fa-times-circle text-danger" rel="voption<?php echo $optionkey;?>" title="Delete this option" style="cursor:pointer"></i></td></tr>
                                                <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </table>
                                    <div style="clear: both;"></div>
                                    <div class="panel-footer">
                                    <input type="button" class="btn btn-default btn-sm add_voption" rel="<?php echo $key;?>" value="<?php _e('Add Option','wpdmpp'); ?>">
                                    </div>
                                </div>
                            <?php
                            }
                        } ?>
                    </div>
                </div>
                <div class="panel-footer"><input type="button" class="btn btn-primary" id="add_variation" value="<?php _e('Add Variation','wpdmpp'); ?>"></div>
                </div>
            </div>

            <script type="text/javascript">
                jQuery('#price_variation').click(function(){
                    if(jQuery('#price_variation').attr("checked")){
                        jQuery('#variation_heading').text("Variation Options");
                        jQuery('#price_dis_table').show();

                    }else{
                          jQuery('#variation_heading').text("Pricing");
                          jQuery('#price_dis_table').hide()  ;
                    }
                });
                jQuery('#add_variation').on("click", function (){
                    var tm=new Date().getTime();
                    jQuery('#vdivs').append('<div id="variation_div_'+tm+'" class="panel panel-default"><div class="panel-heading"><?php _e('Variation ID','wpdm-premium-package'); ?># '+tm+'<a class="delet_vdiv pull-right" rel="variation_div_'+tm+'" title="delete this variation"><i class="fa fa-times-circle text-danger"></i></a></div><table class="table table-v" id="voption_table_'+tm+'"><tr><td colspan="3"><label><input type="checkbox" style="margin: 0 !important;" name="file[variation]['+tm+'][multiple]"> <?php _e('Multiple Select','wpdm-premium-package'); ?></label></td></tr><tr><td colspan="3"><input type="text" name="file[variation]['+tm+'][vname]" id="" class="form-control" placeholder="Variation Name"></td></tr><tr><th>Option Name</th><th>Extra Cost</th><th>Delete</th></tr><tr id="voption_'+tm+'"><td><input type="text" name="file[variation]['+tm+']['+tm+'][option_name]" id="" placeholder="Option Name" class="form-control input-sm"></td><td><div class="input-group input-group-sm"><span class="input-group-addon"><i class="fa fa-plus-circle"></i></span><input type="number" class="form-control" style="max-width: 70px" min=0 name="file[variation]['+tm+']['+tm+'][option_price]" id="" placeholder="Price"></div></td><td><i class="delet_voption fa fa-times-circle text-danger" rel="voption_'+tm+'" title="delete this option" alt="" style="cursor:pointer"></i></td></tr></table><div style="clear: both;"></div><div class="panel-footer"><input type="button" class="btn btn-default btn-sm add_voption" rel="'+tm+'" value="Add Option"></div></div>');
                });
                jQuery('.delet_vdiv').on("click", function(){
                    if(confirm("Are you sure to remove"))
                        jQuery('#'+jQuery(this).attr("rel")).remove();
                });
                jQuery('body').on("click", '.add_voption' , function (){
                    var tm=new Date().getTime();
                    jQuery('#voption_table_'+jQuery(this).attr("rel")).append('<tr id="voption_'+tm+'"><td><input type="text" name="file[variation]['+jQuery(this).attr("rel")+']['+tm+'][option_name]"  placeholder="Option Name" class="form-control input-sm"></td><td><div class="input-group input-group-sm"><span class="input-group-addon"><i class="fa fa-plus-circle"></i></span><input type="number" name="file[variation]['+jQuery(this).attr("rel")+']['+tm+'][option_price]" size="5" id="" placeholder="Price" class="form-control" style="max-width:70px"></div></td><td><i class="delet_voption fa fa-times-circle text-danger" rel="voption_'+tm+'" title="delete this option" alt="" style="cursor:pointer"></i></td></tr>');
                });

                jQuery('body').on("click", '.delet_voption', function(){
                    if(confirm("Are you sure to remove"))
                        jQuery('#'+jQuery(this).attr("rel")).remove();
                });
            </script>

            <!-- Tick to Enable Licensing For this package -->
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('Licensing Option','wpdm-premium-package'); ?></div>
                <div class="panel-body">
                    <label>
                        <input type="checkbox" style="margin: 0 !important;" value="1" name="file[enable_license]" <?php if(get_post_meta($post->ID, "__wpdm_enable_license", true)==1) echo 'checked="checked"'; ?> > &nbsp;<?php _e('License Key Required','wpdm-premium-package'); ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-6  wpdm-full-front">
            <div id="wpdmpp_discount">
                <?php if(is_admin()){ ?>
                <div class="panel panel-default">
                    <div class="panel-heading">Role Based Discount</div>
                    <?php $discount = get_post_meta($post->ID, '__wpdm_discount', true);  ?>
                    <table class="table table-v">
                        <tr>
                            <th align="left">Role</th>
                            <th align="left">Discount (%)</th>
                        </tr>
                        <?php
                        global $wp_roles;
                        $roles = array_reverse($wp_roles->role_names);
                        foreach( $roles as $role => $name ) {
                        if(  isset($currentAccess) ) $sel = ( in_array($role,$currentAccess) ) ? 'checked' : '';
                        ?>
                        <tr>
                            <td><?php echo $name; ?> (<?php echo $role; ?>) </td>
                            <td><input class="form-control input-sm" style="width: 70px" type="text" size="8" name="file[discount][<?php echo $role; ?>]" value="<?php if(isset($discount[$role])) echo $discount[$role]; ?>"></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <?php } ?>
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e('Coupon Discount','wpdm-premium-package'); ?></div>
                    <table id="coupon_table" class="table table-v">
                        <thead>
                            <tr>
                                <th align="left"><?php _e('Coupon Code','wpdm-premium-package'); ?></th>
                                <th align="left" style="width: 80px"><?php _e('Discount(%)','wpdm-premium-package'); ?></th>
                                <th style="width: 50px"><?php _e('Delete','wpdm-premium-package'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $coupon_code = get_post_meta($post->ID, '__wpdm_coupon_code', true);
                        $coupon_discount = get_post_meta($post->ID, '__wpdm_coupon_discount', true);
                        if(is_array($coupon_code) && count($coupon_code)>0){
                            foreach($coupon_code as $coupon_key=>$coupon_val){ ?>
                                <tr id="coupon-<?php echo $coupon_key; ?>">
                                    <td> <input class="form-control input-sm" type="text" size="8"  name="file[coupon_code][<?php echo $coupon_key?>]"  value="<?php echo $coupon_code[$coupon_key];?>"></td>
                                    <td><input class="form-control input-sm" type="text" size="8" name="file[coupon_discount][<?php echo $coupon_key?>]"  value="<?php echo $coupon_discount[$coupon_key];?>"></td>
                                    <td><i class="delete_pp_coupon fa fa-times-circle text-danger" rel="coupon-<?php echo $coupon_key; ?>"></i></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><input type="text" class="form-control input-sm" size="16" id="coupon_code"  value=""></td>
                                <td><input class="form-control input-sm" type="text" size="8" id="coupon_discount"  value=""></td>
                                <td><input class="btn btn-default btn-sm" type="button" size="8" id="add_coupon" value="Add"></td>
                            </tr>
                        </tfoot>
                    </table>

                    <script type="text/javascript">

                        var cdtm = new Date().getTime();
                        jQuery('#add_coupon').on("click", function(){
                            var coupon_code = jQuery('#coupon_code').val();
                            var coupon_discount = jQuery('#coupon_discount').val();
                            jQuery('#coupon_table tbody').append('<tr><td width="250px"> <input size="8" type="text" name="file[coupon_code]['+cdtm+']" value="'+coupon_code+'" class="form-control input-sm"></td><td><input class="form-control input-sm" type="text" size="8" name="file[coupon_discount]['+cdtm+']" value="'+coupon_discount+'"></td><td><i class="fa fa-times-circle"></i></td></tr>');
                            jQuery('#coupon_code').val("");
                            jQuery('#coupon_discount').val("");
                        });

                        jQuery('.delete_pp_coupon').on("click", function(){
                            if(confirm("Are you sure to remove?"))
                                jQuery('#'+jQuery(this).attr("rel")).remove();
                        });

                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="clear: both;"></div>