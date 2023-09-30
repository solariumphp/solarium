<?php

namespace Solarium\Tests\Integration\Query;

use Solarium\QueryType\Select\Query\Query as SelectQuery;

/**
 * Custom query that overrides the setQuery() method with a self return type.
 */
class CustomSelfQuery extends SelectQuery
{
    /**
     * @return self Provides fluent interface
     */
    public function setQuery(string $query, array $bind = null): self
    {
        return parent::setQuery($query, $bind);
    }
}
