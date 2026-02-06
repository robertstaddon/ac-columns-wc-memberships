<?php

declare(strict_types=1);

namespace AcColumnTemplate\Column;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

/**
 * Sorting model. Adds sorting functionality to the column.
 */
class Sorting implements QueryBindings
{

    /**
     * @var string Profile field slug
     */
    private string $profile_field_slug;

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

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        /**
         * @see Bindings This object holds the SQL statements e.g. 'join, where, order by, group by'
         */
        $bindings = new Bindings();

        $meta_key = $this->get_meta_key();

        // 1. Join wp_usermeta table via post_author relationship
        $bindings->join(
            "LEFT JOIN {$wpdb->usermeta} AS ac_sort ON {$wpdb->posts}.post_author = ac_sort.user_id
                AND ac_sort.meta_key = " . $wpdb->prepare('%s', $meta_key)
        );

        // 2. Optionally: if you want your empty results at the bottom, you can
        // use this factory which will create the correct 'ORDER BY' statement for you
        $bindings->order_by(
            SqlOrderByFactory::create("ac_sort.meta_value", $order->name ?? 'ASC')
        );

        // 3. Optionally: set the 'GROUP BY' to groups the results
        $bindings->group_by(
            "{$wpdb->posts}.ID"
        );

        /**
         * The created Query Bindings will be parsed into SQL by one of these services:
         * @see \ACP\Query\Post     This service injects the SQL bindings into `WP_Query`
         * @see \ACP\Query\User     This service injects the SQL bindings into `WP_User_Query`
         * @see \ACP\Query\Term     This service injects the SQL bindings into `WP_Term_Query`
         * @see \ACP\Query\Comment  This service injects the SQL bindings into `WP_Comment_Query`
         */
        return $bindings;
    }

}