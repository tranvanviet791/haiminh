<?php
ob_start();
global $sap;
?>
    <div id="checkout-method">

        <div style="<?php if ($current_user->ID) echo "display:none"; else echo ""; ?>" id="csl">
            <div class="row">
                <div class="col-md-6">
                    <form method="post" action="<?php the_permalink(); echo $sap; ?>checkout_register=register" id="registerform">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b><?php echo __("Register", "wpdm-premium-package"); ?></b></div>
                            <div class="panel-body">
                                <p><?php echo __('If you don\'t have an account signup here:', "wpdm-premium-package"); ?></p>
                                <div style="display: none;" id="rloading_first">
                                    <img src="<?php echo home_url(); ?>/wp-admin/images/loading.gif"/>
                                </div>
                                <div id="rloading_message"></div>

                                <input type="hidden" name="permalink" value="<?php the_permalink(); ?>"/>
                                <div class="form-group">
                                    <label><?php echo __("Username", "wpdm-premium-package"); ?></label>
                                    <input type="text" class="form-control" id="registerform_user_login"
                                           value="<?php echo isset($_SESSION['tmp_reg_info']['user_login']) ? $_SESSION['tmp_reg_info']['user_login'] : ''; ?>"
                                           name="wpdm_reg[user_login]" required/>
                                </div>
                                <div class="form-group">
                                    <label><?php echo __("E-mail", "wpdm-premium-package"); ?></label>
                                    <input type="text" class="email form-control" id="registerform_user_email"
                                           value="<?php echo isset($_SESSION['tmp_reg_info']['user_email']) ? $_SESSION['tmp_reg_info']['user_email'] : ''; ?>"
                                           name="wpdm_reg[user_email]" required/>
                                </div>
                                <div class="form-group">
                                    <label><?php echo __("Password", "wpdm-premium-package"); ?></label>
                                    <input type="password" class="form-control" id="registerform_user_pass"
                                           value="<?php echo isset($_SESSION['tmp_reg_info']['user_email']) ? $_SESSION['tmp_reg_info']['user_email'] : ''; ?>"
                                           name="wpdm_reg[user_pass]" required/>
                                </div>
                                <div class="form-group">
                                    <?php do_action("wpdm_register_form"); ?>
                                    <?php do_action("register_form"); ?>
                                </div>
                                <div id="rmsg"></div>
                            </div>

                            <div class="panel-footer">
                                <button id="register_btn" class="btn btn-success" type="submit"><?php echo __("Continue", "wpdm-premium-package"); ?></button>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form id="loginform" action="<?php the_permalink();
                    echo $sap; ?>&task=login" method="post" style="margin-top: 0 !important;">
                        <div class="panel panel-default">
                            <div class="panel-heading"><b><?php echo __("Login", "wpdm-premium-package"); ?></b></div>
                            <div class="panel-body">
                                <p><?php echo __('If you already have an account, login here:', "wpdm-premium-package"); ?></p>
                                <div style="display: none;" id="loading_first">
                                    <i class="fa fa-spin fa-refresh"></i>
                                </div>
                                <div id="loading_message"></div>

                                <input type="hidden" name="permalink" value="<?php the_permalink(); ?>"/>

                                <div class="form-group">
                                    <label for="loginform_user_login"><?php echo __("Username", "wpdm-premium-package"); ?></label>
                                    <input type="text" name="wpdm_login[log]" id="loginform_user_login" class="form-control" value="" required/>
                                </div>
                                <div class="form-group">
                                    <label for="loginform_user_pass"><?php echo __("Password", "wpdm-premium-package"); ?></label>
                                    <input type="password" name="wpdm_login[pwd]" id="loginform_user_pass" class="form-control" value="" required/>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="rememberme" type="checkbox" id="rememberme" value="forever"/> <?php echo __("Remember Me", "wpdm-premium-package"); ?>
                                    </label>
                                </div>
                                <input type="hidden" name="redirect_to" value="<?php the_permalink(); ?>"/>

                                <div id="lmsg"></div>

                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="wp-submit" id="loginbtn" class="btn btn-success"><?php echo __("Log In", "wpdm-premium-package"); ?></button>
                                <a class="pull-right" href="<?php echo home_url('wp-login.php?action=lostpassword'); ?>"><?php echo __('Forgot password?', "wpdm-premium-package"); ?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <script>
        jQuery('#registerform').validate({
            submitHandler: function (form) {
                jQuery(form).ajaxSubmit({
                    'url': '<?php echo home_url("/?checkout_register=register&wpmpnrd=1"); ?>',
                    'beforeSubmit': function () {
                        jQuery('#register_btn').attr('disabled', 'disabled').html('Please wait...');
                    },
                    'success': function (res) {

                        if (res.match(/success/)) {
                            // reload after succuessfull registration
                            //window.location.reload();
                            jQuery.post('<?php echo home_url('/?wpdmpp_load_pms=1'); ?>', function (res) {
                                jQuery('#checkout-method').slideUp();
                                jQuery('#checkout-method').after(res);
                            });
                        } else {
                            jQuery('#rmsg').html("<br/><div class='alert alert-danger'>" + res + "</div>");
                            jQuery('#register_btn').removeAttr('disabled').html('Continue');
                        }
                        return false;
                    }
                });
            }
        });

        jQuery('#loginform').validate({
            submitHandler: function (form) {
                jQuery(form).ajaxSubmit({
                    'url': '<?php
                        the_permalink();
                        echo $sap;
                        ?>checkout_login=login',
                    'beforeSubmit': function () {
                        jQuery('#loginbtn').attr('disabled', 'disabled').html('Please wait...');
                    },
                    'success': function (res) {
                        if (res.match(/success/)) {
                            // reload after succuessfull login
                            //window.location.reload();
                            jQuery.post('<?php echo home_url('/?wpdmpp_load_pms=1'); ?>', function (res) {
                                jQuery('#checkout-method').slideUp();
                                jQuery('#checkout-method').after(res);
                            });
                        } else if (res.match(/failed/)) {
                            jQuery('#lmsg').html("<br/><div class='alert alert-danger'>Username or Password is not correct!</div>");
                            jQuery('#loginbtn').removeAttr('disabled').html('Login');
                        }
                    }
                });
            }
        });
    </script>

<?php
$login_html = ob_get_clean();
