<?php

namespace Solarium\QueryType\Analysis\Query;

use Solarium\Core\Query\AbstractQuery as BaseQuery;

/**
 * Base class for Analysis queries.
 */
abstract class AbstractQuery extends BaseQuery
{
    /**
     * Set the query string.
     *
     * When present, the text that will be analyzed. The analysis will mimic the query-time analysis.
     *
     * @param string $query
     * @param array  $bind  Optional bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function setQuery($query, $bind = null)
    {
        if (null !== $bind) {
            $query = $this->getHelper()->assemble($query, $bind);
        }

        return $this->setOption('query', trim($query));
    }

    /**
     * Get the query string.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->getOption('query');
    }

    /**
     * Set the showmatch option.
     *
     * @param bool $show
     *
     * @return self Provides fluent interface
     */
    public function setShowMatch($show)
    {
        return $this->setOption('showmatch', $show);
    }

    /**
     * Get the showmatch option.
     *
     * @return mixed
     */
    public function getShowMatch()
    {
        return $this->getOption('showmatch');
    }
}
