<?php

declare(strict_types=1);

namespace AcColumnTemplate\ColumnFactories;

use AC;
use AC\Collection\ColumnFactories;
use AcColumnTemplate\Column\Column;

/**
 * Column factory collection for WooCommerce Memberships profile fields.
 * Registers one Column instance per profile field with the AC aggregate (ACF-style).
 */
final class ProfileFieldsFactory implements AC\ColumnFactoryCollectionFactory
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function create(AC\TableScreen $table_screen): ColumnFactories
    {
        $collection = new ColumnFactories();

        $post_type = $table_screen instanceof AC\PostType
            ? (string)$table_screen->get_post_type()
            : null;

        // Target the post type 'wc_user_membership' list table
        if ('wc_user_membership' !== $post_type) {
            return $collection;
        }

        foreach ($this->get_fields() as $slug => $label) {
            $column = $this->container->make(
                Column::class,
                [
                    'profile_field_slug' => sanitize_key($slug) ?: 'default',
                    'profile_field_label' => trim($label) ?: __('Profile Field', 'ac-column-template'),
                ]
            );

            $collection->add($column);
        }

        return $collection;
    }

    private function get_fields(): array
    {
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

            if ( ! empty($slug)) {
                // Use the slug as label, or format it nicely
                $profile_fields[$slug] = ucwords(str_replace(['_', '-'], ' ', $slug));
            }
        }

        return $profile_fields;
    }
}
