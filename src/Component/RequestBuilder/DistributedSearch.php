<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\DistributedSearch as DistributedSearchComponent;
use Solarium\Core\Client\Request;

/**
 * Add select component distributedsearch to the request.
 */
class DistributedSearch implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for DistributedSearch.
     *
     * @param DistributedSearchComponent $component
     * @param Request                    $request
     *
     * @return Request
     */
    public function buildComponent($component, $request)
    {
        // add shards to request
        $shards = array_values($component->getShards());
        if (count($shards)) {
            $request->addParam('shards', implode(',', $shards));
        }

        $replicas = array_values($component->getReplicas());

        if (count($replicas)) {
            $value = ($request->getParam('shards')) ? $request->getParam('shards').','.implode('|', $replicas) : implode('|', $replicas);

            $request->addParam('shards', $value, true);
        }

        $request->addParam('shards.qt', $component->getShardRequestHandler());

        // add collections to request
        $collections = array_values($component->getCollections());
        if (count($collections)) {
            $request->addParam('collection', implode(',', $collections));
        }

        return $request;
    }
}
