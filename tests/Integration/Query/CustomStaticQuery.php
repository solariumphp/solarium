<?php

namespace Solarium\Tests\Integration\Query;

use Solarium\QueryType\Select\Query\Query as SelectQuery;

/**
 * Custom query that overrides the setQuery() method with a static return type.
 */
class CustomStaticQuery extends SelectQuery
{
    /**
     * @return static Provides fluent interface
     */
    public function setQuery(string $query, array $bind = null): static
    {
        return parent::setQuery($query, $bind);
    }
}
