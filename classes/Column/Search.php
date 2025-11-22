<?php

namespace AcColumnTemplate\Column;

use ACP\Query;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

/**
 * Search class. Adds smart filtering functionality to the column.
 */
class Search extends Comparison
{

    /**
     * @var string Profile field slug
     */
    private $profile_field_slug;

    public function __construct(string $profile_field_slug = '')
    {
        $this->profile_field_slug = $profile_field_slug;

        $operators = new Operators([

            // Available operators:
            // Operators::EQ = equal
            // Operators::NEQ = not Equal
            // Operators::CONTAINS = Matches a part of a string
            // Operators::NOT_CONTAINS
            // Operators::GT = Greater than
            // Operators::LT = Less than
            // Operators::IS_EMPTY
            // Operators::NOT_IS_EMPTY
            // Operators::BETWEEN
            Operators::EQ,
            Operators::CONTAINS,
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