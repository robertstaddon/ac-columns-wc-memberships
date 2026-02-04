<?php

declare(strict_types=1);

namespace AcColumnTemplate\ColumnFactories;

use AC;
use AC\Collection\ColumnFactories;
use AC\Setting\DefaultSettingsBuilder;
use AC\TableScreen;
use AcColumnTemplate\Column\Column;
use ACP\Column\FeatureSettingBuilderFactory;

/**
 * Column factory collection for WooCommerce Memberships profile fields.
 * Registers one Column instance per profile field with the AC aggregate (ACF-style).
 */
final class ProfileFieldsFactory implements AC\ColumnFactoryCollectionFactory
{

    private object $container;

    public function __construct(object $container)
    {
        $this->container = $container;
    }

    public function create(AC\TableScreen $table_screen): ColumnFactories
    {
        $collection = new ColumnFactories();

        $list_screen_key = method_exists($table_screen, 'get_key')
            ? (string) $table_screen->get_key()
            : (string) $table_screen->get_id();

        if ('wc_user_membership' !== $list_screen_key) {
            return $collection;
        }

        $profile_fields = ac_wc_memberships_get_profile_fields();

        $feature_setting_builder_factory = $this->container->get(FeatureSettingBuilderFactory::class);
        $default_settings_builder = $this->container->get(DefaultSettingsBuilder::class);

        foreach ($profile_fields as $slug => $label) {
            $slug = \sanitize_key($slug);
            if ($slug === '') {
                $slug = 'default';
            }
            $label = \trim($label);
            if ($label === '') {
                $label = \__('Profile Field', 'ac-column-template');
            }

            $column = new Column(
                $slug,
                $label,
                $feature_setting_builder_factory,
                $default_settings_builder
            );
            $collection->add($column);
        }

        return $collection;
    }
}
