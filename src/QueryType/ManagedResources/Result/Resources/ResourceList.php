<?php

namespace Solarium\QueryType\ManagedResources\Result\Resources;

use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\Core\Query\Result\Result;

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
     * @var \Solarium\QueryType\ManagedResources\Result\Resources\Resource[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param AbstractQuery $query
     * @param Response      $response
     */
    public function __construct($query, $response)
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
     * @return \Solarium\QueryType\ManagedResources\Result\Resources\Resource[]
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
