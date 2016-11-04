<form action="" method="post">
<div class="panel panel-default">
    <div class="panel-heading"><b><?php echo __("Comissions","wpdm-premium-package");?></b></div>


     <table class="table table-striped">
     <tr><th align="left"><?php echo __("Role","wpdm-premium-package");?></th><th align="left"><?php echo __("Commission (%)","wpdm-premium-package");?></th></tr>
     <tr><td><?php echo __("Default","wpdm-premium-package");?> </td><td><input  class="form-control input-sm" style="width: 80px" type="text" size="8" name="comission[default]" value="<?php echo $comission['guest']; ?>"></td></tr>
         <?php
    global $wp_roles;
    $roles = array_reverse($wp_roles->role_names);
    foreach( $roles as $role => $name ) { 
    if(  isset($currentAccess) ) $sel = (in_array($role,$currentAccess))?'checked':'';
    ?>
    <tr><td><?php echo $name; ?> (<?php echo $role; ?>) </td><td><input type="text" class="form-control input-sm" style="width: 80px" size="8" name="comission[<?php echo $role; ?>]" value="<?php echo $comission[$role]; ?>"></td></tr>
    
    <?php } ?>  
    <tr><td colspan="2"><input type="submit" class="btn btn-primary" value="Submit" name="csub"></td></tr>
    </table>


</div>
</form>
<div class="panel panel-default">
    <div class="panel-heading"><b><?php echo __("Payout Duration","wpdm-premium-package");?></b></div>
    <div class="panel-body">
    <form action="" method="post">
    <?php echo __("Duration of payout to mature :","wpdm-premium-package");?> <input class="form-control input-sm" style="width: 80px;display: inline" type="text" name="payout_duration" value="<?php echo $payout_duration;?>" >  <?php echo __("Days","wpdm-premium-package");?>
     <br/><br/><input type="submit" class="btn btn-primary" name="psub" value="Submit">
    </form>    
    </div>
</div>

    