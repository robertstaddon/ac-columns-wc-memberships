<?php

namespace AcColumnTemplate\Column;

use ACP\Export\Service;

/**
 * Export class. Adds export functionality to the column.
 */
class Export implements Service
{

    /**
     * @var string Profile field slug
     */
    private $profile_field_slug;

    public function __construct(string $profile_field_slug = '')
    {
        $this->profile_field_slug = $profile_field_slug;
    }

    /**
     * Get the meta key for this profile field.
     *
     * @return string
     */
    private function get_meta_key(): string
    {
        return '_wc_memberships_profile_field_' . $this->profile_field_slug;
    }

    public function get_value($id)
    {
        // Get the post author ID (user ID) from the membership post
        $post = get_post($id);
        if (!$post || !$post->post_author) {
            return '';
        }

        $user_id = (int) $post->post_author;
        $meta_key = $this->get_meta_key();
        
        // Return the value you would like to be exported from user meta
        return get_user_meta($user_id, $meta_key, true);
    }

}