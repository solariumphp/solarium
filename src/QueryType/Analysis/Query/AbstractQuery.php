<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
