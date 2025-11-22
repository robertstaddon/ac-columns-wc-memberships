# Admin Columns - WooCommerce Memberships Profile Fields

A WordPress plugin that automatically creates Admin Columns Pro columns for each WooCommerce Memberships Profile Field on the `wc_user_membership` post type.

## Description

This plugin extends Admin Columns Pro to dynamically display WooCommerce Memberships Profile Fields as columns in the User Memberships admin list table. Each profile field gets its own column with full support for:

- **Display**: Shows profile field values from the membership owner's user meta
- **Editing**: Inline and bulk editing capabilities
- **Export**: CSV export support
- **Search**: Smart filtering by profile field values
- **Sorting**: Sort memberships by profile field values

## Requirements

- WordPress 3.5 or higher
- Admin Columns Pro 6.3 or higher
- WooCommerce Memberships (with profile fields configured)
- PHP 7.2 or higher

## Installation

1. Copy the plugin folder into your `wp-content/plugins` directory
2. Activate the plugin via the WordPress admin plugins page
3. Navigate to **Settings > Admin Columns** in WordPress admin
4. Select the **User Memberships** (`wc_user_membership`) post type
5. Add columns - you'll see one column available for each WooCommerce Memberships Profile Field

## How It Works

The plugin automatically discovers WooCommerce Memberships Profile Fields by querying user meta keys that match the pattern `_wc_memberships_profile_field_*`. For each discovered profile field, it creates a column that:

- Retrieves values from the post author's (membership owner's) user meta
- Uses the meta key pattern: `_wc_memberships_profile_field_{slug}`
- Displays the profile field label as the column header

## Structure

* `ac-column-template.php`: Main plugin file that discovers and registers columns
* `/classes/Column/Column.php`: Column class with display logic
* `/classes/Column/Editing.php`: Editing model for inline/bulk editing
* `/classes/Column/Export.php`: Export model for CSV export
* `/classes/Column/Search.php`: Search/filtering model
* `/classes/Column/Sorting.php`: Sorting model
* `/css`: CSS files (if needed)
* `/js`: JavaScript files (if needed)
* `/languages`: Translation files

## Technical Details

- Profile field data is stored in `wp_usermeta` on the membership owner's user account
- The plugin joins `wp_posts.post_author` with `wp_usermeta.user_id` to retrieve values
- Each column has a unique type identifier based on the profile field slug
- Profile fields are discovered dynamically at column registration time

## Support

For more information about Admin Columns Pro, visit:
https://docs.admincolumns.com/article/21-how-to-create-my-own-column

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes.