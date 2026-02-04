<?php
/**
 * Plugin Name: Admin Columns - WooCommerce Memberships Profile Fields
 * Plugin URI: https://admincolumns.com
 * Description: Dynamic columns for WooCommerce Memberships Profile Fields in Admin Columns Pro
 * Version: 2.3
 * Author: Abundant Designs LLC
 * Requires PHP: 7.4
 */

const AC_CT_FILE = __FILE__;

/**
 * Store the AC/ACP DI container when acp/init runs (Loader.php line 255).
 * Used by ProfileFieldsFactory to resolve FeatureSettingBuilderFactory and
 * DefaultSettingsBuilder when creating Column instances.
 */
add_action('acp/init', static function ( $container, $plugin ): void {
    ac_wc_memberships_set_ac_container($container);

    if ($container === null) {
        return;
    }

    require_once __DIR__ . '/classes/Column/Column.php';
    require_once __DIR__ . '/classes/Formatter/ValueFormatter.php';
    require_once __DIR__ . '/classes/Column/Editing.php';
    require_once __DIR__ . '/classes/Column/Search.php';
    require_once __DIR__ . '/classes/Column/Sorting.php';
    require_once __DIR__ . '/classes/ColumnFactories/ProfileFieldsFactory.php';

    $factory = new \AcColumnTemplate\ColumnFactories\ProfileFieldsFactory($container);
    \AC\ColumnFactories\Aggregate::add($factory);
}, 10, 2);

/**
 * @param \Psr\Container\ContainerInterface|null $container
 */
function ac_wc_memberships_set_ac_container( $container ): void {
    $GLOBALS['ac_wc_memberships_ac_container'] = $container;
}

/**
 * Get the AC/ACP DI container (set on acp/init). Used by ProfileFieldsFactory
 * when creating Column instances.
 *
 * @return \Psr\Container\ContainerInterface|null
 */
function ac_wc_memberships_get_ac_container() {
    return $GLOBALS['ac_wc_memberships_ac_container'] ?? null;
}

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

// Optionally: load a text domain
// load_plugin_textdomain('ac-column-template', false, __DIR__ . '/languages/');
