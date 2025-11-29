=== Blogroll Links ===
Contributors: rajivpant, xenograg
Donate link: https://www.rajiv.com/contact/
Tags: links, blogroll, shortcode, bookmarks
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 3.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display your blogroll links anywhere in posts or pages using a simple shortcode.

== Description ==

Blogroll Links is a WordPress plugin that displays your site's link bookmarks (formerly called "blogroll") within posts and pages using a simple shortcode.

For people who maintain their website or blog using the WordPress content management system, Blogroll Links uses WordPress' built-in Links feature and presents links to friends' pages, resources, and social networking profiles.

= Features =

* Display links by category using the category slug
* Customizable sorting (by name, URL, rating, or ID)
* Honors link visibility settings (show/hidden)
* Respects target window settings for each link
* Displays link descriptions and images if available
* Works with WordPress' built-in Links Manager
* Supports XFN (XHTML Friends Network) relationship tags
* Full PHP 8+ compatibility
* Secure: Protected against SQL injection, XSS, and CSRF attacks

= Usage =

Add this shortcode to any post or page:

`[blogroll-links categoryslug="my-links"]`

= Full Shortcode Options =

`[blogroll-links categoryslug="my-links" sortby="link_name" sortorder="asc"]`

= Parameters =

* `categoryslug` - The slug of the link category to display (required)
* `sortby` - Sort field: link_name, link_url, link_rating, link_id (default: link_name)
* `sortorder` - Sort direction: asc or desc (default: asc)

= Examples =

**Display friends' websites sorted by name:**
`[blogroll-links categoryslug="friends" sortby="link_name" sortorder="asc"]`

**Display resources sorted by rating (highest first):**
`[blogroll-links categoryslug="resources" sortby="link_rating" sortorder="desc"]`

**Display social media profiles:**
`[blogroll-links categoryslug="social-profiles"]`

= Live Examples =

See this plugin in action:

* [www.rajiv.com/friends/](https://www.rajiv.com/friends/) - Social networking links with XFN tags
* [www.rajiv.com/charity/](https://www.rajiv.com/charity/) - Charitable organizations list

= Credits =

Thanks to Dave Grega and Adam E. Falk (xenograg) for their contributions to this code.

Version 3.0 was modernized using [Synthesis Coding](https://rajiv.com/blog/2025/11/09/synthesis-engineering-with-claude-code-technical-implementation-and-workflows/) with Claude Code - a human-AI collaborative development approach.

== Installation ==

1. Upload the `blogroll-links` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure default settings under Settings > Blogroll Links
4. Enable the Links Manager if hidden (see FAQ)

== Frequently Asked Questions ==

= Where is the Links Manager? =

WordPress hides the Links Manager by default in newer versions. To enable it, you have two options:

**Option 1:** Install the "Link Manager" plugin from WordPress.org.

**Option 2:** Add this code to your theme's functions.php file:

`add_filter( 'pre_option_link_manager_enabled', '__return_true' );`

= Why do I need this plugin? WordPress can display blogroll links in sidebars on its own. =

This plugin extends WordPress' links functionality by enabling you to show links within the content of your Posts and Pages, not just in sidebars. It takes full advantage of the WordPress links database and management tools.

= Why are no links showing? =

1. Make sure the Links Manager is enabled (see above)
2. Verify the category slug matches exactly (check in Links > Link Categories)
3. Check that your links are not set to "hidden"

= Is the legacy HTML comment syntax still supported? =

Yes, for backward compatibility the old syntax still works:

`<!--blogroll-links category-slug="my-links"--><!--/blogroll-links-->`

However, we recommend using the shortcode syntax for new content.

= Is this plugin compatible with PHP 8? =

Yes! Version 3.0.0 has been fully updated for PHP 8+ compatibility.

= Is this plugin secure? =

Yes. Version 3.0.0 includes comprehensive security improvements:

* SQL injection protection using prepared statements
* XSS protection with proper output escaping
* CSRF protection with nonce verification
* Proper capability checks for admin functions

== Screenshots ==

1. Links displayed on a page using the shortcode, showing XFN relationship tags
2. Admin settings page for configuring default options

== Changelog ==

= 3.0.0 =
* Security: Fixed SQL injection vulnerabilities using $wpdb->prepare()
* Security: Added CSRF protection with nonce verification on admin forms
* Security: Added output escaping (esc_html, esc_attr, esc_url) to prevent XSS
* Security: Fixed capability check using 'manage_options' instead of deprecated integer
* Compatibility: Full PHP 8.0, 8.1, 8.2, and 8.3 compatibility
* Compatibility: Tested with WordPress 6.7
* Fixed: Admin settings panel now saves and loads values correctly
* Fixed: Proper use of $wpdb->terms instead of hardcoded table prefix
* Improved: Added internationalization support with 'blogroll-links' text domain
* Improved: Added PHPDoc documentation for all functions
* Improved: Refactored code following WordPress Coding Standards
* Improved: Better error messages when category slug is invalid
* Improved: Added CSS class 'blogroll-links' to output for easier styling
* Updated: Plugin header with modern WordPress requirements

= 2.3 =
* Updated stable tag in readme.txt

= 2.1 =
* Fixed typo in description

= 2.0 =
* Switched to WordPress shortcode syntax
* Legacy HTML comment syntax still supported for backward compatibility

= 1.1 =
* Admin panel layout improvements for WordPress guidelines compliance
* Thanks to @federicobond for contributions

= 1.0 =
* Initial release

== Upgrade Notice ==

= 3.0.0 =
Critical security update. Fixes SQL injection, XSS, and CSRF vulnerabilities. Updates for PHP 8+ and WordPress 6.x compatibility. All users should upgrade immediately.

= 2.3 =
Minor update to stable tag.
