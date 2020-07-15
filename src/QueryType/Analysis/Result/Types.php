<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Analysis\Result;

/**
 * Analysis types result.
 */
class Types extends ResultList
{
    /**
     * List items.
     *
     * @var ResultList[]
     */
    protected $items;

    /**
     * Get index analysis list.
     *
     * @return ResultList|null
     */
    public function getIndexAnalysis(): ?ResultList
    {
        foreach ($this->items as $item) {
            if ('index' === $item->getName()) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Get query analysis list.
     *
     * @return ResultList|null
     */
    public function getQueryAnalysis(): ?ResultList
    {
        foreach ($this->items as $item) {
            if ('query' === $item->getName()) {
                return $item;
            }
        }

        return null;
    }
}
