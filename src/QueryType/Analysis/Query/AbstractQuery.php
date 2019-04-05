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
    public function setShowMatch(bool $show): self
    {
        $this->setOption('showmatch', $show);
        return $this;
    }

    /**
     * Get the showmatch option.
     *
     * @return bool
     */
    public function getShowMatch(): ?bool
    {
        return $this->getOption('showmatch');
    }
}
