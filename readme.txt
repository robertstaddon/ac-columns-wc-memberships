=== Admin Columns - WooCommerce Memberships Profile Fields ===
Contributors: abundantdesigns
Author: Abundant Designs LLC
Tags: admin-columns, woocommerce, memberships, profile-fields, columns
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 2.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Dynamically creates Admin Columns Pro 7 columns for each WooCommerce Memberships Profile Field on the User Memberships post type.

== Description ==

This plugin extends Admin Columns Pro to automatically display WooCommerce Memberships Profile Fields as columns in the User Memberships admin list table. Each profile field gets its own column with full support for display, editing, export, search, and sorting.

**Features:**

* Automatic discovery of WooCommerce Memberships Profile Fields
* Dynamic column creation for each profile field
* Full Admin Columns Pro integration (editing, export, search, sorting)
* Dropdown filters with all available values for easy filtering
* Retrieves values from membership owner's user meta
* Works seamlessly with existing Admin Columns Pro settings

**Requirements:**

* WordPress 5.0+
* Admin Columns Pro 7.0+
* WooCommerce Memberships
* PHP 7.4+

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

= 2.2 =
* Column constructor now receives dependencies (FeatureSettingBuilderFactory, DefaultSettingsBuilder) explicitly; resolved at instantiation in generated subclass, not inside Column (per AC developer feedback)
* Removed get_export() and ExportFormatter; display and export both use ValueFormatter via get_formatters()
* Removed unused Column\Export.php service class
* Validation of profile_slug and label moved outside Column to ac_wc_memberships_profile_field_column_class(); Column assumes validated input

= 2.1 =
* Fixed sorting fatal error: SqlOrderByFactory::create() now receives a string (order name with ASC fallback) for Admin Columns Pro 7 compatibility
* Fixed export fatal: export column values now use AC\Formatter (ExportFormatter) in FormatterCollection
* Fixed column registration: register class name strings and dynamic subclasses; Column resolves AdvancedColumnFactory dependencies from AC DI container
* Fixed ValueFormatter: format() signature and Value API for AC\Formatter compatibility

= 2.0 =
* Upgraded for Admin Columns Pro 7 (breaking change; v6 no longer supported)
* Column registration now uses filter `ac/column/types/pro` and factory pattern
* Column class now extends `ACP\Column\AdvancedColumnFactory`
* Display value now uses `AC\FormatterCollection` and custom ValueFormatter
* Editing, sorting, search, and export wired via get_editing/get_sorting/get_search/get_export
* Requires PHP 7.4+ and Admin Columns Pro 7.0+

= 1.3 =
* Added dropdown filter with all available values for each profile field
* Added automatic discovery and population of filter dropdown options from existing user meta values
* Changed search/filter functionality to use dropdown select instead of plain text input
* Improved handling of serialized array values in filter dropdown

= 1.2 =
* Fixed profile field slug extraction from column type when columns are loaded from saved configurations
* Fixed inline editing not saving values by ensuring profile field slug is correctly passed to supporting classes
* Fixed meta key construction to include profile field slug in all cases
* Updated all supporting class instantiation methods to use get_profile_field_slug() method
* Improved user ID retrieval consistency across all classes using get_post_field()
* Columns now properly categorized under "woocommerce" group instead of "custom"

= 1.1 =
* Added dynamic column creation for WooCommerce Memberships Profile Fields
* Added automatic discovery of profile fields by querying user meta keys
* Added support for displaying profile field values from post author's user meta
* Added full Admin Columns Pro integration with editing, export, search, and sorting capabilities
* Changed all column classes to work with user meta instead of post meta
* Changed search and sorting queries to join wp_usermeta via wp_posts.post_author

= 1.0 =
* Initial Release