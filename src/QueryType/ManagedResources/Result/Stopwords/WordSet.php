<?php

namespace Solarium\QueryType\ManagedResources\Result\Stopwords;

class WordSet implements \IteratorAggregate, \Countable
{
    /**
     * List name.
     *
     * @var string
     */
    protected $name = 'wordSet';

    /**
     * Whether or not to ignore the case.
     * @var boolean
     */
    protected $ignoreCase;

    /**
     * Datetime when the resource was initialized
     * @var string
     */
    protected $initializedOn;

    /**
     * List items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param string $name
     * @param array $result
     */
    public function __construct($result)
    {
        $this->items = $result['managedList'];
        $this->initializedOn = $result['initializedOn'];
        $this->ignoreCase = (bool)$result['initArgs']['ignoreCase'];
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
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    /**
     * @return bool
     */
    public function isIgnoreCase(): bool
    {
        return $this->ignoreCase;
    }

    /**
     * @return string
     */
    public function getInitializedOn(): string
    {
        return $this->initializedOn;
    }


}
