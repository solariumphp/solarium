<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Configsets\Query\Action;

use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Configsets\Result\ListConfigsetsResult;
use Solarium\QueryType\Server\Query\Action\AbstractAction;

/**
 * Class ListConfigsets (name "List" is reserved).
 *
 * @see https://solr.apache.org/guide/configsets-api.html#configsets-list
 */
class ListConfigsets extends AbstractAction
{
    /**
     * Returns the action type of the Configsets API action.
     *
     * @return string
     */
    public function getType(): string
    {
        return ConfigsetsQuery::ACTION_LIST;
    }

    /**
     * Returns the namespace and class of the result class for the action.
     *
     * @return string
     */
    public function getResultClass(): string
    {
        return ListConfigsetsResult::class;
    }
}
