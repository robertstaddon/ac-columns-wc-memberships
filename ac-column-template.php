<?php
/**
 * Plugin Name: Admin Columns - WooCommerce Memberships Profile Fields
 * Plugin URI: https://admincolumns.com
 * Description: Dynamic columns for WooCommerce Memberships Profile Fields in Admin Columns Pro
 * Version: 2.4
 * Author: Abundant Designs LLC
 * Requires PHP: 7.4
 */

add_action('acp/init', static function ($container, \ACP\AdminColumnsPro $plugin): void {
    if ($plugin->get_version()->is_lt(new \AC\Plugin\Version('7'))) {
        return;
    }

    require_once __DIR__ . '/classes/Column/Column.php';
    require_once __DIR__ . '/classes/Formatter/ValueFormatter.php';
    require_once __DIR__ . '/classes/Column/Editing.php';
    require_once __DIR__ . '/classes/Column/Search.php';
    require_once __DIR__ . '/classes/Column/Sorting.php';
    require_once __DIR__ . '/classes/ColumnFactories/ProfileFieldsFactory.php';

    \AC\ColumnFactories\Aggregate::add(
        new \AcColumnTemplate\ColumnFactories\ProfileFieldsFactory($container)
    );
}, 10, 2);