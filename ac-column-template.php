<?php
/**
 * Plugin Name: Admin Columns - WooCommerce Memberships Profile Fields
 * Plugin URI: https://admincolumns.com
 * Description: Dynamic columns for WooCommerce Memberships Profile Fields in Admin Columns Pro
 * Version: 2.2
 * Author: Abundant Designs LLC
 * Requires PHP: 7.4
 */

const AC_CT_FILE = __FILE__;

/**
 * Store the AC/ACP DI container when acp/init runs (Loader.php line 255).
 * Used only when instantiating column subclasses to resolve FeatureSettingBuilderFactory
 * and DefaultSettingsBuilder for the Column constructor (not inside Column itself).
 */
add_action('acp/init', static function ( $container, $plugin ): void {
    ac_wc_memberships_set_ac_container($container);
}, 10, 2);

/**
 * @param \Psr\Container\ContainerInterface|null $container
 */
function ac_wc_memberships_set_ac_container( $container ): void {
    $GLOBALS['ac_wc_memberships_ac_container'] = $container;
}

/**
 * Get the AC/ACP DI container (set on acp/init). Used when creating column instances
 * to resolve constructor dependencies, not inside the Column class.
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

/**
 * Ensure a concrete Column subclass exists for the given profile field; return its FQCN.
 * AC7 expects class name strings, so we create one named subclass per profile field.
 * Dependencies are resolved here (at instantiation) and passed into the Column constructor.
 *
 * @param string $slug  Profile field slug (validated: non-empty, sanitized key)
 * @param string $label Profile field label (validated: non-empty)
 * @return string Fully qualified class name
 */
function ac_wc_memberships_profile_field_column_class(string $slug, string $label): string {
    $slug = \sanitize_key($slug);
    if ($slug === '') {
        $slug = 'default';
    }
    $label = \trim($label);
    if ($label === '') {
        $label = \__('Profile Field', 'ac-column-template');
    }

    $safe_slug = preg_replace('/[^a-zA-Z0-9_]/', '_', $slug);
    $short_name = 'ProfileFieldColumn_' . ($safe_slug !== '' ? $safe_slug : 'default');
    $fqcn = 'AcColumnTemplate\Column\\' . $short_name;

    if (!class_exists($fqcn)) {
        $slug_export = var_export($slug, true);
        $label_export = var_export($label, true);
        eval(
            "namespace AcColumnTemplate\Column; class {$short_name} extends Column { " .
            "public function __construct() { " .
            "\$c = \\ac_wc_memberships_get_ac_container(); " .
            "if (\$c === null) { throw new \\RuntimeException('Admin Columns Pro container not available.'); } " .
            "parent::__construct({$slug_export}, {$label_export}, \$c->get(\\ACP\\Column\\FeatureSettingBuilderFactory::class), \$c->get(\\AC\\Setting\\DefaultSettingsBuilder::class)); " .
            "} }"
        );
    }

    return $fqcn;
}

// Register column types for Admin Columns Pro 7
add_filter('ac/column/types/pro', static function (array $factories, AC\TableScreen $table_screen): array {
    // Only register for wc_user_membership post type (User Memberships)
    $list_screen_key = method_exists($table_screen, 'get_key') ? (string) $table_screen->get_key() : (string) $table_screen->get_id();
    if ('wc_user_membership' !== $list_screen_key) {
        return $factories;
    }

    require_once __DIR__ . '/classes/Column/Column.php';
    require_once __DIR__ . '/classes/Formatter/ValueFormatter.php';
    require_once __DIR__ . '/classes/Column/Editing.php';
    require_once __DIR__ . '/classes/Column/Search.php';
    require_once __DIR__ . '/classes/Column/Sorting.php';

    $profile_fields = ac_wc_memberships_get_profile_fields();

    foreach ($profile_fields as $slug => $label) {
        $factories[] = ac_wc_memberships_profile_field_column_class($slug, $label);
    }

    return $factories;
}, 10, 2);

// 2. Optionally: load a text domain
// load_plugin_textdomain('ac-column-template', false, __DIR__ . '/languages/');
