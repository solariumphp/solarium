<?php

namespace Solarium\QueryType\Server\Collections\Result;

class ClusterState
{
    /*
     * @var string[]
     */
    protected $aliases;

    /**
     * @var CollectionState[]
     */
    protected $collections;

    /**
     * @var string[]
     */
    protected $liveNodes;

    public function __construct(array $clusterStatus)
    {
        $this->aliases = isset($clusterStatus['aliases']) ? $clusterStatus['aliases'] : [];
        if (isset($clusterStatus['collections'])) {
            foreach ($clusterStatus['collections'] as $collectionName => $collectionState) {
                $this->collections[$collectionName] = new CollectionState(
                    [$collectionName => $collectionState],
                    $clusterStatus['live_nodes']
                );
            }
        }
        $this->liveNodes = isset($clusterStatus['live_nodes']) ? $clusterStatus['live_nodes'] : [];
    }

    /**
     * @return string[]
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @return ClusterState[]
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    /**
     * @param string $collectionName
     *
     * @return CollectionState
     *
     * @throws \Exception
     */
    public function getCollectionState(string $collectionName): CollectionState
    {
        if ($this->collectionExists($collectionName)) {
            return $this->collections[$collectionName];
        } else {
            throw new \Exception(sprintf("Collection '%s' does not exist.", $collectionName));
        }
    }

    /**
     * Check if collection exists in SolrCloud.
     *
     * @param string $collectionName
     *
     * @return bool
     */
    public function collectionExists(string $collectionName): bool
    {
        return isset($this->collections[$collectionName]);
    }

    /**
     * @return string[]
     */
    public function getLiveNodes()
    {
        return $this->liveNodes;
    }
}
