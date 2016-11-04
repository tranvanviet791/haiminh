<div class="panel panel-default dashboard-panel">
    <div class="panel-heading">
        <a href="<?php the_permalink(); ?>purchases/orders/" class="pull-right"><?php _e('All Orders', 'wpdm-premium-package'); ?></a><?php _e('Purchased Items', 'wpdm-premium-package'); ?>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th><?php _e('Product Name','wpdmpp'); ?></th>
                <th><?php _e('Price','wpdmpp'); ?></th>
                <th><?php _e('Order ID','wpdmpp'); ?></th>
                <th><?php _e('Purchase Date','wpdmpp'); ?></th>
                <th><?php _e('Download','wpdmpp'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php

        foreach($purchased_items as $item){ ?>

        <tr>
            <td><?php $title = get_the_title($item->pid); echo $title ? $title : '<span class="text-danger"><i class="fa fa-warning"></i> '.__('Product Deleted','wpdmpp').'</span>'; ?></td>
            <td><?php echo wpdmpp_currency_sign().number_format($item->price,2); ?></td>
            <td><a href="<?php the_permalink(); ?>purchases/order/<?php echo $item->oid; ?>/"><?php echo $item->oid; ?></a></td>
            <td><?php echo date(get_option('date_format'),$item->date); ?></td>
            <td>
                <?php if($item->order_status == 'Completed'){ ?>
                    <a href="<?php the_permalink(); ?>purchases/order/<?php echo $item->oid; ?>/" class="btn btn-xs btn-primary btn-block"><?php _e('Download','wpdmpp'); ?></a>
                <?php } else { ?>
                    <a href="<?php the_permalink(); ?>purchases/order/<?php echo $item->oid; ?>/" class="btn btn-xs btn-danger btn-block"><?php _e('Expired','wpdmpp'); ?></a>
                <?php } ?>
            </td>
        </tr>

        <?php } ?>
        </tbody>
    </table>
    <div class="panel-footer">
        <?php _e('If you are not seeing your purchased item:','wpdmpp'); ?> <a class="btn btn-warning btn-xs" style="color: #ffffff !important;" href="<?php the_permalink(); ?>purchases/orders/"><?php _e('Fix It Here','wpdmpp'); ?></a>
    </div>
</div>

