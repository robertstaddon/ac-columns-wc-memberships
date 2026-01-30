<?php

declare(strict_types=1);

namespace AcColumnTemplate\Formatter;

use AC;

/**
 * Formatter for WooCommerce Memberships profile field column value.
 * Retrieves value from membership owner's user meta and formats for display.
 */
class ValueFormatter implements AC\Formatter
{

    private string $profile_field_slug;

    public function __construct(string $profile_field_slug)
    {
        $this->profile_field_slug = $profile_field_slug;
    }

    private function get_meta_key(): string
    {
        return '_wc_memberships_profile_field_' . $this->profile_field_slug;
    }

    /**
     * Format the value for display in the list table.
     *
     * @param string|null $value  Pre-existing value (may be empty when this formatter is the source).
     * @param int         $id     Membership post ID.
     * @param AC\Column  $column Column instance.
     */
    public function format($value, $id, AC\Column $column): string
    {
        $raw = $value;
        if ($raw === null || $raw === '') {
            $user_id = (int) \get_post_field('post_author', (int) $id);
            if (!$user_id) {
                return (string) $column->get_empty_char();
            }
            $raw = \get_user_meta($user_id, $this->get_meta_key(), true);
        }

        if ($raw === '' || $raw === null || $raw === false) {
            return (string) $column->get_empty_char();
        }

        if (is_array($raw)) {
            $raw = implode(', ', array_filter($raw));
            if ($raw === '') {
                return (string) $column->get_empty_char();
            }
        }

        return \esc_html((string) $raw);
    }
}
