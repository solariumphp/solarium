<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Collections\Result\DeleteResult;

/**
 * Class Delete.
 *
 * @see https://lucene.apache.org/solr/guide/collection-management.html#delete
 */
class Delete extends AbstractCDRAction
{
    /**
     * Returns the action type of the Collections API action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CollectionsQuery::ACTION_DELETE;
    }

    /**
     * Returns the namespace and class of the result class for the action.
     *
     * @return string
     */
    public function getResultClass(): string
    {
        return DeleteResult::class;
    }
}
