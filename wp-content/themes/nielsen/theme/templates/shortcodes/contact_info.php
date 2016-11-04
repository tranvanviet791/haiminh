<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Template file for show a contact info
 *
 * @package Yithemes
 * @author Francesco Licandro <francesco.licandro@yithemes.com>
 * @since 1.0.0
 */

$animate_data   = ( $animate != '' ) ? 'data-animate="' . $animate . '"' : '';
$animate_data  .= ( $animation_delay != '' ) ? ' data-delay="' . $animation_delay . '"' : '';
$animate        = ( $animate != '' ) ? ' yit_animate' : '';

?>
<div class="contact-info <?php echo esc_attr( $animate . $vc_css ); ?>" <?php echo $animate_data ?>>
	<?php if ( isset ($title) && $title != '' ) : ?> <h3><?php echo strip_tags($title) ?></h3><?php endif; ?>
    <?php if ( isset ($subtitle) && $subtitle != '' ) : ?> <h2><?php echo strip_tags($subtitle) ?></h2><?php endif; ?>
	<div class="sidebar-nav">
		<ul>
           <?php yit_get_contact_info_item_custom($icon_type, $address_icon, $address_icon_theme, $address_title ,$address); ?>
           <?php yit_get_contact_info_item_custom($icon_type, $phone_icon, $phone_icon_theme,  $phone_title, $phone); ?>
           <?php yit_get_contact_info_item_custom($icon_type, $mobile_icon, $mobile_icon_theme, $mobile_title,$mobile); ?>
           <?php yit_get_contact_info_item_custom($icon_type, $fax_icon, $fax_icon_theme, $fax_title,$fax); ?>
           <?php yit_get_contact_info_item_custom($icon_type, $email_icon, $email_icon_theme, $email_title,$email, $email_link); ?>
		</ul>
	</div>
</div>