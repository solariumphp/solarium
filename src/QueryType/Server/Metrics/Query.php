<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Metrics;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Support\Utility;

/**
 * Metrics query.
 */
class Query extends AbstractQuery
{
    public const REPLICA_TYPE_NRT = 'NRT';

    public const REPLICA_TYPE_PULL = 'PULL';

    public const REPLICA_TYPE_TLOG = 'TLOG';

    public const WT_OPENMETRICS = 'openmetrics';

    public const WT_PROMETHEUS = 'prometheus';

    /**
     * Default options.
     */
    protected array $options = [
        'handler' => 'admin/metrics',
        'responsewriter' => self::WT_PROMETHEUS,
        'resultclass' => Result::class,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_METRICS;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Set the metric name to filter on.
     *
     * If you want to use multiple values supply an array or comma separated string.
     *
     * @param string|string[] $name
     *
     * @return self Provides fluent interface
     */
    public function setName(string|array $name): self
    {
        $this->setOption('name', Utility::stringOrArrayToArray($name));

        return $this;
    }

    /**
     * Get the metric name to filter on.
     *
     * @return string[]|null
     */
    public function getName(): ?array
    {
        return $this->getOption('name');
    }

    /**
     * Set the category label to filter on.
     *
     * If you want to use multiple values supply an array or comma separated string.
     *
     * @param string|string[] $category
     *
     * @return self Provides fluent interface
     */
    public function setCategory(string|array $category): self
    {
        $this->setOption('category', Utility::stringOrArrayToArray($category));

        return $this;
    }

    /**
     * Get the category label to filter on.
     *
     * @return string[]|null
     */
    public function getCategory(): ?array
    {
        return $this->getOption('category');
    }

    /**
     * Set the core name to filter on.
     *
     * If you want to use multiple values supply an array or comma separated string.
     *
     * @param string|string[] $core
     *
     * @return self Provides fluent interface
     */
    public function setCore(string|array $core): self
    {
        $this->setOption('core', Utility::stringOrArrayToArray($core));

        return $this;
    }

    /**
     * Get the core name to filter on.
     *
     * @return string[]|null
     */
    public function getCore(): ?array
    {
        return $this->getOption('core');
    }

    /**
     * Set the collection name to filter on.
     *
     * If you want to use multiple values supply an array or comma separated string.
     *
     * @param string|string[] $collection
     *
     * @return self Provides fluent interface
     */
    public function setCollection(string|array $collection): self
    {
        $this->setOption('collection', Utility::stringOrArrayToArray($collection));

        return $this;
    }

    /**
     * Get the collection name to filter on.
     *
     * @return string[]|null
     */
    public function getCollection(): ?array
    {
        return $this->getOption('collection');
    }

    /**
     * Set the shard name to filter on.
     *
     * If you want to use multiple values supply an array or comma separated string.
     *
     * @param string|string[] $shard
     *
     * @return self Provides fluent interface
     */
    public function setShard(string|array $shard): self
    {
        $this->setOption('shard', Utility::stringOrArrayToArray($shard));

        return $this;
    }

    /**
     * Get the shard name to filter on.
     *
     * @return string[]|null
     */
    public function getShard(): ?array
    {
        return $this->getOption('shard');
    }

    /**
     * Set the replica type to filter on.
     *
     * If you want to use multiple values supply an array or comma separated string.
     *
     * @param string|string[] $replicaType
     *
     * @return self Provides fluent interface
     */
    public function setReplicaType(string|array $replicaType): self
    {
        $this->setOption('replica_type', Utility::stringOrArrayToArray($replicaType));

        return $this;
    }

    /**
     * Get the shard name to filter on.
     *
     * @return string[]|null
     */
    public function getReplicaType(): ?array
    {
        return $this->getOption('replica_type');
    }

    /**
     * Initialize options.
     *
     * {@internal Options for this query type need additional setup work
     *            because they can be an array or a comma separated string.}
     */
    protected function init(): void
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'name':
                    $this->setName($value);
                    break;
                case 'category':
                    $this->setCategory($value);
                    break;
                case 'core':
                    $this->setCore($value);
                    break;
                case 'collection':
                    $this->setCollection($value);
                    break;
                case 'shard':
                    $this->setShard($value);
                    break;
                case 'replica_type':
                    $this->setReplicaType($value);
                    break;
            }
        }
    }
}
