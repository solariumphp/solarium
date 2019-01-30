<?php

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Collections\Result\ReloadResult;

/**
 * Class Reload for reloading a collection.
 *
 * @see https://lucene.apache.org/solr/guide/collections-api.html#reload
 */
class Reload extends AbstractAction
{
    /**
     * Returns the action type of the Collections API action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CollectionsQuery::ACTION_RELOAD;
    }

    /**
     * The name of the collection to reload. This parameter is required.
     *
     * @param string $collection
     *
     * @return self Provides fluent interface
     */
    public function setName(string $name)
    {
        $this->setOption('name', $name);
        return $this;
    }

    /**
     * Get collection name.
     *
     * @return string
     */
    public function getName()
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
        return ReloadResult::class;
    }
}
