<?php

namespace Solarium\Tests\Integration\Query;

use Solarium\Component\QueryInterface;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

/**
 * Custom query that overrides the setQuery() method with a QueryInterface return type.
 */
class CustomQueryInterfaceQuery extends SelectQuery
{
    /**
     * @return self Provides fluent interface
     */
    public function setQuery(string $query, array $bind = null): QueryInterface
    {
        return parent::setQuery($query, $bind);
    }
}
