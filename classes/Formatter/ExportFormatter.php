<?php

declare(strict_types=1);

namespace AcColumnTemplate\Formatter;

use AC;
use AC\Type\Value;

/**
 * Formatter for WooCommerce Memberships profile field column export (CSV).
 * Retrieves value from membership owner's user meta; plain string, no HTML escaping.
 */
class ExportFormatter implements AC\Formatter
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
     * Format the value for export (AC7: single Value param, return Value).
     */
    public function format(Value $value): Value
    {
        $id = (int) $value->get_id();
        $user_id = (int) \get_post_field('post_author', $id);
        if (!$user_id) {
            return $value->with_value('');
        }

        $raw = \get_user_meta($user_id, $this->get_meta_key(), true);

        if ($raw === '' || $raw === null || $raw === false) {
            return $value->with_value('');
        }

        if (is_array($raw)) {
            $raw = implode(', ', array_filter($raw));
            if ($raw === '') {
                return $value->with_value('');
            }
        }

        return $value->with_value((string) $raw);
    }
}
