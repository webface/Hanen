=== Beaver Tunnels ===
Contributors: firetree, danielmilner
Tags: beaver builder
Requires at least: 4.3
Tested up to: 4.7.4
Stable tag: 2.1.5
License: GPLv2 or later
License URI: http://ww.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that allows Beaver Builder to travel outside of the content area and tunnel to other areas of your site.

== Description ==

A WordPress plugin that allows Beaver Builder to travel outside of the content area and tunnel to other areas of your site.

> Requires [Beaver Builder](http://beaverbuilder.com/) Standard, Pro, or Agency.

= Features: =
* Attach Beaver Builder Templates to action hooks in your themes and plugins.
* Conditionally display the templates on the desired pages.
* Visual Hook Guide to show you where each action hook is.

== Installation ==

1. Upload the beaver-tunnels folder to the /wp-content/plugins/ directory.
2. Activate the Beaver Tunnels plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= 2.1.5 =
* Beaver Tunnels column now displays properly with Beaver Builder v1.10 and greater.
* Updated how some help text was worded.

= 2.1.4 =
* Added: Actions to fire before and after the find_templates function runs.
* Enhancement: Limited the number of posts returned to the 1,000 most recent.

= 2.1.3 =
* Fixed: An issue with the other shortcodes inside the [beaver_builder] shortcode while in Page Builder mode.

= 2.1.2 =
* Fixed: Creating multiple OR conditions would save as AND conditions.
* Fixed: Improvements to the reliability of the [beaver_tunnels] shortcode.

= 2.1.1 =
* Fixed: The Tax Archive condition did not work for Categories or Tags.

= 2.1.0 =
* Added: Parent Page condition.
* Added: Dismissible admin pointer - showing where the display conditions are located.
* Enhancement: When done editing a tunneled template, you will be returned to the previous page you were editing.
* Enhancement: The metabox fields are now responsive.
* Enhancement: Reworked the Admin Bar Hook Guide UI.
* Fixed: User condition was not working.
* Fixed: Rare error when no tunneled templates could be found.

= 2.0.1 =
* Fixed: GeneratePress detection was throwing an error.
* Fixed: v2.0.0 upgrade routine was throwing an error in certain situations.
* Fixed: In multisite settings, some of our HTML was showing. Oops :/

= 2.0.0 =
* Added: Before Date/Time condition.
* Added: After Date/Time condition.
* Added: Before Time condition.
* Added: After Time condition.
* Added: Day of Week condition.
* Added: Author condition.
* Added: Term Archive condition.
* Added: Custom Post Type conditions.
* Added: Built-in support for the GeneratePress theme.
* Enhancement: Completely reworked the display conditions interface.
* Enhancement: Settings have moved to the Page Builder settings screen. (Multisite settings have not moved.)
* Enhancement: Help links inside of the meta box on the edit Template screen.
* Enhancement: The Visual Hook Guide has a new color.
* Enhancement: Admin notice if using PHP 5.3 or earlier.
* Enhancement: Supported themes now also check if they are active, not just installed.
* Fixed: Now uses WP_Query instead of get_pages().
* Fixed: Beaver Builder Theme now respects white label settings.
* Fixed: WooCommerce grid would break in certain situations.

= 1.2.5 =
* Fixed Term Singular to work on Pages.

= 1.2.4 =
* Fixed another issue with the Term Singular conditional check.

= 1.2.3 =
* Fixed: An issue that occasionally happened during the upgrade routine.
* Fixed: The Term Singular conditional check.

= 1.2.2 =
* Enhancement: Improved behavior when Beaver Builder is deactivated.
* Fixed: Incorrect viewport meta in the Template page template.
* Added: Support for Genesis stylesheets on the Template editor screen.

= 1.2.1 =
* Fixed: Reverted back to the previous method of rendering Templates because of issues with certain modules.

= 1.2.0 =
* Added: Multisite support. Certain settings will move to the Network Admin when network activated.
* Added: White Label settings are inherited from Beaver Builder.
* Enhancement: Beaver Builder Template Admin is turned on when plugin is activated.
* Enhancement: Template assets are now enqueued with Beaver Builder and are no longer rendered inline with the tunneled Template.
* Fixed: The minimal Template override now has the correct meta tags to be responsive.
* Fixed: The `[beaver_tunnels]` shortcode now overrides the `$wp_query` object as well.

= 1.1.1 =
* Fixed: Removed some debugging output when using taxonomy conditionals.

= 1.1.0 =
* Added: `[beaver_tunnels]` shortcode for use in tunneled templates.

= 1.0.3 =
* Fixed: Styles are imported correctly when editing a Template and using the Beaver Builder Theme.

= 1.0.2 =
* Fixed: Admin bar now only displays if user can edit page.
* Fixed: More checks to make sure that the admin bar is visible when it should be.
* Enhancement: License Key field type is now set to "password".

= 1.0.1 =
* Fixed: An issue with term display rules.
* Added: More options for user status.

= 1.0.0 =
* First public release.
