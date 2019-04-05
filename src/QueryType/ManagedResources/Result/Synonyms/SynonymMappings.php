<?php

namespace Solarium\QueryType\ManagedResources\Result\Synonyms;

use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\Core\Query\Result\Result;

class SynonymMappings extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * List name.
     *
     * @var string
     */
    protected $name = 'synonymMappings';

    /**
     * ResourceId looked up using the managed resources component.
     *
     * @var string
     */
    protected $resourceId;

    /**
     * Whether or not to ignore the case.
     *
     * @var bool
     */
    protected $ignoreCase;

    /**
     * Datetime when the resource was initialized.
     *
     * @var string
     */
    protected $initializedOn;

    /**
     * Datetime when the resource was last updated.
     *
     * @var string
     */
    protected $updatedSinceInit;

    /**
     * List items.
     *
     * @var array
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
     * @return Synonyms[]
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

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        $this->parseResponse();
        return $this->resourceId;
    }

    /**
     * @return bool
     */
    public function isIgnoreCase(): bool
    {
        $this->parseResponse();
        return $this->ignoreCase;
    }

    /**
     * @return string
     */
    public function getInitializedOn(): string
    {
        $this->parseResponse();
        return $this->initializedOn;
    }
}
