<div id="all-notes">
<?php
$order_notes = maybe_unserialize($order->order_notes);

if(isset($order_notes['messages'])){
    foreach ($order_notes['messages'] as $time => $order_note) {
        $copy = array();
        if(isset($order_note['admin'])) $copy[] = '<input type=checkbox checked=checked disabled=disabled /> Admin &nbsp; ';
        if(isset($order_note['seller'])) $copy[] = '<input type=checkbox checked=checked disabled=disabled /> Seller &nbsp; ';
        if(isset($order_note['customer'])) $copy[] = '<input type=checkbox checked=checked disabled=disabled /> Customer &nbsp; ';
        $copy = implode("", $copy);
        ?>

        <div class="panel panel-default dashboard-panel">
            <div class="panel-body">
                <?php echo strip_tags(stripcslashes($order_note['note']),"<a><strong><b><img>"); ?>
            </div>
            <?php if(isset($order_note['file'])){ ?>
                <div class="panel-footer text-right">
                    <?php foreach($order_note['file'] as $id => $file){ $aid = WPDM_Crypt::Encrypt($order->order_id."|||".$time."|||".$file); ?>
                        <a href="<?php echo home_url("/?oid=".$order->order_id."&_atcdl=".$aid); ?>" style="margin-left: 10px"><i class="fa fa-paperclip"></i> <?php echo $file; ?></a> &nbsp;
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="panel-footer text-right">
                <small><em><i class="fa fa-pencil"></i> <?php echo $order_note['by']; ?> &nbsp; <i class="fa fa-clock-o"></i> <?php echo date(get_option('date_format') . " h:i", $time); ?></em></small>
                <div class="pull-left"><small><em><?php if($copy!='') echo "Copy sent to ".$copy; ?></em></small></div>
            </div>
        </div>
    <?php
    }
}
?>
</div>
<form method="post" id="post-order-note">
    <input type="hidden" name="execute" value="AddNote" />
    <input type="hidden" name="order_id" value="<?php echo $order->order_id; ?>" />
    <div class="panel panel-default dashboard-panel">
        <textarea id="order-note" name="note" class="form-control" style="border: 0;box-shadow: none;min-height: 90px;max-width: 100%;min-width: 100%;padding: 10px"></textarea>

        <div id="plupload-upload-ui" class="panel-footer attachments drag-drop-area">
            <div id="filelist" class="pull-right"></div>
              <button id="plupload-browse-button" type="button" class="btn btn-default btn-xs" ><i class="fa fa-file"></i> &nbsp; <?php esc_attr_e('Attach Files'); ?></button>

                <?php
                $slimit = get_option('__wpdm_max_upload_size',0);
                if($slimit>0)
                    $slimit = wp_convert_hr_to_bytes($slimit.'M');
                else
                    $slimit = wp_max_upload_size();
                $plupload_init = array(
                    'runtimes'            => 'html5,silverlight,flash,html4',
                    'browse_button'       => 'plupload-browse-button',
                    'container'           => 'plupload-upload-ui',
                    'drop_element'        => 'drag-drop-area',
                    'file_data_name'      => 'async-upload',
                    'multiple_queues'     => true,
                    'max_file_size'       => $slimit.'b',
                    'url'                 => admin_url('admin-ajax.php'),
                    'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
                    'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                    'filters'             => array(array('title' => __('Allowed Files'), 'extensions' =>  get_option('__wpdm_allowed_file_types','*'))),
                    'multipart'           => true,
                    'urlstream_upload'    => true,

                    // additional post data to send to our ajax hook
                    'multipart_params'    => array(
                        '_ajax_nonce' => wp_create_nonce('frontend-file-upload'),
                        'action'      => 'wpdm_frontend_file_upload',            // the ajax action name
                    ),
                );

                // we should probably not apply this filter, plugins may expect wp's media uploader...
                $plupload_init = apply_filters('plupload_init', $plupload_init); ?>

                <script type="text/javascript">

                    jQuery(document).ready(function($){

                        // create the uploader and pass the config from above
                        var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

                        // checks if browser supports drag and drop upload, makes some css adjustments if necessary
                        uploader.bind('Init', function(up){
                            var uploaddiv = jQuery('#plupload-upload-ui');

                            if(up.features.dragdrop){
                                uploaddiv.addClass('drag-drop');
                                jQuery('#drag-drop-area')
                                    .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                                    .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                            }else{
                                uploaddiv.removeClass('drag-drop');
                                jQuery('#drag-drop-area').unbind('.wp-uploader');
                            }
                        });

                        uploader.init();

                        // a file was added in the queue
                        uploader.bind('FilesAdded', function(up, files){
                            //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);



                            plupload.each(files, function(file){
                                jQuery('#filelist').append(
                                    '<div class="file pull-left" id="' + file.id + '"><b>' +

                                    file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') </div>');
                            });

                            up.refresh();
                            up.start();
                        });

                        uploader.bind('UploadProgress', function(up, file) {

                            jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
                            jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
                        });


                        // a file was uploaded
                        uploader.bind('FileUploaded', function(up, file, response) {

                            jQuery('#' + file.id ).remove();
                            var d = new Date();
                            var ID = d.getTime();
                            response = response.response;
                            var nm = response;
                            if(response.length>20) nm = response.substring(0,7)+'...'+response.substring(response.length-10);
                            var fileinfo = "<span id='file_"+ID+"' class='atcf' ><a href='#' rel='#file_"+ID+"' class='del-file text-danger'><i class='fa fa-times'></i></a> &nbsp; <input type='hidden' name='file[]' value='"+response+"' />"+response+"</span>";
                            jQuery('#filelist').prepend(fileinfo);

                        });

                    });

                </script>


                <div class="clear"></div>


        </div>
        <div class="panel-footer text-right">

            <button class="btn btn-primary btn-sm" id="add-note-button" type="submit"><i class="fa fa-plus-circle"></i> Add Note</button>

            <div class="pull-left">
                <label>Also mail to:</label> &nbsp; <label><input type="checkbox" name="admin" value="1"> Site Admin</label>
                &nbsp; <label><input type="checkbox" name="seller" value="1"> Seller</label>
                &nbsp; <label><input type="checkbox" name="customer" value="1"> Customer</label>
            </div>
        </div>
    </div>
</form>

<script>
    jQuery(function($){
        $('#post-order-note').submit(function(){
            $('#add-note-button').html('<i class="fa fa-spinner fa-spin"></i> Adding...');
            $(this).ajaxSubmit({
                url: '<?php echo admin_url('/admin-ajax.php?action=wpdmpp_ajax_call'); ?>',
                success: function(res){
                    $('#add-note-button').html('<i class="fa fa-plus-circle"></i> Add Note');
                    if(res!='error')
                    $('#all-notes').append(res);
                    else
                    alert('Error!');
                }
            });
            return false;
        });
    });
</script>
