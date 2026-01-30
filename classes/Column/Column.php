<?php

declare(strict_types=1);

namespace AcColumnTemplate\Column;

use AC;
use AC\Setting\DefaultSettingsBuilder;
use ACP;
use ACP\Column\FeatureSettingBuilderFactory;
use AcColumnTemplate\Formatter\ValueFormatter;

/**
 * Column for a single WooCommerce Memberships profile field (Admin Columns Pro 7).
 *
 * @link https://docs.admincolumns.com/article/21-how-to-create-my-own-column
 */
class Column extends ACP\Column\AdvancedColumnFactory
{

    private string $profile_field_slug;

    private string $profile_field_label;

    public function __construct(string $profile_field_slug = '', string $profile_field_label = '')
    {
        $this->profile_field_slug = $profile_field_slug;
        $this->profile_field_label = $profile_field_label;

        $container = \ac_wc_memberships_get_ac_container();
        if ($container === null) {
            throw new \RuntimeException('Admin Columns Pro container not available. Ensure ac-columns-wc-memberships loads after Admin Columns Pro and acp/init has run.');
        }
        $feature_setting_builder_factory = $container->get(FeatureSettingBuilderFactory::class);
        $default_settings_builder = $container->get(DefaultSettingsBuilder::class);

        parent::__construct($feature_setting_builder_factory, $default_settings_builder);
    }

    public function get_label(): string
    {
        return $this->profile_field_label !== ''
            ? $this->profile_field_label
            : \__('Profile Field', 'ac-column-template');
    }

    public function get_column_type(): string
    {
        $type = 'ac-wc-memberships-profile-field';
        if ($this->profile_field_slug !== '') {
            $type .= '-' . \sanitize_key($this->profile_field_slug);
        }
        return $type;
    }

    /**
     * Profile field slug. When loaded from saved config, slug is derived from column type.
     */
    public function get_profile_field_slug(): string
    {
        if ($this->profile_field_slug !== '') {
            return $this->profile_field_slug;
        }
        $type = method_exists($this, 'get_type') ? $this->get_type() : $this->get_column_type();
        $prefix = 'ac-wc-memberships-profile-field-';
        if (strpos($type, $prefix) === 0) {
            $slug = substr($type, strlen($prefix));
            $this->profile_field_slug = $slug;
            return $slug;
        }
        return '';
    }

    public function get_meta_key(): string
    {
        return '_wc_memberships_profile_field_' . $this->get_profile_field_slug();
    }

    public function get_group(): string
    {
        return 'woocommerce';
    }

    protected function get_formatters(AC\Setting\Config $config): AC\FormatterCollection
    {
        return new AC\FormatterCollection([
            new ValueFormatter($this->get_profile_field_slug()),
        ]);
    }

    protected function get_editing(AC\Setting\Config $config): ?ACP\Editing\Service
    {
        return new Editing($this->get_profile_field_slug());
    }

    protected function get_sorting(AC\Setting\Config $config): ?ACP\Sorting\Model\QueryBindings
    {
        return new Sorting($this->get_profile_field_slug());
    }

    protected function get_search(AC\Setting\Config $config): ?ACP\Search\Comparison
    {
        return new Search($this->get_profile_field_slug());
    }

    protected function get_export(AC\Setting\Config $config): ?AC\FormatterCollection
    {
        return new AC\FormatterCollection([
            new \AcColumnTemplate\Formatter\ExportFormatter($this->get_profile_field_slug()),
        ]);
    }
}
