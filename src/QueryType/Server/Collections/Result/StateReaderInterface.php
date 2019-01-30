<?php

namespace Solarium\QueryType\Server\Collections\Result;

// @todo CollectionEndpoint is missing!
use Solarium\Cloud\Core\Client\CollectionEndpoint;
use Symfony\Component\Cache\Adapter\AdapterInterface;

interface StateReaderInterface
{
    /**
     * Returns a list of all the collections and their aliases.
     *
     * @return array
     */
    public function getCollectionAliases(): array;

    /**
     * Returns a list of all the collections.
     *
     * @return array
     */
    public function getCollectionList(): array;

    /**
     * Returns a the raw cluster state.
     *
     * @return array
     */
    public function getClusterState(): ClusterState;

    /**
     * @return array
     */
    public function getClusterProperties(): array;

    /**
     * @param string $collection
     *
     * @return CollectionState
     */
    public function getCollectionState(string $collection): CollectionState;

    /**
     * @return array
     */
    public function getLiveNodes(): array;

    public function getActiveBaseUris(string $collection = null): array;

    public function getCollectionShardLeadersBaseUri(string $collection): array;

    /**
     * Returns a list of all the end points.
     *
     * @return array
     */
    public function getEndpoints(): array;

    public function getCollectionEndpoint(string $collection): CollectionEndpoint;

    /**
     * Returns the official collection name
     *
     * @param  string $collection Collection name
     *
     * @return string Name of the collection. Returns an empty string if it's not found.
     */
    public function getCollectionName(string $collection): string;

    /**
     * Reads the state from the server.
     */
    public function readState();

    /**
     * @return AdapterInterface
     */
    public function getCache(): AdapterInterface;

    /**
     * @param AdapterInterface $cache
     */
    public function setCache(AdapterInterface $cache);
}
