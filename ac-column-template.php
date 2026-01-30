<?php
/**
 * Plugin Name: Admin Columns - WooCommerce Memberships Profile Fields
 * Plugin URI: https://admincolumns.com
 * Description: Dynamic columns for WooCommerce Memberships Profile Fields in Admin Columns Pro
 * Version: 2.0
 * Author: Abundant Designs LLC
 * Requires PHP: 7.4
 */

const AC_CT_FILE = __FILE__;

/**
 * Discover WooCommerce Memberships Profile Fields by querying user meta keys.
 *
 * @return array Array of profile field slugs with their labels
 */
function ac_wc_memberships_get_profile_fields(): array {
    global $wpdb;

    $profile_fields = [];
    $meta_key_prefix = '_wc_memberships_profile_field_';

    // Query all unique user meta keys that match the profile field pattern
    $meta_keys = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT meta_key 
            FROM {$wpdb->usermeta} 
            WHERE meta_key LIKE %s 
            ORDER BY meta_key ASC",
            $wpdb->esc_like($meta_key_prefix) . '%'
        )
    );

    foreach ($meta_keys as $meta_key) {
        // Extract the slug from the meta key
        $slug = str_replace($meta_key_prefix, '', $meta_key);
        
        if (!empty($slug)) {
            // Use the slug as label, or format it nicely
            $label = ucwords(str_replace(['_', '-'], ' ', $slug));
            $profile_fields[$slug] = $label;
        }
    }

    return $profile_fields;
}

// Register column types for Admin Columns Pro 7
add_filter('ac/column/types/pro', static function (array $factories, AC\TableScreen $table_screen): array {
    // Only register for wc_user_membership post type (User Memberships)
    $list_screen_key = method_exists($table_screen, 'get_key') ? (string) $table_screen->get_key() : (string) $table_screen->get_id();
    if ('wc_user_membership' !== $list_screen_key) {
        return $factories;
    }

    require_once __DIR__ . '/classes/Column/ColumnFactory.php';
    require_once __DIR__ . '/classes/Column/Column.php';
    require_once __DIR__ . '/classes/Formatter/ValueFormatter.php';
    require_once __DIR__ . '/classes/Column/Editing.php';
    require_once __DIR__ . '/classes/Column/Export.php';
    require_once __DIR__ . '/classes/Column/Search.php';
    require_once __DIR__ . '/classes/Column/Sorting.php';

    $profile_fields = ac_wc_memberships_get_profile_fields();

    foreach ($profile_fields as $slug => $label) {
        $factories[] = new AcColumnTemplate\Column\ColumnFactory($slug, $label);
    }

    return $factories;
}, 10, 2);

// 2. Optionally: load a text domain
// load_plugin_textdomain('ac-column-template', false, __DIR__ . '/languages/');
