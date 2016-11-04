<?php
    global $current_user, $wpdb;
    $user = $wpdb->get_row("select * from {$wpdb->prefix}users where ID=".$current_user->ID);
    $billing_shipping=unserialize(get_user_meta($current_user->ID, 'user_billing_shipping',true));
    if(!isset($settings)) $settings = array();
    if(is_array($billing_shipping))
        extract($billing_shipping);
     
?>
<div class="my-profile wp-marketplace">

    <ul style="display: block; list-style: none;">
       
        <?php if(isset($_SESSION['member_error'])){ ?>
        <li class="col-md-11"><div class="alert alert-warning"><b>Save Failed!</b><br/><?php echo implode('<br/>',$_SESSION['member_error']); unset($_SESSION['member_error']); ?></div></li>
        <?php } ?>
        <?php if(isset($_SESSION['member_success'])){ ?>
        <li class="col-md-11"><div class="alert alert-success"><b>Done!</b><br/><?php echo $_SESSION['member_success']; unset($_SESSION['member_success']); ?></div></li>
        <?php } ?>
    </ul>
<div style="clear: both;margin-top:20px ;"></div>    
<div id="form" class="form profile-form">

<form method="post" id="validate_form" class="wpmp-edit-profile-form" name="contact_form" action="">
    <input type="hidden" name="dact" value="update-profile" />
    
    
    <div class="panel panel-default">
        <div class="panel-heading"><b>Basic Info</b></div>
     <div class="panel-body">   
    <div class="row row-fluid">
        <div class="form-group col-md-6 span6">
            <label for="name">Your name: </label>
            <input type="text" class="required form-control col-sm-6" value="<?php echo $user->display_name;?>" name="profile[display_name]" id="name">
        </div>
        <div class="form-group col-md-6 span6">
            <label for="email">Your Email:</label>
            <input type="text" class="required form-control" value="<?php echo $user->user_email;?>" name="profile[user_email]" id="email">
        </div>
    </div>
    
    <div class="row row-fluid">
        <div class="form-group col-md-6 span6">
            <label for="phone">Phone Number: </label>
            <input type="text" class="required form-control" value="<?php echo get_user_meta($current_user->ID,'phone',true);?>" name="phone" id="phone">
        </div>
        <div class="form-group col-md-6 span6">
            <label for="company_name">PayPal Account: </label>
            <input type="text" class="form-control" value="<?php echo get_user_meta($current_user->ID,'payment_account',true);?>" name="payment_account" id="payment_account" placeholder="Add paypal or moneybookers email here">
        </div>
    </div>
    
    <div class="row row-fluid">
        <div class="form-group col-md-6 span6">
            <label for="new_pass">New Password: </label>
            <input placeholder="Use nothing if you don't want to change old password" type="password" value="" name="password" id="new_pass" class=" form-control">
        </div>
        <div class="form-group col-md-6 span6">
            <label for="re_new_pass">Re-type New Password: </label>
            <input type="password" value="" name="cpassword" id="re_new_pass" class=" form-control">
        </div>
    </div>
    
    <div class="row row-fluid">
        <div class="form-group col-md-12 span12">
            <label for="message">Description:</label>
            <textarea class="required form-control" cols="40" rows="8" name="profile[description]" id="message"><?php echo htmlspecialchars(stripslashes($current_user->description));?></textarea>
        </div>
    </div>
     </div>
    </div>
    
    <?php include(dirname(__FILE__).'/billing-info.php'); ?>

    <div class="row row-fluid">
        <div class="col-md-12 span12">
            <button type="submit" class="btn btn-large btn-primary" id="billing_btn"><i class="icon-ok icon-white"></i> Save Changes</button>
        </div>
    </div>

</form>
</div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
             
        $('span.error').css('color','red');
        
        $('#billing_btn').click(function(){
            //alert('1');
            var go = false;
            if($.trim($("#billing_first_name").val())==""){
                go = true;
                $("#billing_first_name").parent().find('.error').html("Please Enter Your First Name");
            }
            else{
                $("#billing_first_name").parent().find('.error').html("");
            }
            
            if($.trim($("#billing_last_name").val())==""){
                go = true;
                $("#billing_last_name").parent().find('.error').html("Please Enter Your Last Name");
            }
            else{
                $("#billing_last_name").parent().find('.error').html("");
            }
            
            if($.trim($("#billing_address_1").val())==""){
                go = true;
                $("#billing_address_1").parent().find('.error').html("Please Enter Your Address");
            }
            else{
                $("#billing_address_1").parent().find('.error').html("");
            }
            
            if($.trim($("#billing_city").val())==""){
                go = true;
                $("#billing_city").parent().find('.error').html("Please Enter Your City");
            }
            else{
                $("#billing_city").parent().find('.error').html("");
            }
            
            if($.trim($("#billing_postcode").val())==""){
                go = true;
                $("#billing_postcode").parent().find('.error').html("Please Enter Your Postcode");
            }
            else{
                $("#billing_postcode").parent().find('.error').html("");
            }
            
            if($.trim($("#billing_country").val())==""){
                go = true;
                $("#billing_country").parent().find('.error').html("Please Enter Your Country");
            }
            else{
                $("#billing_country").parent().find('.error').html("");
            }
            
            if($.trim($("#billing_state").val())==""){
                go = true;
                $("#billing_state").parent().find('.error').html("Please Enter Your State");
            }
            else{
                $("#billing_state").parent().find('.error').html("");
            }
            
            if($.trim($("#billing_email").val())==""){
                go = true;
                $("#billing_email").parent().find('.error').html("Please Enter Your Email Address");
            }
            else{
                $("#billing_email").parent().find('.error').html("");
            }
            
            if($.trim($("#billing_phone").val())==""){
                go = true;
                $("#billing_phone").parent().find('.error').html("Please Enter Your Phone Number");
            }
            else{
                $("#billing_phone").parent().find('.error').html("");
            }
            
            
            if(go==false) return true;
            
            else return false;
        });
        
    });
    
</script>