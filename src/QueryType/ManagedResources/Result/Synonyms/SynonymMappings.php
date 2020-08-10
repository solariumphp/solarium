<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Result\Synonyms;

use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\Core\Query\Result\Result;

/**
 * SynonymMappings.
 */
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
     * @var bool|null
     */
    protected $ignoreCase;

    /**
     * Format.
     *
     * @var string|null
     */
    protected $format;

    /**
     * Datetime when the resource was initialized.
     *
     * @var string
     */
    protected $initializedOn;

    /**
     * Datetime when the resource was last updated.
     *
     * @var string|null
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
     * @param \Solarium\Core\Query\AbstractQuery $query
     * @param \Solarium\Core\Client\Response     $response
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
     * @return \Solarium\QueryType\ManagedResources\Result\Synonyms\Synonyms[]
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
     * @return bool|null
     */
    public function isIgnoreCase(): ?bool
    {
        $this->parseResponse();

        return $this->ignoreCase;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        $this->parseResponse();

        return $this->format;
    }

    /**
     * @return string
     */
    public function getInitializedOn(): string
    {
        $this->parseResponse();

        return $this->initializedOn;
    }

    /**
     * @return string|null
     */
    public function getUpdatedSinceInit(): ?string
    {
        $this->parseResponse();

        return $this->updatedSinceInit;
    }
}
