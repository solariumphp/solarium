<?php

namespace Solarium\QueryType\Server\Collections\Result;

use DateInterval;
use Solarium\Cloud\Core\Client\CollectionEndpoint;
use Solarium\Cloud\Exception\ZookeeperException;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\SolrCloudException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Zookeeper;

/**
 * Class ZkStateReader.
 */
class ZkStateReader implements StateReaderInterface
{
    const BASE_URL_PROP = 'base_url';

    const NODE_NAME_PROP = 'node_name';

    const CORE_NODE_NAME_PROP = 'core_node_name';

    const ROLES_PROP = 'roles';

    const STATE_PROP = 'state';

    const CORE_NAME_PROP = 'core';

    const COLLECTION_PROP = 'collection';

    const ELECTION_NODE_PROP = 'election_node';

    const SHARD_ID_PROP = 'shard';

    const SHARDS_PROP = 'shards';

    const STATE_ACTIVE = 'active';

    const REPLICA_PROP = 'replica';

    const REPLICAS_PROP = 'replicas';

    const RANGE_PROP = 'range';

    const SHARD_STATE_PROP = 'shard_state';

    const SHARD_PARENT_PROP = 'shard_parent';

    const NUM_SHARDS_PROP = 'numShards';

    const LEADER_PROP = 'leader';

    const ROUTER_PROP = 'router';

    const PROPERTY_PROP = 'property';

    const PROPERTY_VALUE_PROP = 'property.value';

    const MAX_AT_ONCE_PROP = 'maxAtOnce';

    const MAX_WAIT_SECONDS_PROP = 'maxWaitSeconds';

    const COLLECTIONS_ZKNODE = '/collections';

    const LIVE_NODES_ZKNODE = '/live_nodes';

    const ALIASES = '/aliases.json';

    const ALIASES_PROP = 'aliases';

    const CLUSTER_STATE = '/clusterstate.json';

    const CLUSTER_PROPS = '/clusterprops.json';

    const COLLECTION_STATE = 'state.json';

    const REJOIN_AT_HEAD_PROP = 'rejoinAtHead';

    const SOLR_SECURITY_CONF_PATH = '/security.json';

    const REPLICATION_FACTOR = 'replicationFactor';

    const MAX_SHARDS_PER_NODE = 'maxShardsPerNode';

    const AUTO_ADD_REPLICAS = 'autoAddReplicas';

    const MAX_CORES_PER_NODE = 'maxCoresPerNode';

    const ROLES = '/roles.json';

    const CONFIGS_ZKNODE = '/configs';

    const CONFIGNAME_PROP = 'configName';

    const LEGACY_CLOUD = 'legacyCloud';

    const URL_SCHEME = 'urlScheme';

    const GET_LEADER_RETRY_INTERVAL_MS = 50;

    const GET_LEADER_RETRY_DEFAULT_TIMEOUT = 4000;

    const LEADER_ELECT_ZKNODE = 'leader_elect';

    const SHARD_LEADERS_ZKNODE = 'leaders';

    const ELECTION_NODE = 'election';

    /** @var array Aliases of collections */
    protected $aliases;

    /** @var array Collections */
    protected $collections;

    /** @var array Collections tracked in the legacy (shared) state format, reflects the contents of clusterstate.json. */
    protected $legacyCollectionStates = [];

    // @todo unused, check if needed.
    /** @var int Last seen ZK version of clusterstate.json. */
    protected $legacyClusterStateVersion = 0;

    /** @var array Each individual collection state combined, without the legacy clusterstate.json values */
    protected $collectionStates = [];

    /** @var array A view of the current state of all collections; combines all the different state sources into a single view. */
    protected $clusterState;

    /** @var array Shard leaders for every collection */
    protected $collectionShardLeaders;

    /** @var array All the live nodes */
    protected $liveNodes = [];

    /** @var array Cluster properties from clusterproperties.json */
    protected $clusterProperties;

    // @todo unused, check if needed.
    protected $collectionWatches;

    // @todo unused, check if needed.
    protected $configManager;

    /** @var array Security information from security.json */
    protected $securityData;

    /**
     * @var string[] Zookeeper hosts
     */
    protected $zkHosts;

    /** @var Zookeeper Zookeeper client */
    protected $zkClient;

    /** @var int Zookeeper client timeout */
    private $zkTimeout;

    /** @var array Zookeeper client callback container */
    protected $zkCallback = array();

    /** @var AdapterInterface Cache object holding Zookeeper state information */
    protected $cache;

