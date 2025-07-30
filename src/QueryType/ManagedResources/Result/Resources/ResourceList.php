<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Result\Resources;

use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\ManagedResources\Result\Resources\Resource as ManagedResource;

/**
 * ResourceList.
 */
class ResourceList extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * List name.
     *
     * @var string
     */
    protected $name = 'managedResources';

    /**
     * List items.
     *
     * @var ManagedResource[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param AbstractQuery $query
     * @param Response      $response
     */
    public function __construct(AbstractQuery $query, Response $response)
    {
        Result::__construct($query, $response);
    }

    /**
     * Get type value.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get all items.
     *
     * @return ManagedResource[]
     */
    public function getItems(): array
    {
        $this->parseResponse();

        return $this->items;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        $this->parseResponse();

        return new \ArrayIterator($this->items);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        $this->parseResponse();

        return \count($this->items);
    }
}
