<?php

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
    public function getIndexAnalysis()
    {
        foreach ($this->items as $item) {
            if ('index' == $item->getName()) {
                return $item;
            }
        }
    }

    /**
     * Get query analysis list.
     *
     * @return ResultList|null
     */
    public function getQueryAnalysis()
    {
        foreach ($this->items as $item) {
            if ('query' == $item->getName()) {
                return $item;
            }
        }
    }
}
