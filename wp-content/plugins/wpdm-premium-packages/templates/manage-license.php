<?php
global $wpdb;
?>
<div class="wrap">
    <h2><?php _e('Licenses', 'wpdmpp'); ?></h2>

    <form method="get" action="edit.php" id="posts-filter">
        <input type="hidden" name="post_type" value="wpdmpro">
        <input type="hidden" name="page" value="pp-license">
        <div class="tablenav">

            <div class="alignleft actions">
                <label for="oid"><?php _e('Order ID:','wpdmpp'); ?></label>
                <input type="text" name="oid" value="<?php echo isset($_REQUEST['oid']) ? $_REQUEST['oid'] : ''; ?>">
                <label for="licenseno"><?php _e('License No:','wpdmpp'); ?></label>
                <input type="text" name="licenseno" value="<?php echo isset($_REQUEST['licenseno']) ? $_REQUEST['licenseno'] : ''; ?>">
                <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Apply">
                | <b><?php echo $t; ?> <?php _e('license(s) found','wpdmpp'); ?></b>
            </div>
            <br class="clear">
        </div>

        <div class="clear"></div>

        <table cellspacing="0" class="widefat fixed">
            <thead>
            <tr>
                <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
                <th style="" class="manage-column column-media" id="media" scope="col"><?php _e('License Key','wpdmpp'); ?></th>
                <th style="" class="manage-column column-author" id="author" scope="col"><?php _e('Product Name','wpdmpp'); ?></th>
                <th style="" class="manage-column column-author" id="author" scope="col"><?php _e('Order ID','wpdmpp'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Activation Date','wpdmpp'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Expire Date','wpdmpp'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Status','wpdmpp'); ?></th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
                <th style="" class="manage-column column-media" id="media" scope="col"><?php _e('License Key','wpdmpp'); ?></th>
                <th style="" class="manage-column column-author" id="author" scope="col"><?php _e('Product Name','wpdmpp'); ?></th>
                <th style="" class="manage-column column-author" id="author" scope="col"><?php _e('Order ID','wpdmpp'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Activation Date','wpdmpp'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Expire Date','wpdmpp'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Status','wpdmpp'); ?></th>
            </tr>
            </tfoot>

            <tbody class="list:post" id="the-list">
            <?php
            foreach ($licenses as $i => $license) {

                ?>
                <tr valign="top" class="<?php if ($i % 2 == 0) echo 'alternate'; ?> author-self status-inherit" id="post-8">
                    <th class="check-column" scope="row"><input type="checkbox" value="8" name="id[]"></th>
                    <td class="media column-media">
                        <strong>
                            <a title="Edit" href="edit.php?post_type=wpdmpro&page=pp-license&task=editlicense&id=<?php echo $license->id; ?>"><?php echo $license->licenseno; ?></a>
                        </strong>
                    </td>
                    <td class="author column-author"><?php echo $license->productname; ?></td>
                    <td class="author column-author">
                        <a target="_blank" href="edit.php?post_type=wpdmpro&page=orders&task=vieworder&id=<?php echo $license->oid; ?>"><?php echo $license->oid; ?></a>
                    </td>
                    <td class="parent column-parent"><?php echo $license->activation_date ? date("Y-m-d", $license->activation_date) : 'Inactive'; ?></td>
                    <td class="parent column-parent"><?php echo $license->expire_date ? date("Y-m-d", $license->expire_date) : 'Inactive'; ?></td>
                    <td class="parent column-parent"><?php echo $license->status ? 'Online' : 'Offline'; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php
        $page_links = paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($t / $l),
            'current' => $p
        ));
        ?>

        <div id="ajax-response"></div>
        <div class="tablenav">
            <?php if ($page_links) { ?>
                <div class="tablenav-pages">
                    <?php $page_links_text = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s') . '</span>%s',
                        number_format_i18n(($_GET['paged'] - 1) * $l + 1),
                        number_format_i18n(min($_GET['paged'] * $l, $t)),
                        number_format_i18n($t),
                        $page_links
                    );
                    echo $page_links_text; ?></div>
            <?php } ?>

            <div class="alignleft actions">
                <input type="submit" class="button-secondary action" id="doaction2" name="doaction2" value="Apply">
            </div>
            <br class="clear">
        </div>
        <div style="display: none;" class="find-box" id="find-posts">
            <div class="find-box-head" id="find-posts-head"><?php _e('Find Posts or Pages','wpdmpp'); ?></div>
            <div class="find-box-inside">
                <div class="find-box-search">
                    <input type="hidden" value="" id="affected" name="affected">
                    <input type="hidden" value="3a4edcbda3" name="_ajax_nonce" id="_ajax_nonce">
                    <label for="find-posts-input" class="screen-reader-text"><?php _e('Search','wpdmpp'); ?></label>
                    <input type="text" value="" name="ps" id="find-posts-input">
                    <input type="button" class="button" value="Search" onclick="findPosts.send();"><br>
                    <input type="radio" value="posts" checked="checked" id="find-posts-posts" name="find-posts-what">
                    <label for="find-posts-posts"><?php _e('Posts','wpdmpp'); ?></label>
                    <input type="radio" value="pages" id="find-posts-pages" name="find-posts-what">
                    <label for="find-posts-pages"><?php _e('Pages','wpdmpp'); ?></label>
                </div>
                <div id="find-posts-response"></div>
            </div>
            <div class="find-box-buttons">
                <input type="button" value="Close" onclick="findPosts.close();" class="button alignleft">
                <input type="submit" value="Select" class="button-primary alignright" id="find-posts-submit">
            </div>
        </div>
    </form>
    <br class="clear">
</div>