    /** @var @param null|int|\DateInterval $cacheExpiration Seconds or date interval when cache expires */
    protected $cacheExpiration;

    /**
     * ZkStateReader constructor.
     *
     * @param array                  $zkHosts
     * @param int                    $zkTimeout
     * @param null|AdapterInterface  $cache           Caching object
     * @param null|int|\DateInterval $cacheExpiration Seconds or date interval when cache expires
     *
     * @throws ZookeeperException
     */
    public function __construct(array $zkHosts, int $zkTimeout = 10000)
    {
        $this->zkHosts = $zkHosts;
        $this->zkTimeout = $zkTimeout;

        $this->setZkClient(new Zookeeper($this->zkHosts, null, $this->zkTimeout));

        if (!$this->getCacheData()) {
            try {
                $this->readState();
            } catch (\Exception $e) {
                throw new ZookeeperException($e->getMessage());
            }
        }
    }

    /**
     * @return Zookeeper
     */
    public function getZkClient(): Zookeeper
    {
        return $this->zkClient;
    }

    /**
     * @param Zookeeper $zkClient
     */
    public function setZkClient(Zookeeper $zkClient)
    {
        $this->zkClient = $zkClient;
    }

    /**
     * @return array
     */
    public function getCollectionAliases(): array
    {
        if (null !== $this->aliases && isset($this->aliases[self::COLLECTION_PROP])) {
            return $this->aliases[self::COLLECTION_PROP];
        }

        return [];
    }

    /**
     * List of collections.
     *
     * @return array
     */
    public function getCollectionList(): array
    {
        if (null !== $this->collections) {
            return array_keys($this->collections);
        }

        return [];
    }

    /**
     * @return ClusterState
     */
    public function getClusterState(): ClusterState
    {
        if (null !== $this->clusterState) {
            return $this->clusterState;
        }

        return [];
    }

    /**
     * @return array
     */
    public function getClusterProperties(): array
    {
        if (null !== $this->clusterProperties) {
            return $this->clusterProperties;
        }

        return [];
    }

    /**
     * @return array Live nodes
     */
    public function getLiveNodes(): array
    {
        if (null !== $this->liveNodes) {
            return $this->liveNodes;
        }

        return [];
    }

    /**
     * Return active base URIs for all or a specific collection.
     *
     * @param string $collection
     *
     * @return array
     *
     * @throws SolrCloudException
     * @throws ZookeeperException
     */
    public function getActiveBaseUris(string $collection = null): array
    {
        if (null != $collection) {
            $collection = $this->getCollectionName($collection);
            $states[$collection] = $this->getClusterState()->getCollectionState($collection);
        } else {
            $states = $this->clusterState;
        }

        $replicas = [];

        if (!empty($states)) {
            foreach ($states as $collectionId => $state) {
                foreach ($state[self::SHARDS_PROP] as $shardname => $shard) {
                    foreach ($shard[self::REPLICAS_PROP] as $replicaName => $replica) {
                        if (isset($replica[self::STATE_PROP]) && self::STATE_ACTIVE === $replica[self::STATE_PROP]) {
                            $baseUri = $replica[self::BASE_URL_PROP];
                            if (!in_array($baseUri, $replicas, true)) {
                                $replicas[$replica[self::NODE_NAME_PROP].'_'.$collectionId] = $baseUri;
                            }
                        }
                    }
                }
            }
        } else {
            throw new \Solarium\Exception\SolrCloudException("Collection '$collection' does not exist.'");
        }

        return $replicas;
    }

    /**
     * @todo This method is not relevant. leaders are specific for shards, not collections alone.
     *
     * @param string $collection Collection name
     *
     * @return array List of leaders of collection shards
     *
     * @throws ZookeeperException
     * @throws SolrCloudException
     */
    public function getCollectionShardLeadersBaseUri(string $collection): array
    {
        $collection = $this->getCollectionName($collection);
        $state = $this->getCollectionState($collection);

        $leaders = [];

        if (null !== $state) {
            foreach ($state[self::SHARDS_PROP] as $shardname => $shard) {
                foreach ($shard[self::REPLICAS_PROP] as $replicaName => $replica) {
                    if (isset($replica[self::LEADER_PROP]) && 'true' === $replica[self::LEADER_PROP]) {
                        $baseUri = $replica[self::BASE_URL_PROP];
                        if (!in_array($baseUri, $leaders, true)) {
                            $leaders[$replica[self::NODE_NAME_PROP].'_'.$collection] = $baseUri;
                        }
                    }
                }
            }
        } else {
            throw new SolrCloudException("Collection '$collection' does not exist.'");
        }

        return $leaders;
    }

