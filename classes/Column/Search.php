<?php

declare(strict_types=1);

namespace AcColumnTemplate\Column;

use AC\Helper\Select\Options;
use ACP\Query;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Comparison\Values;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

/**
 * Search class. Adds smart filtering functionality to the column.
 * Provides a dropdown filter with all available values for the profile field.
 */
class Search extends Comparison implements Values
{

    /**
     * @var string Profile field slug
     */
    private $profile_field_slug;

    /**
     * @var array Cached list of available values
     */
    private $values_cache = null;

    public function __construct(string $profile_field_slug = '')
    {
        $this->profile_field_slug = $profile_field_slug;

        // For dropdown/select, we only need EQ (equal) operator
        $operators = new Operators([
            Operators::EQ,
        ]);

        // Available value types:
        // Value::STRING = Value is a string
        // Value::DATE = Value is a date
        // Value::INT = Value is a whole number e.g. `5`
        // Value::DECIMAL = Value is a number with decimals e.g. `5.1`
        $value = Value::STRING;

        parent::__construct($operators, $value);
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

    /**
     * Get all unique values for this profile field from user meta.
     * Used to populate the dropdown filter.
     *
     * @return Options Options object with value => label pairs
     */
    public function get_values(): Options
    {
        // Return cached values if available
        if ($this->values_cache !== null) {
            return Options::create_from_array($this->values_cache);
        }

        // Safety check: return empty options if slug is empty
        if (empty($this->profile_field_slug)) {
            $this->values_cache = [];
            return Options::create_from_array([]);
        }

        global $wpdb;

        $meta_key = $this->get_meta_key();
        $values = [];

        // Query all distinct values for this profile field from user meta
        $meta_values = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT DISTINCT meta_value 
                FROM {$wpdb->usermeta} 
                WHERE meta_key = %s 
                AND meta_value != '' 
                AND meta_value IS NOT NULL
                ORDER BY meta_value ASC",
                $meta_key
            )
        );

        // Create value => label pairs (using value as both key and label)
        foreach ($meta_values as $meta_value) {
            // Handle serialized arrays
            if (is_serialized($meta_value)) {
                $unserialized = maybe_unserialize($meta_value);
                if (is_array($unserialized)) {
                    $meta_value = implode(', ', array_filter($unserialized));
                }
            }

            // Use the value as both key and label
            $values[$meta_value] = $meta_value;
        }

        // Cache the values array (not the Options object)
        $this->values_cache = $values;

        // Return Options object created from the array
        return Options::create_from_array($values);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        /**
         * @see Bindings This object holds the SQL statements e.g. 'join, where, order by, group by' and the 'meta_query'
         */
        $binding = new Bindings();

        /**
         * Altering the query with custom SQL to search user meta
         * Profile fields are stored in wp_usermeta, but we need to join via wp_posts.post_author
         * @see Query\Post This service handler parses the SQL bindings into `WP_Query`
         * @see WP_Query::get_posts This object runs the SQL query
         */
        global $wpdb;

        $meta_key = $this->get_meta_key();

        // 1. Join wp_usermeta table via post_author relationship
        $binding->join(
            "INNER JOIN {$wpdb->usermeta} AS ac_filter ON {$wpdb->posts}.post_author = ac_filter.user_id 
                AND ac_filter.meta_key = " . $wpdb->prepare('%s', $meta_key)
        );

        // 2. Create the `WHERE` clause. Use the `ComparisonFactory` to create a where-statement by operator (equal, contains etc.)
        $where = ComparisonFactory::create(
            'ac_filter.meta_value',
            $operator,
            $value
        )->prepare();

        $binding->where($where);

        /**
         * The created Query Bindings will be parsed into SQL by one of these services:
         * @see \ACP\Query\Post This service injects the SQL bindings into `WP_Query`
         * @see \ACP\Query\User This service injects the SQL bindings into `WP_User_Query`
         * @see \ACP\Query\Term This service injects the SQL bindings into `WP_Term_Query`
         * @see \ACP\Query\Comment This service injects the SQL bindings into `WP_Comment_Query`
         */
        return $binding;
    }

}