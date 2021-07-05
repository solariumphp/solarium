<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Result\Stopwords;

use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\Core\Query\Result\Result;

/**
 * WordSet.
 */
class WordSet extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * List name.
     *
     * @var string
     */
    protected $name = 'wordSet';

    /**
     * Whether or not to ignore the case.
     *
     * @var bool|null
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
     * @var bool
     */
    protected $wasSuccessful = false;

    /**
     * @var string
     */
    protected $statusMessage = 'ERROR';

    /**
     * Constructor.
     *
     * @param \Solarium\Core\Query\AbstractQuery $query
     * @param \Solarium\Core\Client\Response     $response
     */
    public function __construct(AbstractQuery $query, Response $response)
    {
        Result::__construct($query, $response);
        $this->parseResponse();
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
     * @return bool|null
     */
    public function isIgnoreCase(): ?bool
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

    /**
     * @return string|null
     */
    public function getUpdatedSinceInit(): ?string
    {
        $this->parseResponse();

        return $this->updatedSinceInit;
    }

    /**
     * @return bool
     */
    public function getWasSuccessful(): bool
    {
        $this->parseResponse();

        return $this->wasSuccessful;
    }

    /**
     * @return string
     */
    public function getStatusMessage(): string
    {
        $this->parseResponse();

        return $this->statusMessage;
    }
}
