<?php
/**
 * Main admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH Pre-Launch
 * @version 1.0.2
 */

if ( ! defined( 'YITH_PRELAUNCH' ) ) {
    exit;
} // Exit if accessed directly

global $yith_prelaunch_options;
$yith_prelaunch_options = array(
    //tab general
    'general'    => array(
        'label'    => __( 'General', 'yith-pre-launch' ),
        'sections' => array(
            'general'    => array(
                'title'       => __( 'General Settings', 'yith-pre-launch' ),
                'description' => '',
                'fields'      => array(
                    'yith_prelaunch_enable'       => array(
                        'title'       => __( 'Enable Pre-Launch', 'yith-pre-launch' ),
                        'description' => __( 'Enable the splash page to lets users to know the blog is down for prelaunch. (Default: Off) ', 'yith-pre-launch' ),
                        'type'        => 'checkbox',
                        'std'         => false
                    ),

                    'yith_prelaunch_roles'        => array(
                        'title'       => __( 'Roles', 'yith-pre-launch' ),
                        'description' => __( 'The user roles enabled to see the frontend. Check a role to enable it to show the website with prelaunch mode active.', 'yith-pre-launch' ),
                        'type'        => 'checkboxes',
                        'options'     => yit_wp_roles(),
                        'std'         => array( 'administrator' )
                    ),

                    'yith_prelaunch_message'      => array(
                        'title'       => __( 'Message', 'yith-pre-launch' ),
                        'description' => __( 'The message displayed. You can also use HTML code.', 'yith-pre-launch' ),
                        'type'        => 'textarea',
                        'std'         => '<h3>' . __( 'OPS! WE ARE NOT READY YET!', 'yith-pre-launch' ) . '</h3>
<p>' . __( "Hello there! We are not ready yet, but why don't you leave your email address  and we'll let you know  as soon as we're in business!", 'yith-pre-launch' ) . '</p>'
                    ),

                    'yith_prelaunch_custom_style' => array(
                        'title'       => 'Custom style',
                        'description' => __( 'Insert here your custom CSS style.', 'yith-pre-launch' ),
                        'type'        => 'textarea',
                        'std'         => ''
                    ),

                    'yith_prelaunch_mascotte'     => array(
                        'title'       => 'Mascotte',
                        'description' => __( 'If you want, you can set here a mascotte image to show above the main box, in the right side.', 'yith-pre-launch' ),
                        'type'        => 'upload',
                        'std'         => YITH_PRELAUNCH_URL . 'assets/images/mascotte.png'
                    ),
                )
            ),
            'typography' => array(
                'title'       => __( 'Typography', 'yith-pre-launch' ),
                'description' => '',
                'fields'      => array(
                    'yith_prelaunch_title_font'     => array(
                        'title'       => __( 'Title font of message', 'yith-pre-launch' ),
                        'description' => __( 'Choose the font type, size and color for the titles inside the message text.', 'yith-pre-launch' ),
                        'type'        => 'typography',
                        'std'         => array(
                            'size'   => 18,
                            'unit'   => 'px',
                            'family' => 'Open Sans',
                            'style'  => 'bold',
                            'color'  => '#666666',
                        ),
                    ),
                    'yith_prelaunch_paragraph_font' => array(
                        'title'       => __( 'Paragraph font of message', 'yith-pre-launch' ),
                        'description' => __( 'Choose the font type, size and color for the paragraphs inside the message text.', 'yith-pre-launch' ),
                        'type'        => 'typography',
                        'std'         => array(
                            'size'   => 13,
                            'unit'   => 'px',
                            'family' => 'Open Sans',
                            'style'  => 'regular',
                            'color'  => '#666666',
                        ),
                    )
                )
            ),
            'colors'     => array(
                'title'       => __( 'Colors', 'yith-pre-launch' ),
                'description' => '',
                'fields'      => array(
                    'yith_prelaunch_border_top' => array(
                        'title'       => __( 'Border top color', 'yith-pre-launch' ),
                        'description' => __( 'Choose the color for the big border top of the main box.', 'yith-pre-launch' ),
                        'type'        => 'colorpicker',
                        'std'         => '#99AFD2',
                    )
                )
            ),
        )
    ),

    //tab background
    'background' => array(
        'label'    => __( 'Background', 'yith-pre-launch' ),
        'sections' => array(
            'background' => array(
                'title'       => __( 'Background Settings', 'yith-pre-launch' ),
                'description' => __( 'Customize the background of the page', 'yith-pre-launch' ),
                'fields'      => array(
                    'yith_prelaunch_background_image'      => array(
                        'title'       => __( 'Background image', 'yith-pre-launch' ),
                        'description' => __( 'Upload or choose from your media library the background image', 'yith-pre-launch' ),
                        'type'        => 'upload',
                        'std'         => YITH_PRELAUNCH_URL . 'assets/images/bg-pattern.png',
                    ),
                    'yith_prelaunch_background_color'      => array(
                        'title'       => __( 'Background Color', 'yith-pre-launch' ),
                        'description' => __( 'Choose a background color', 'yith-pre-launch' ),
                        'type'        => 'colorpicker',
                        'std'         => '',
                    ),
                    'yith_prelaunch_background_repeat'     => array(
                        'title'       => __( 'Background Repeat', 'yith-pre-launch' ),
                        'description' => __( 'Select the repeat mode for the background image.', 'yith-pre-launch' ),
                        'type'        => 'select',
                        'std'         => apply_filters( 'yith_prelaunch_background_repeat_std', 'repeat' ),
                        'options'     => array(
                            'repeat'    => __( 'Repeat', 'yith-pre-launch' ),
                            'repeat-x'  => __( 'Repeat Horizontally', 'yith-pre-launch' ),
                            'repeat-y'  => __( 'Repeat Vertically', 'yith-pre-launch' ),
                            'no-repeat' => __( 'No Repeat', 'yith-pre-launch' ),
                        )
                    ),
                    'yith_prelaunch_background_position'   => array(
                        'title'       => __( 'Background Position', 'yith-pre-launch' ),
                        'description' => __( 'Select the position for the background image.', 'yith-pre-launch' ),
                        'type'        => 'select',
                        'options'     => array(
                            'center'        => __( 'Center', 'yith-pre-launch' ),
                            'top left'      => __( 'Top left', 'yith-pre-launch' ),
                            'top center'    => __( 'Top center', 'yith-pre-launch' ),
                            'top right'     => __( 'Top right', 'yith-pre-launch' ),
                            'bottom left'   => __( 'Bottom left', 'yith-pre-launch' ),
                            'bottom center' => __( 'Bottom center', 'yith-pre-launch' ),
                            'bottom right'  => __( 'Bottom right', 'yith-pre-launch' ),
                        ),
                        'std'         => apply_filters( 'yith_prelaunch_background_position_std', 'top left' )
                    ),
                    'yith_prelaunch_background_attachment' => array(
                        'title'       => __( 'Background Attachment', 'yith-pre-launch' ),
                        'description' => __( 'Select the attachment for the background image.', 'yith-pre-launch' ),
                        'type'        => 'select',
                        'options'     => array(
                            'scroll' => __( 'Scroll', 'yith-pre-launch' ),
                            'fixed'  => __( 'Fixed', 'yith-pre-launch' ),
                        ),
                        'std'         => apply_filters( 'yith_prelaunch_background_attachment_std', 'scroll' )
                    )
                )
            )
        )
    ),

    //tab logo
    'logo'       => array(
        'label'    => __( 'Logo', 'yith-pre-launch' ),
        'sections' => array(
            'logo' => array(
                'title'       => __( 'Logo Settings', 'yith-pre-launch' ),
                'description' => __( 'Customize the logo', 'yith-pre-launch' ),
                'fields'      => array(
                    'yith_prelaunch_logo_image'        => array(
                        'title'       => __( 'Logo image', 'yith-pre-launch' ),
                        'description' => __( 'Upload or choose from your media library the logo image', 'yith-pre-launch' ),
                        'type'        => 'upload',
                        'std'         => YITH_PRELAUNCH_URL . 'assets/images/logo.png',
                    ),
                    'yith_prelaunch_logo_tagline'      => array(
                        'title'       => __( 'Logo tagline', 'yith-pre-launch' ),
                        'description' => __( 'Set the tagline to show below the logo image', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_logo_tagline_font' => array(
                        'title'       => __( 'Logo tagline font', 'yith-pre-launch' ),
                        'description' => __( 'Choose the font type, size and color for the tagline text.', 'yith-pre-launch' ),
                        'type'        => 'typography',
                        'std'         => array(
                            'size'   => 15,
                            'unit'   => 'px',
                            'family' => 'Open Sans',
                            'style'  => 'regular',
                            'color'  => '#999999',
                        ),
                    )
                )
            )
        )
    ),

    //tab countodn
    'countdown'  => array(
        'label'    => __( 'Countdown', 'yith-pre-launch' ),
        'sections' => array(
            'settings'   => array(
                'title'  => __( 'Settings', 'yith-pre-launch' ),
                'fields' => array(
                    'yith_prelaunch_countdown_enable' => array(
                        'title'       => __( 'Enable countdown', 'yith-pre-launch' ),
                        'description' => __( 'Tick if you want to show the countdown.', 'yith-pre-launch' ),
                        'type'        => 'checkbox',
                        'std'         => 1,
                    ),
                    'yith_prelaunch_to_date'          => array(
                        'title'       => __( 'Countdown to', 'yith-pre-launch' ),
                        'description' => __( 'The date when the countdown will stop.', 'yith-pre-launch' ),
                        'type'        => 'datepicker',
                        'std'         => array( 'date' => '', 'hh' => 0, 'mm' => 0, 'ss' => 0 ),
                    ),
                )
            ),
            'typography' => array(
                'title'  => __( 'Typography', 'yith-pre-launch' ),
                'fields' => array(
                    'yith_prelaunch_numbers_font' => array(
                        'title'       => __( 'Font numbers', 'yith-pre-launch' ),
                        'description' => __( 'The font for the numbers.', 'yith-pre-launch' ),
                        'type'        => 'typography',
                        'std'         => array(
                            'size'   => 50,
                            'unit'   => 'px',
                            'family' => 'Open Sans',
                            'style'  => 'extra-bold',
                            'color'  => '#617291',
                        ),
                    ),
                    'yith_prelaunch_labels_font'  => array(
                        'title'       => __( 'Font labels', 'yith-pre-launch' ),
                        'description' => __( 'The font for the labels.', 'yith-pre-launch' ),
                        'type'        => 'typography',
                        'std'         => array(
                            'size'   => 15,
                            'unit'   => 'px',
                            'family' => 'Open Sans',
                            'style'  => 'regular',
                            'color'  => '#999999',
                        ),
                    ),
                )
            )
        )
    ),

    //tab container
    'newsletter' => array(
        'label'    => __( 'Newsletter', 'yith-pre-launch' ),
        'sections' => array(
            'newsletter'               => array(
                'title'       => __( 'Newsletter', 'yith-pre-launch' ),
                'description' => __( 'Add a newsletter form in your prelaunch mode page.', 'yith-pre-launch' ),
                'fields'      => array(
                    'yith_prelaunch_enable_newsletter_form'             => array(
                        'title'       => __( 'Enable Newsletter form', 'yith-pre-launch' ),
                        'description' => __( 'Choose if you want to enable the newsletter form in the prelaunch mode page.', 'yith-pre-launch' ),
                        'type'        => 'checkbox',
                        'std'         => true
                    ),
                    'yith_prelaunch_newsletter_email_font'              => array(
                        'title'       => __( 'Newsletter Email Font', 'yith-pre-launch' ),
                        'description' => __( 'Choose the font type, size and color for the email input field.', 'yith-pre-launch' ),
                        'type'        => 'typography',
                        'std'         => array(
                            'size'   => 12,
                            'unit'   => 'px',
                            'family' => 'Open Sans',
                            'style'  => 'bold',
                            'color'  => '#a3a3a3',
                        ),
                    ),
                    'yith_prelaunch_newsletter_submit_font'             => array(
                        'title'       => __( 'Newsletter Submit Font', 'yith-pre-launch' ),
                        'description' => __( 'Choose the font type, size and color for the email submit.', 'yith-pre-launch' ),
                        'type'        => 'typography',
                        'std'         => array(
                            'size'   => 16,
                            'unit'   => 'px',
                            'family' => 'Open Sans',
                            'style'  => 'extra-bold',
                            'color'  => '#fff',
                        ),
                    ),
                    'yith_prelaunch_newsletter_submit_background'       => array(
                        'title'       => __( 'Newsletter submit background', 'yith-pre-launch' ),
                        'description' => __( 'The submit button background.', 'yith-pre-launch' ),
                        'type'        => 'colorpicker',
                        'std'         => '#617291',
                    ),
                    'yith_prelaunch_newsletter_submit_background_hover' => array(
                        'title'       => __( 'Newsletter submit hover background', 'yith-pre-launch' ),
                        'description' => __( 'The submit button hover background.', 'yith-pre-launch' ),
                        'type'        => 'colorpicker',
                        'std'         => '#3c5072',
                    ),
                    'yith_prelaunch_newsletter_title'                   => array(
                        'title'       => __( 'Title', 'yith-pre-launch' ),
                        'description' => __( 'The title displayed above the newsletter form.', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    )
                )
            ),
            'newsletter_configuration' => array(
                'title'       => __( 'Form configuration', 'yith-pre-launch' ),
                'description' => __( 'Configure the form and each field, to integrate the newsletter features of an external service.', 'yith-pre-launch' ),
                'fields'      => array(
                    'yith_prelaunch_newsletter_action'        => array(
                        'title'       => __( 'Action URL', 'yith-pre-launch' ),
                        'description' => __( 'Set the action url of the form.', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => ''
                    ),
                    'yith_prelaunch_newsletter_method'        => array(
                        'title'       => __( 'Form method', 'yith-pre-launch' ),
                        'description' => __( 'Set the method for the form request.', 'yith-pre-launch' ),
                        'type'        => 'select',
                        'options'     => array(
                            'POST' => 'POST',
                            'GET'  => 'GET',
                        ),
                        'std'         => 'POST'
                    ),
                    'yith_prelaunch_newsletter_email_label'   => array(
                        'title'       => __( '"Email" field label', 'yith-pre-launch' ),
                        'description' => __( 'The label for the email field', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => 'Enter your email address',
                    ),
                    'yith_prelaunch_newsletter_email_name'    => array(
                        'title'       => __( '"Email" field name', 'yith-pre-launch' ),
                        'description' => __( 'The "name" attribute for the email field', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => 'email',
                    ),
                    'yith_prelaunch_newsletter_submit_label'  => array(
                        'title'       => __( 'Submit button label', 'yith-pre-launch' ),
                        'description' => __( 'The label for the submit button', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => __( 'GET NOTIFIED', 'yith-pre-launch' ),
                    ),
                    'yith_prelaunch_newsletter_hidden_fields' => array(
                        'title'       => __( 'Newsletter Hidden fields', 'yith-pre-launch' ),
                        'description' => __( 'Set the hidden fields to include in the form. Use the form: field1=value1&field2=value2&field3=value3', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    )
                )
            )
        )
    ),

    //tab logo
    'socials'    => array(
        'label'    => __( 'Socials', 'yith-pre-launch' ),
        'sections' => array(
            'logo' => array(
                'title'       => __( 'Set the socials', 'yith-pre-launch' ),
                'description' => __( "You can set here the url of your social accounts (you can leave empty if you don't want to show the social icon)", 'yith-pre-launch' ),
                'fields'      => array(
                    'yith_prelaunch_socials_facebook'  => array(
                        'title'       => __( 'Facebook', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your facebook profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_twitter'   => array(
                        'title'       => __( 'Twitter', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your twitter profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_gplus'     => array(
                        'title'       => __( 'Google+', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your Google+ profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_youtube'   => array(
                        'title'       => __( 'Youtube', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your youtube profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_rss'       => array(
                        'title'       => __( 'RSS', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your RSS feed', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_skype'     => array(
                        'title'       => __( 'Skype', 'yith-pre-launch' ),
                        'description' => __( 'Set the username of your skype account', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_email'     => array(
                        'title'       => __( 'Email', 'yith-pre-launch' ),
                        'description' => __( 'Write here your email address', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_behance'   => array(
                        'title'       => __( 'Behance', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your Behance profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_dribble'   => array(
                        'title'       => __( 'Dribbble', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your dribbble profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_flickr'    => array(
                        'title'       => __( 'FlickR', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your Flickr profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_instagram' => array(
                        'title'       => __( 'Instagram', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your instagram profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_pinterest' => array(
                        'title'       => __( 'Pinterest', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your Pinterest profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_tumblr'    => array(
                        'title'       => __( 'Tumblr', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your Tumblr profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                    'yith_prelaunch_socials_linkedin'  => array(
                        'title'       => __( 'LinkedIn', 'yith-pre-launch' ),
                        'description' => __( 'Set the URL of your LinkedIn profile', 'yith-pre-launch' ),
                        'type'        => 'text',
                        'std'         => '',
                    ),
                )
            )
        )
    ),
);
