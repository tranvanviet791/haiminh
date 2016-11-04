=== YITH Pre-Launch ===

Contributors: yithemes
Tags: prelaunch, maintenance, construction, themes, yit
Requires at least: 3.5.1
Tested up to: 4.4.2
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

YITH Pre-Launch give you the ability to have a simple Pre-Launch page for your website in under construction.


== Description ==

If you are been launching your website and would like to make it known to your visitors, install the plugin `YITH Pre-Launch` to quickly set
a lovely page customizable to show to your visitors to warn them of the ongoing prelaunch, with a pretty countodown in real time.

A working demo is available [here](http://plugins.yithemes.com/yith-prelaunch/). Full documentation is available [here](http://yithemes.com/docs-plugins/yith_pre_launch/).


= Installation =

Once you have installed the plugin, you just need to activate the plugin in order to enable it.

= Configuration =

YITH Pre-Launch will add a new page under Appearance -> Pre-Launch, where you can configure the plugin and customize the frontend page.

= Developer =

Are you a developer? Want to customize the templates or the style of the plugin? Read on the [documentation](http://yithemes.com/docs-plugins/yith_pre_launch/) and discover how to do that.

== Installation ==

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH Pre-Launch` from Plugins page

== Frequently Asked Questions ==

= Can I customize the prelaunch mode page? =
Yes, the page is a simple template and you can override it by putting the file template "prelaunch.php" inside the theme folder. You can also customize the style by adding your custom CSS in the specific option of the settings.

= Have I deactive the plugin to deactive the prelaunch mode page? =
No, you can deactive the prelaunch mode page even if the plugin is active.

= What are the main changes in plugin translation? =
Recently this plugin has been selected to be included in the "translate.wordpress.org" translate programme. In order to import correctly the plugin strings in the new system, we had to change the text domain form 'yit' to 'yith-pre-launch'. Once the plugin will be imported in the translate.wordpress.org system, the translations of other languages will be downloaded directly from WordPress, without using any .po and .mo files. Moreover, users will be able to participate in a more direct way to plugin translations, suggesting texts in their languages in the dedicated tab on translate.wordpress.org. During this transition step, .po and .mo files will be used as always, but in order to be recognized by WordPress, they will need to have a new nomenclature, renaming them in: yith-pre-launch-.po yith-woocommerce-ajax-navigation-.mo. For example, if your language files were named yit-en_GB.po and yit-en_GB.mo, you will just have to rename them respectively as yith-pre-launch-en_GB.po and yith-pre-launch-en_GB.mo.

== Screenshots ==

1. The prelaunch mode page
2. The general settings

== Changelog ==

= 1.2.1 =

* Fixed: Call data remotely and in a bad way

= 1.2.0 =

* Added: Support to new translate.wordpress.org program
* Added: 'yith-custom-login' languages file
* Updated: language file
* Removed: All 'yit' languages file

= 1.1.0 =

* Fixed: Reflected Cross-Site Scripting (XSS) in plugin panel page
* Tweak: Hidden fields on newsletter form

= 1.0.7 =

* Added: Plugin options file hooks
* Tweak: Check if user is allowd to show frontend 
* Tweak: Plugin options initializzation

= 1.0.6 =

* Added: yit_prelaunch_action, fired during wp_footer before enqueue plugin scripts
* Updated: Italian translation

= 1.0.5 =

* Fixed: Bugs with W3 Total Cache

= 1.0.4 =

* Added: Filters to manage options from theme
* Added: Support to Wordpress 3.9.1
* Updated: Plugin Core Framework
* Tweek: Filters for custom template from theme
* Tweek: Filters for custom template from child theme
* Fixed: minor bugs

= 1.0.3 =

* Minor bugs fixes

= 1.0.2 =

* The mascotte only appear if there's an image URL in the options

= 1.0.1 =

* Minor bugs fixes

= 1.0.0 =

* Initial release

== Suggestions ==

If you have suggestions about how to improve YITH Pre-Launch, you can [write us](mailto:plugins@yithemes.com "Your Inspiration Themes") so we can bundle them into YITH Pre-Launch.

== Translators ==

= Available Languages =
* English (Default)
* Italiano

If you have created your own language pack, or have an update for an existing one, you can send [gettext PO and MO file](http://codex.wordpress.org/Translating_WordPress "Translating WordPress")
[use](http://yithemes.com/contact/ "Your Inspiration Themes") so we can bundle it into YITH Pre-Launch Languages.

== Documentation ==

Full documentation is available [here](http://yithemes.com/docs-plugins/yith_pre_launch/).

== Upgrade notice ==

= 1.0.0 =

Initial release