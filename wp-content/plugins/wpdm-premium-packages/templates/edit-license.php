<?php
global $wpdb;
?>
<div class="wrap">
    <div class="w3eden">
        <div class="panel panel-default">
            <div class="panel-heading"><?php _e('Edit License','wpdmpp'); ?></div>
            <div class="panel-body">

                <form method="post" action="" id="">
                    <input type="hidden" name="lid" value="<?php echo $_GET['id']; ?>">
                    <label>License No:</label><br>
                    <input id="title" style="width: 400px" type="text" disabled="disabled" value="<?php echo $license->licenseno; ?>"><br><br>
                    <label>Order ID:</label><br>
                    <input id="title" style="width: 150px" type="text" disabled="disabled" value="<?php echo $license->oid; ?>"><br><br>

                    <label>Status:</label><br>
                    <select style="width: 100px" name="license[status]">
                        <option value="1">Online</option>
                        <option value="0" <?php echo $license->status ? '' : 'selected=selected'; ?> >Offline</option>
                    </select><br><br>

                    <label>Domains: <span class="fa fa-info-circle ttip" title="One domain per line. Don't use 'http://' or 'www' only 'domain.com'"></span></label><br/><br>
                    <textarea cols="60" rows="6" name="license[domain]"><?php echo @implode("\n", @unserialize($license->domain)); ?></textarea><br><br>

                    <label>Activation Date:</label><br/>
                    <input type="text" name="license[activation_date]" value="<?php echo $license->activation_date ? date("Y-m-d", $license->activation_date) : ''; ?>" placeholder="YYYY-MM-DD"/><br><br>

                    <label>Expire Period:</label><br/>
                    <input type="text" size="5" name="license[expire_period]" value="<?php echo $license->expire_period; ?>"/> day(s)<br><br>

                    <input type="submit" class="button-primary" value="Save License"><br><br>
                </form>

            </div>
        </div>
    </div>
</div>