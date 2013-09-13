<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\RequestBuilder\Component\DistributedSearch as RequestBuilder;

/**
 * Distributed Search (sharding) component
 *
 * @link http://wiki.apache.org/solr/DistributedSearch
 * @link http://wiki.apache.org/solr/SolrCloud/
 */
class DistributedSearch extends Component
{
    /**
     * Request to be distributed across all shards in the list
     *
     * @var array
     */
    protected $shards = array();

    /**
     * Requests will be distributed across collections in this list
     *
     * @var array
     */
    protected $collections = array();

    /**
     * Get component type
     *
     * @return string
     */
    public function getType()
    {
        return SelectQuery::COMPONENT_DISTRIBUTEDSEARCH;
    }

    /**
     * Get a requestbuilder for this query
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder;
    }

    /**
     * This component has no response parser...
     *
     * @return null
     */
    public function getResponseParser()
    {
        return null;
    }

    /**
     * Initialize options
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     *
     * @return void
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
            }
        }
    }

    /**
     * Add a shard
     *
     * @param  string $key   unique string
     * @param  string $shard The syntax is host:port/base_url
     * @return self   Provides fluent interface
     * @link http://wiki.apache.org/solr/DistributedSearch
     */
    public function addShard($key, $shard)
    {
        $this->shards[$key] = $shard;

        return $this;
    }

    /**
     * Add multiple shards
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
     * @param  array $shards
     * @return self  Provides fluent interface
     */
    public function addShards(array $shards)
    {
        foreach ($shards as $key => $shard) {
            $this->addShard($key, $shard);
        }

        return $this;
    }

    /**
     * Remove a shard
     *
     * @param  string $key
     * @return self   Provides fluent interface
     */
    public function removeShard($key)
    {
        if (isset($this->shards[$key])) {
            unset($this->shards[$key]);
        }

        return $this;
    }

    /**
     * Remove all shards
     *
     * @return self Provides fluent interface
     */
    public function clearShards()
    {
        $this->shards = array();

        return $this;
    }

    /**
     * Set multiple shards
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
     * @param  array $shards Associative array of shards
     * @return self  Provides fluent interface
     */
    public function setShards(array $shards)
    {
        $this->clearShards();
        $this->addShards($shards);

        return $this;
    }

    /**
     * Get a list of the shards
     *
     * @return array
     */
    public function getShards()
    {
        return $this->shards;
    }

    /**
     *  A sharded request will go to the standard request handler
     *  (not necessarily the original); this can be overridden via shards.qt
     *
     * @param string
     * @return self Provides fluent interface
     */
    public function setShardRequestHandler($handler)
    {
        $this->setOption('shardhandler', $handler);

        return $this;
    }

    /**
     * Get a shard request handler (shards.qt)
     *
     * @param string
     * @return self Provides fluent interface
     */
    public function getShardRequestHandler()
    {
        return $this->getOption('shardhandler');
    }

    /**
     * Add a collection
     *
     * @param  string $key   unique string
     * @param  string $collection The syntax is host:port/base_url
     * @return self   Provides fluent interface
     * @link http://wiki.apache.org/solr/SolrCloud/
     */
    public function addCollection($key, $collection)
    {
        $this->collections[$key] = $collection;

        return $this;
    }

    /**
     * Add multiple collections
     *
     * @param  array $collections
     * @return self  Provides fluent interface
     */
    public function addCollections(array $collections)
    {
        foreach ($collections as $key => $collection) {
            $this->addCollection($key, $collection);
        }

        return $this;
    }

    /**
     * Remove a collection
     *
     * @param  string $key
     * @return self   Provides fluent interface
     */
    public function removeCollection($key)
    {
        if (isset($this->collections[$key])) {
            unset($this->collections[$key]);
        }

        return $this;
    }

    /**
     * Remove all collections
     *
     * @return self Provides fluent interface
     */
    public function clearCollections()
    {
        $this->collections = array();

        return $this;
    }

    /**
     * Set multiple collections
     *
     * This overwrites any existing collections
     *
     * @param  array $collections Associative array of collections
     * @return self  Provides fluent interface
     */
    public function setCollections(array $collections)
    {
        $this->clearCollections();
        $this->addCollections($collections);

        return $this;
    }

    /**
     * Get a list of the collections
     *
     * @return array
     */
    public function getCollections()
    {
        return $this->collections;
    }
}
