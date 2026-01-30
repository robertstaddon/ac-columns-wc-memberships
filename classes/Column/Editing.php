<?php

declare(strict_types=1);

namespace AcColumnTemplate\Column;

use ACP;
use ACP\Editing\View;

/**
 * Editing class. Adds editing functionality to the column.
 */
class Editing implements ACP\Editing\Service
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

    public function get_value(int $id)
    {
        // Get the post author ID (user ID) from the membership post
        $user_id = (int) \get_post_field('post_author', $id);
        if (!$user_id) {
            return '';
        }
        return \get_user_meta($user_id, $this->get_meta_key(), true);
    }

    /**
     * Set the type of input field you want to use e.g. 'text', 'number', 'select' etc.
     *
     * @param string $context 'single' (for inline edit) or 'bulk' (for bulk editing)
     *
     * @return View|null
     */
    public function get_view(string $context): ?View
    {
        /**
         * Available input types:
         * @see View\Text
         * @see View\TextArea
         * @see View\Number
         * @see View\Image
         * @see View\Url
         * @see View\Email
         * @see View\Wysiwyg
         * @see View\Select
         * @see View\Toggle
         * @see View\Media
         * @see View\Password
         * @see View\Taxonomy
         * @see View\Color
         * @see View\Date
         * @see View\DateTime
         * @see View\CheckboxList
         * @see View\ComputedNumber
         * @see View\AjaxSelect
         * @see View\Audio
         * @see View\Video
         */
        $view = new View\Text();

        // Example of a dropdown select:
        // $view = new View\Select([1 => 'Option #1', 2 => 'Option #2']);

        // (Optional) use View specific modifiers
        //$view->set_clear_button( true );
        //$view->set_placeholder( 'Custom placeholder' );
        //$view->set_required( true );

        // (Optional) return a different view or disable editing based on context: 'bulk' or 'single' (index)
        // return $context === 'bulk' ? $view : null;

        return $view;
    }

    /**
     * Saves the value after using inline or bulk-edit
     *
     * @param int   $id   Object ID (membership post ID)
     * @param mixed $data Value to be saved
     */
    public function update(int $id, $data): void
    {
        // Get the post author ID (user ID) from the membership post
        $user_id = (int) \get_post_field('post_author', $id);
        if (!$user_id) {
            return;
        }
        \update_user_meta($user_id, $this->get_meta_key(), $data);
    }

}