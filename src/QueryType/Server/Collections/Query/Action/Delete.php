<?php

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Collections\Result\DeleteResult;

/**
 * Class Delete.
 *
 * @see https://lucene.apache.org/solr/guide/collections-api.html#delete
 */
class Delete extends AbstractAction
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
     * The name of the collection to be deleted. This parameter is required.
     *
     * @param string $collection
     */
    public function setName(string $collection)
    {
        $this->setOption('name', $collection);
    }

    /**
     * Get the name of the collection to be deleted.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getOption('name');
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
