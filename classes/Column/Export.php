<?php

declare(strict_types=1);

namespace AcColumnTemplate\Column;

use ACP\Export\Service;

/**
 * Export formatter for WooCommerce Memberships profile field column (Admin Columns Pro 7).
 * Used inside AC\FormatterCollection for CSV export.
 */
class Export implements Service
{

    private string $profile_field_slug;

    public function __construct(string $profile_field_slug = '')
    {
        $this->profile_field_slug = $profile_field_slug;
    }

    private function get_meta_key(): string
    {
        return '_wc_memberships_profile_field_' . $this->profile_field_slug;
    }

    public function get_value($id)
    {
        $user_id = (int) \get_post_field('post_author', $id);
        if (!$user_id) {
            return '';
        }
        return \get_user_meta($user_id, $this->get_meta_key(), true);
    }
}