    // @todo make it setEndpoints

    /**
     * Return all active CollectionStates.
     *
     * @return CollectionState[] An array of CollectionStates where the keys are the ids of the CollectionStates
     *
     * @throws \Solarium\Cloud\Exception\ZookeeperException
     */
    public function getEndpoints(): array
    {
        $endpoints = [];

        foreach ($this->collections as $collection) {
            $endpoints[$collection] = $this->getCollectionState($collection);
        }

        return $endpoints;
    }

    /**
     * Return all active collection CollectionStates.
     *
     * @param string $collection Collection name
     *
     * @return CollectionState
     *
     * @throws ZookeeperException
     */
    public function getCollectionState(string $collection): CollectionState
    {
        $collection = $this->getCollectionName($collection);
        if (null != $this->clusterState) {
            if (!isset($this->clusterState[$collection])) {
                throw new ZookeeperException("Collection '$collection' does not exist.'");
            }
            // @todo This should be a CollectionState, not ClusterState
            $this->getClusterState();
        }

        throw new ZookeeperException('The cluster state is unknown.');
    }

    /**
     * @param string $collection
     *
     * @return CollectionEndpoint
     *
     * @throws InvalidArgumentException
     * @throws ZookeeperException
     */
    public function getCollectionEndpoint(string $collection): CollectionEndpoint
    {
        $collection = $this->getCollectionName($collection);
        // @todo it would be great to have the CollectionEndpoint update when the state is updated

        return new CollectionEndpoint($collection, $this);
    }

    /**
     * Returns the official collection name.
     *
     * @param string $collection Collection name
     *
     * @return string Name of the collection. Returns an empty string if it's not found.
     *
     * @throws SolrCloudException
     */
    public function getCollectionName(string $collection): string
    {
        if (!in_array($collection, $this->collections, true)) {
            $aliases = $this->getCollectionAliases();
            if (!empty($aliases && array_key_exists($collection, $aliases))) {
                return $aliases[$collection];
            }
            throw new SolrCloudException("Solr collection with name '$collection' not found.'");
        }

        return $collection;
    }

    /**
     * @param array  $zkHosts
     * @param string $chroot
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function buildZkHostString(array $zkHosts, string $chroot = ''): string
    {
        if (!is_array($zkHosts) || empty($zkHosts)) {
            throw new InvalidArgumentException('Cannot create CloudSearchClient without valid ZooKeeper host; none specified!');
        }
        $zkHostString = '';
        $lastIndexValue = count($zkHosts) - 1;
        $i = 0;
        foreach ($zkHosts as $zkHost) {
            $zkHostString .= $zkHost;
            if ($i < $lastIndexValue) {
                $zkHostString .= ',';
            }
            ++$i;
        }

        if (strlen($chroot) > 0) {
            if ('/' === $chroot[0]) {
                $zkHostString .= $chroot;
            } else {
                throw new InvalidArgumentException('The chroot must start with a forward slash.');
            }
        }

        return $zkHostString;
    }

    /**
     * Destruct ZkStateReader object.
     */
    public function __destruct()
    {
        $this->zkClient = null;
    }

    /**
     * Reads data from Zookeeper.
     *
     * @throws \Solarium\Cloud\Exception\ZookeeperException
     */
    public function readState()
    {
        $this->readLiveNodes();
        $this->readAliases();
        $this->readCollectionList();
        $this->readClusterState();
        $this->readSecurityData();

        if (null !== $this->cache) {
            $this->fillCacheData();
        }
    }

    /**
     * @return AdapterInterface
     */
    public function getCache(): AdapterInterface
    {
        return $this->cache;
    }

