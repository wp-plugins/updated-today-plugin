=== Updated Today Banner === 
Contributors: cklosows 
Tags: css, posts, updates, banner
Stable tag: 2.5.2
Requires at least: 2.7.1
Tested up to: 3.4.2
Donate link: http://www.chriskdesigns.com/plugins/updated-today
 
Displays a banner graphic on your site whenever you publish or update a post or page on your blog.

== Installation ==
1. Download the .zip file
2. Unpack into wp-content/plugins/ folder
3. Activate the plugin

== Frequently Asked Questions ==
If you have questions please leave them in the comments at the <a href="http://www.chriskdesigns.com/plugins/updated-today" target="_blank">plugin homepage</a>.

== Changelog ==
–Version 2.5.2–
Fixing some short tag issues.
Adding in a constant for the Version number to add on the enqueue scripts/styles
White Spacing Fixes to meet standards (Thanks to the WP-PHPTidy extension for Sublime Text 2 - https://github.com/welovewordpress/SublimePhpTidy)

–Version 2.5.1–
Fix for mistaken Short Tags issues, my bad guys (Thanks to Scott Grayban of https://www.borgnet.net for the find/fix and working with me on it)

–Version 2.5–
Moved CSS into wp_enqueue_style
Moved pngfix.js into wp_enqueue_script
Added ability to upload images

–Version 2.4–
Added an option to make the image a link to the most recent post
Cleaned up the options to a single Datbase Entry to require less calls to
the get_option function. This also imports your old settings automatically.

–Version 2.3–
Cleaning a little house from old settings and code.
Plugin now sets defaults upon activation and removes settings from the options table when deactivated.

–Version 2.2–
Added the ability to change what post types (post or page) and if it’s modified or published status that is checked. Also added the option for if the banner displays in the header area, or the footer area. Useful for some themes if it just won’t display correctly.

–Version 2.1.1–
Changed date(“Y-m-d”); to use date_i18n(“Y-m-d”); to avoid date conflicts when timezones are set in WordPress

–Version 2.0–
Added a ‘Settings’ page in which you can choose your image to display, the side of the page it displays on and allows the blogger to upload their own images to the specified folder.

–Version 1.8.1–
Cleaned up the whitespace on the CSS and added CSS to make the padding and margin on the image ’0′.

–Version 1.8–
Modified database query to use the $table_prefix variable for databases containing multiple WordPress installations as well as people who modified the $table_prefix on setup.
Big thanks to Georg and TeraS for their help on testing for this one!

–Version 1.6.1–
Fixed an error with some themes that made their header image use a Z-Index of 99, which matched that of the plugin. Changed the plugin Z-Index to 100. – thanks TeraS

–Version 1.6–
Fixed the banner from displaying when a draft was created by adding the post_status query to only include posts with the status of ‘publish’

–Version 1.4–
Fixed the issue with not displaying transparent in Internet Explorer
Fixed link of the banner to now go to the post id instead of the guid URI

–Version 1.3–
Plugin is now fully WC3 Compliant
Minor CSS style changes
Added configuration boolean (true/false) settings for placement, style, and pngfix.
Added link to the most current post
Known Issues:
When a picture is used in a post, that picture becomes the target of the link on the banner.

–Version 1.2–
Chromakode is now helping me out with some of the testing and coding.
Improved WC3 compatability (still one more issue to resolve with code placement)

–Version 1.1–
Included pngfix.js in to the plugin folder in order to make for an easier install. This allows for transparent png’s to be visable in Internet Explorer.
Thanks to http://homepage.ntlworld.com/bobosola/ for the PNGFIX code.

–Version 1.0–
This is the initial release of the plugin.
Possible issues:
Not certain if every WordPress install uses the same field names for the post_date and same table names.

== Description ==
When people link into your site, the content may not be your most recent. The visitor may not realize that you are still active and publishing new content. With this plugin,
 visitors will see that you are active and writing new content.

== Upgrade Notice ==
If you are upgrading, you may need to go back into your settings and set them to your previous configuration.

== Screenshots ==
1. The banner in action
