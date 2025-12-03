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
     */
    protected string $name = 'synonymMappings';

    /**
     * Whether or not to ignore the case.
     */
    protected ?bool $ignoreCase;

    /**
     * Format.
     */
    protected ?string $format;

    /**
     * Datetime when the resource was initialized.
     */
    protected string $initializedOn;

    /**
     * Datetime when the resource was last updated.
     */
    protected ?string $updatedSinceInit = null;

    /**
     * List items.
     *
     * @var Synonyms[]
     */
    protected array $items = [];

    protected bool $wasSuccessful = false;

    protected string $statusMessage = 'ERROR';

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