    /**
     * @param AdapterInterface $cache
     */
    public function setCache(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Invalidates the Zookeeper state cache.
     */
    public function invalidateCache()
    {
        if (null != $this->cache) {
            $this->cache->invalidateTags(['zkstate']);
        }
    }

    /**
     * Check if all the data is in cache.
     *
     * @return bool
     */
    protected function getCacheData(): bool
    {
        try {
            if (null !== $this->cache) {
                if (!$this->cache->getItem('zkstate.aliases')->isHit()) {
                    return false;
                } else {
                    $this->aliases = $this->cache->getItem('zkstate.aliases')->get();
                }
                if (!$this->cache->getItem('zkstate.collections')->isHit()) {
                    return false;
                } else {
                    $this->collections = $this->cache->getItem('zkstate.collections')->get();
                }
                if (!$this->cache->getItem('zkstate.legacyCollectionStates')->isHit()) {
                    return false;
                } else {
                    $this->legacyCollectionStates = $this->cache->getItem('zkstate.legacyCollectionStates')->get();
                }
                if (!$this->cache->getItem('zkstate.collectionStates')->isHit()) {
                    return false;
                } else {
                    $this->collectionStates = $this->cache->getItem('zkstate.collectionStates')->get();
                }
                if (!$this->cache->getItem('zkstate.collectionShardLeaders')->isHit()) {
                    return false;
                } else {
                    $this->collectionShardLeaders = $this->cache->getItem('zkstate.collectionShardLeaders')->get();
                }
                if (!$this->cache->getItem('zkstate.liveNodes')->isHit()) {
                    return false;
                } else {
                    $this->liveNodes = $this->cache->getItem('zkstate.liveNodes')->get();
                }
                if (!$this->cache->getItem('zkstate.clusterProperties')->isHit()) {
                    return false;
                } else {
                    $this->clusterProperties = $this->cache->getItem('zkstate.clusterProperties')->get();
                }
                if (!$this->cache->getItem('zkstate.securityData')->isHit()) {
                    return false;
                } else {
                    $this->securityData = $this->cache->getItem('zkstate.securityData')->get();
                }

                return true;
            }
        } catch (\Psr\Cache\InvalidArgumentException $e) {
            return false;
        }

        return false;
    }

    /**
     * Updates the cache object.
     *
     * @param int|DateInterval Cache will expire after this period
     *
     * @return bool Returns whether storing data to cache was successful or not
     */
    protected function fillCacheData($cacheExpiresAfter = null): bool
    {
        if (null !== $this->cache) {
            try {
                $this->cache->save($this->cache->getItem('zkstate.aliases')
                    ->set($this->aliases)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
                $this->cache->save($this->cache->getItem('zkstate.collections')
                    ->set($this->collections)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
                $this->cache->save($this->cache->getItem('zkstate.legacyCollectionStates')
                    ->set($this->legacyCollectionStates)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
                $this->cache->save($this->cache->getItem('zkstate.collectionStates')
                    ->set($this->collectionStates)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
                $this->cache->save($this->cache->getItem('zkstate.clusterState')
                    ->set($this->clusterState)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
                $this->cache->save($this->cache->getItem('zkstate.collectionShardLeaders')
                    ->set($this->collectionShardLeaders)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
                $this->cache->save($this->cache->getItem('zkstate.liveNodes')
                    ->set($this->liveNodes)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
                $this->cache->save($this->cache->getItem('zkstate.clusterProperties')
                    ->set($this->clusterProperties)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
                $this->cache->save($this->cache->getItem('zkstate.securityData')
                    ->set($this->securityData)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
                $this->cache->save($this->cache->getItem('zkstate.securityData')
                    ->set($this->securityData)->expiresAfter($cacheExpiresAfter)->tag('zkstate'));
            } catch (\Psr\Cache\InvalidArgumentException $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Read aliases and write to class property.
     *
     * @throws \Solarium\Cloud\Exception\ZookeeperException
     */
    protected function readAliases()
    {
        $this->readData(self::ALIASES, $this->aliases, true);
    }

    /**
     *  Read collections and write to class property.
     *
     * @throws \Solarium\Cloud\Exception\ZookeeperException
     */
    protected function readCollectionList()
    {
        $this->collections = $this->getChildren(self::COLLECTIONS_ZKNODE);
    }

    /**
     * Read cluster state and write to class property.
     *
     * @throws \Solarium\Cloud\Exception\ZookeeperException
     */
    protected function readClusterState()
    {
        //Compatibility for older versions of Solr
        $this->readData(self::CLUSTER_STATE, $this->legacyCollectionStates, true);

        if (is_array($this->collections)) {
            foreach ($this->collections as $i => $collection) {
                $stateFile = self::COLLECTIONS_ZKNODE.'/'.$collection.'/'.self::COLLECTION_STATE;
                if ($this->getZkClient()->exists($stateFile)) {
                    $this->collectionStates = array_merge($this->collectionStates, json_decode($this->getZkClient()->get($stateFile), true));
                }

                foreach ($this->getChildren(self::COLLECTIONS_ZKNODE.'/'.$collection.'/'.self::SHARD_LEADERS_ZKNODE) as $shard) {
                    $leaderInfoLocation = self::COLLECTIONS_ZKNODE.'/'.$collection.'/'.self::SHARD_LEADERS_ZKNODE.'/'.$shard.'/'.self::LEADER_PROP;

                    if ($this->getZkClient()->exists($leaderInfoLocation)) {
                        $this->readData(
                            $leaderInfoLocation,
                            $this->collectionShardLeaders[$collection][$shard],
                            true
                        );
                    } else {
                        // This shard has no leader
                        $this->collectionShardLeaders[$collection][$shard] = [];
                    }
                }
            }
        }

        // @todo instead of a merge, create ClusterState array in $this->clusterState
        //$this->clusterState = new ClusterState();
        $this->clusterState = array_merge($this->collectionStates, $this->legacyCollectionStates);
    }

    /**
     * Read cluster properties and write to class property.
     *
     * @throws \Solarium\Cloud\Exception\ZookeeperException
     */
    protected function readClusterProperties()
    {
        $this->readData(self::CLUSTER_PROPS, $this->clusterProperties, true);
    }

    /**
     * Reads the security data from Zookeeper.
     *
     * @throws \Solarium\Cloud\Exception\ZookeeperException
     */
    protected function readSecurityData()
    {
        $this->readData(self::SOLR_SECURITY_CONF_PATH, $this->securityData, true);
    }

    /**
     * Reads the live node information from Zookeeper.
     *
     * @throws \Solarium\Cloud\Exception\ZookeeperException
     */
    protected function readLiveNodes()
    {
        $this->liveNodes = $this->getChildren(self::LIVE_NODES_ZKNODE);
    }

    /**
     * @param string $location
     * @param $property
     * @param bool $jsonDecode
     *
     * @throws ZookeeperException
     */
    protected function readData(string $location, &$property, bool $jsonDecode = true)
    {
        if ($this->getZkClient()->exists($location)) {
            $property = $jsonDecode ? json_decode($this->getZkClient()->get($location), true) : $this->getZkClient()->get($location);
        } else {
            throw new ZookeeperException("Cannot read data from location '$location'");
        }
    }

    /**
     * @param string $location
     *
     * @return array
     *
     * @throws ZookeeperException
     */
    protected function getChildren(string $location): array
    {
        if ($this->getZkClient()->exists($location)) {
            return $this->getZkClient()->getChildren($location);
        }

        throw new ZookeeperException("Cannot read data from location '$location'");
    }

    /**
     * Wath a given path.
     *
     * @param string   $path     the path to node
     * @param callable $callback callback function
     *
     * @return string|null
     *
     * @throws \ZookeeperException
     */
    protected function watch($path, $callback)
    {
        if (!is_callable($callback)) {
            return null;
        }

        if ($this->getZkClient()->exists($path)) {
            if (!isset($this->zkCallback[$path])) {
                $this->zkCallback[$path] = [];
            }
            if (!in_array($callback, $this->zkCallback[$path], true)) {
                $this->zkCallback[$path][] = $callback;

                return $this->getZkClient()->get($path, [$this, 'watchCallback']);
            }
        }

        return null;
    }

    /**
     * Wath event callback warper.
     *
     * @param int    $eventType
     * @param int    $stat
     * @param string $path
     *
     * @return mixed the return of the callback or null
     *
     * @throws \ZookeeperException
     */
    protected function watchCallback($eventType, $stat, $path)
    {
        // @todo, eventType and stat do nothing.
        if (!isset($this->zkCallback[$path])) {
            return null;
        }

        foreach ($this->zkCallback[$path] as $callback) {
            $this->getZkClient()->get($path, [$this, 'watchCallback']);

            return $callback();
        }

        return null;
    }

    /**
     * Delete watch callback on a node, delete all callback when $callback is null.
     *
     * @param string   $path
     * @param callable $callback
     *
     * @return bool|null
     *
     * @throws \ZookeeperException
     */
    protected function cancelWatch($path, $callback = null)
    {
        if (isset($this->zkCallback[$path])) {
            if (empty($callback)) {
                unset($this->zkCallback[$path]);
                $this->getZkClient()->get($path); //reset the callback

                return true;
            }
            $key = array_search($callback, $this->zkCallback[$path], true);

            if (false !== $key) {
                unset($this->zkCallback[$path][$key]);

                return true;
            }

            return null;
        }

        return null;
    }
}
