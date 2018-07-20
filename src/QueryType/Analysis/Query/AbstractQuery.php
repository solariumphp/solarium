<?php

namespace Solarium\QueryType\Analysis\Query;

use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Core\Query\AbstractQuery as BaseQuery;

/**
 * Base class for Analysis queries.
 */
abstract class AbstractQuery extends BaseQuery implements QueryInterface
{
    use QueryTrait;

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
