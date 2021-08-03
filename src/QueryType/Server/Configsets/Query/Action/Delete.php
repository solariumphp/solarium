<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Configsets\Query\Action;

use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Configsets\Result\ConfigsetsResult;
use Solarium\QueryType\Server\Query\Action\AbstractAction;
use Solarium\QueryType\Server\Query\Action\NameParameterTrait;

/**
 * Class Create.
 *
 * @see https://solr.apache.org/guide/configsets-api.html#configsets-create
 */
class Delete extends AbstractAction
{
    use NameParameterTrait;

    /**
     * Returns the action type of the Configsets API action.
     *
     * @return string
     */
    public function getType(): string
    {
        return ConfigsetsQuery::ACTION_DELETE;
    }

    /**
     * Returns the namespace and class of the result class for the action.
     *
     * @return string
     */
    public function getResultClass(): string
    {
        return ConfigsetsResult::class;
    }
}
