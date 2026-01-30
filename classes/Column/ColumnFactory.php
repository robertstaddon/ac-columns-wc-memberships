<?php

declare(strict_types=1);

namespace AcColumnTemplate\Column;

/**
 * Factory that creates a Column instance for a specific WooCommerce Memberships profile field.
 * Used for Admin Columns Pro 7 dynamic column registration (one factory per profile field).
 */
class ColumnFactory
{

    private string $profile_field_slug;

    private string $profile_field_label;

    public function __construct(string $profile_field_slug, string $profile_field_label)
    {
        $this->profile_field_slug = $profile_field_slug;
        $this->profile_field_label = $profile_field_label;
    }

    /**
     * Create the column instance. Invoked by Admin Columns when building the column list.
     */
    public function __invoke(): Column
    {
        return new Column($this->profile_field_slug, $this->profile_field_label);
    }
}
