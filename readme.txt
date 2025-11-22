=== Admin Columns - WooCommerce Memberships Profile Fields ===
Contributors: admincolumns
Tags: admin-columns, woocommerce, memberships, profile-fields, columns
Requires at least: 3.5
Tested up to: 6.4
Stable tag: 1.1
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Dynamically creates Admin Columns Pro columns for each WooCommerce Memberships Profile Field on the User Memberships post type.

== Description ==

This plugin extends Admin Columns Pro to automatically display WooCommerce Memberships Profile Fields as columns in the User Memberships admin list table. Each profile field gets its own column with full support for display, editing, export, search, and sorting.

**Features:**

* Automatic discovery of WooCommerce Memberships Profile Fields
* Dynamic column creation for each profile field
* Full Admin Columns Pro integration (editing, export, search, sorting)
* Retrieves values from membership owner's user meta
* Works seamlessly with existing Admin Columns Pro settings

**Requirements:**

* WordPress 3.5+
* Admin Columns Pro 6.3+
* WooCommerce Memberships
* PHP 7.2+

== Installation ==

1. Copy the plugin folder into your `wp-content/plugins` directory
2. Activate the plugin via the WordPress admin plugins page
3. Navigate to **Settings > Admin Columns** in WordPress admin
4. Select the **User Memberships** (`wc_user_membership`) post type
5. Add columns - you'll see one column available for each WooCommerce Memberships Profile Field

== Frequently Asked Questions ==

= How does the plugin discover profile fields? =

The plugin queries user meta keys matching the pattern `_wc_memberships_profile_field_*` to automatically discover all available profile fields.

= Where is the profile field data stored? =

Profile field values are stored in the membership owner's user meta with the key pattern `_wc_memberships_profile_field_{slug}`.

= Can I edit profile field values from the admin list? =

Yes! Each column supports inline and bulk editing through Admin Columns Pro's editing features.

= Do I need to configure anything? =

No configuration needed. The plugin automatically discovers and creates columns for all existing profile fields.

== Changelog ==

= 1.1 =
* Added dynamic column creation for WooCommerce Memberships Profile Fields
* Added automatic discovery of profile fields by querying user meta keys
* Added support for displaying profile field values from post author's user meta
* Added full Admin Columns Pro integration with editing, export, search, and sorting capabilities
* Changed all column classes to work with user meta instead of post meta
* Changed search and sorting queries to join wp_usermeta via wp_posts.post_author

= 1.0 =
* Initial Release