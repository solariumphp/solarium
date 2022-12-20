<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\DistributedSearch as RequestBuilder;

/**
 * Distributed Search (sharding) component.
 *
 * @see https://solr.apache.org/guide/distributed-search-with-index-sharding.html
 * @see https://solr.apache.org/guide/solrcloud.html
 */
class DistributedSearch extends AbstractComponent
{
    /**
     * Request to be distributed across all shards in the list.
     *
     * @var array
     */
    protected $shards = [];

    /**
     * Requests will be distributed across collections in this list.
     *
     * @var array
     */
    protected $collections = [];

    /**
     * Requests will be load balanced across replicas in this list.
     *
     * @var array
     */
    protected $replicas = [];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_DISTRIBUTEDSEARCH;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Add a shard.
     *
     * @param string $key   unique string
     * @param string $shard The syntax is host:port/base_url
     *
     * @return self Provides fluent interface
     *
     * @see https://solr.apache.org/guide/distributed-search-with-index-sharding.html
     */
    public function addShard(string $key, string $shard): self
    {
        $this->shards[$key] = $shard;

        return $this;
    }

    /**
     * Add multiple shards.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createSelect();
     * $distributedSearch = $query->getDistributedSearch();
     * $distributedSearch->addShards(array(
     *     'core0' => 'localhost:8983/solr/core0',
     *     'core1' => 'localhost:8983/solr/core1'
     * ));
     * $result = $client->select($query);
     * </code>
     *
     * @param array $shards
     *
     * @return self Provides fluent interface
     */
    public function addShards(array $shards): self
    {
        foreach ($shards as $key => $shard) {
            $this->addShard($key, $shard);
        }

        return $this;
    }

    /**
     * Remove a shard.
     *
     * @param string $key
     *
     * @return self Provides fluent interface
     */
    public function removeShard(string $key): self
    {
        if (isset($this->shards[$key])) {
            unset($this->shards[$key]);
        }

        return $this;
    }

    /**
     * Remove all shards.
     *
     * @return self Provides fluent interface
     */
    public function clearShards(): self
    {
        $this->shards = [];

        return $this;
    }

    /**
     * Set multiple shards.
     *
     * This overwrites any existing shards
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createSelect();
     * $distributedSearch = $query->getDistributedSearch();
     * $distributedSearch->setShards(array(
     *     'core0' => 'localhost:8983/solr/core0',
     *     'core1' => 'localhost:8983/solr/core1'
     * ));
     * $result = $client->select($query);
     * </code>
     *
     * @param array $shards Associative array of shards
     *
     * @return self Provides fluent interface
     */
    public function setShards(array $shards): self
    {
        $this->clearShards();
        $this->addShards($shards);

        return $this;
    }

    /**
     * Get a list of the shards.
     *
     * @return array
     */
    public function getShards(): array
    {
        return $this->shards;
    }

    /**
     *  A sharded request will go to the standard request handler
     *  (not necessarily the original); this can be overridden via shards.qt.
     *
     * @param string $handler
     *
     * @return self Provides fluent interface
     */
    public function setShardRequestHandler(string $handler): self
    {
        $this->setOption('shardhandler', $handler);

        return $this;
    }

    /**
     * Get a shard request handler (shards.qt).
     *
     * @return ?string
     */
    public function getShardRequestHandler(): ?string
    {
        return $this->getOption('shardhandler');
    }

    /**
     * Add a collection.
     *
     * @param string $key        unique string
     * @param string $collection The syntax is host:port/base_url
     *
     * @return self Provides fluent interface
     *
     * @see https://solr.apache.org/guide/solrcloud.html
     */
    public function addCollection(string $key, string $collection): self
    {
        $this->collections[$key] = $collection;

        return $this;
    }

    /**
     * Add multiple collections.
     *
     * @param array $collections
     *
     * @return self Provides fluent interface
     */
    public function addCollections(array $collections): self
    {
        foreach ($collections as $key => $collection) {
            $this->addCollection($key, $collection);
        }

        return $this;
    }

    /**
     * Remove a collection.
     *
     * @param string $key
     *
     * @return self Provides fluent interface
     */
    public function removeCollection(string $key): self
    {
        if (isset($this->collections[$key])) {
            unset($this->collections[$key]);
        }

        return $this;
    }

    /**
     * Remove all collections.
     *
     * @return self Provides fluent interface
     */
    public function clearCollections(): self
    {
        $this->collections = [];

        return $this;
    }

    /**
     * Set multiple collections.
     *
     * This overwrites any existing collections
     *
     * @param array $collections Associative array of collections
     *
     * @return self Provides fluent interface
     */
    public function setCollections(array $collections): self
    {
        $this->clearCollections();
        $this->addCollections($collections);

        return $this;
    }

    /**
     * Get a list of the collections.
     *
     * @return array
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    /**
     * Add a replica.
     *
     * @param string $key     unique string
     * @param string $replica The syntax is host:port/base_url
     *
     * @return self Provides fluent interface
     *
     * @see https://solr.apache.org/guide/distributed-requests.html
     */
    public function addReplica(string $key, string $replica): self
    {
        $this->replicas[$key] = $replica;

        return $this;
    }

    /**
     * Add multiple replicas.
     *
     * @param array $replicas
     *
     * @return self Provides fluent interface
     */
    public function addReplicas(array $replicas): self
    {
        foreach ($replicas as $key => $replica) {
            $this->addReplica($key, $replica);
        }

        return $this;
    }

    /**
     * Remove a replica.
     *
     * @param string $key
     *
     * @return self Provides fluent interface
     */
    public function removeReplica(string $key): self
    {
        if (isset($this->replicas[$key])) {
            unset($this->replicas[$key]);
        }

        return $this;
    }

    /**
     * Remove all replicas.
     *
     * @return self Provides fluent interface
     */
    public function clearReplicas(): self
    {
        $this->replicas = [];

        return $this;
    }

    /**
     * Set multiple replicas.
     *
     * This overwrites any existing replicas
     *
     * @param array $replicas Associative array of collections
     *
     * @return self Provides fluent interface
     */
    public function setReplicas(array $replicas): self
    {
        $this->clearReplicas();
        $this->addReplicas($replicas);

        return $this;
    }

    /**
     * Get a list of the replicas.
     *
     * @return array
     */
    public function getReplicas(): array
    {
        return $this->replicas;
    }

    /**
     * Initialize options.
     *
     * {@internal Several options need some extra checks or setup work,
     *            for these options the setters are called.}
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'shards':
                    $this->setShards($value);
                    break;
                case 'collections':
                    $this->setCollections($value);
                    break;
                case 'replicas':
                    $this->setReplicas($value);
                    break;
            }
        }
    }
}
