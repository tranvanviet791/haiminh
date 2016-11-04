<?php
if (isset($_POST['psub']))
    update_option("wpdmpp_payout_duration", $_POST['payout_duration']);

if (isset($_POST['csub']))
    update_option("wpdmpp_user_comission", $_POST['comission']);

if (isset($_POST['pschange'])) {
    global $wpdb;
    if ($_POST['payout_status'] != "-1" && $_POST['payout_status'] != "2") {
        if ($_POST['poutid']) {
            foreach ($_POST['poutid'] as $payout_id) {
                $wpdb->update(
                    "{$wpdb->prefix}ahm_withdraws",
                    array(
                        'status' => $_POST['payout_status']
                    ),
                    array('ID' => $payout_id),
                    array(
                        '%d',
                    ),
                    array('%d')
                );
            }
        }
    }

    if ($_POST['payout_status'] == "2") {
        if ($_POST['poutid']) {
            foreach ($_POST['poutid'] as $payout_id) {
                $wpdb->query("delete from {$wpdb->prefix}ahm_withdraws where id={$payout_id}");
            }
        }
    }
}

$payout_duration = get_option("wpdmpp_payout_duration");
$comission = get_option("wpdmpp_user_comission");
?>

<style>
    .nav-tabs {
        margin-bottom: 0 !important;
    }
    .w3eden {
        max-width: 95%;
    }
    .tab-content {
        background: #ffffff;
        border: 1px solid #dddddd;
        border-top: 0;
        padding: 20px;
    }
    table.widefat {
        border-radius: 4px;
        border-collapse: separate;
        overflow: hidden;
    }
    thead th, tfoot th {
        font-size: 9pt !important;
        text-transform: uppercase;
        font-weight: 900 !important;
    }
</style>

<div class="wrap payout-entries">
    <h2><?php echo __("Payouts", "wpdm-premium-package"); ?></h2>
    <div class="w3eden">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab"><?php echo __("All Payouts", "wpdm-premium-package"); ?></a></li>
            <li><a href="#tab2" data-toggle="tab"><?php echo __("Dues", "wpdm-premium-package"); ?></a></li>
            <li><a href="#tab3" data-toggle="tab"><?php echo __("Payout Settings", "wpdm-premium-package"); ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <?php include_once("payout-all.php"); ?>
            </div>
            <div class="tab-pane" id="tab2">
                <?php include_once("payout-dues.php"); ?>
            </div>
            <div class="tab-pane" id="tab3">
                <?php include_once("payout-settings.php"); ?>
            </div>
        </div>
    </div>
</div>