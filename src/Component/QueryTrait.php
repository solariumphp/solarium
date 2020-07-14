<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Builder\Select\QueryBuilder;
use Solarium\Builder\Select\QueryExpressionVisitor;
use Solarium\Exception\RuntimeException;

/**
 * Query Trait.
 */
trait QueryTrait
{
    /**
     * Set the query string.
     *
     * This overwrites the current value of a query or 'q' parameter.
     *
     * @param string $query
     * @param array  $bind  Bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function setQuery(string $query, array $bind = null): QueryInterface
    {
        if (null !== $bind) {
            $helper = $this->getHelper();
            $query = $helper->assemble($query, $bind);
        }

        return $this->setOption('query', trim($query));
    }

    /**
     * @param \Solarium\Builder\Select\QueryBuilder $builder
     *
     * @return \Solarium\Component\QueryInterface
     *
     * @throws \Solarium\Exception\RuntimeException
     */
    public function setQueryFromQueryBuilder(QueryBuilder $builder): QueryInterface
    {
        if (1 !== count($builder->getExpressions())) {
            throw new RuntimeException('The QueryBuilder can only contain one expression when setting the query. Use ExpressionBuilder::andX or ExpressionBuilder::orX to combine expressions.');
        }

        return $this->setOption('query', (new QueryExpressionVisitor())->dispatch($builder->getExpressions()[0]));
    }

    /**
     * Get query option.
     *
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->getOption('query');
    }
}
