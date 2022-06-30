<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Core\Query\DocumentInterface;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Result as SelectResult;

/**
 * Prefetch plugin.
 *
 * This plugin can be used to create an 'endless' iterator over a complete resultset. The iterator will take care of
 * fetching the data in sets (sequential prefetching).
 */
class PrefetchIterator extends AbstractPlugin implements \Iterator, \Countable
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'prefetch' => 100,
    ];

    /**
     * Query instance to execute.
     *
     * @var SelectQuery
     */
    protected $query;

    /**
     * Start position (offset).
     *
     * @var int
     */
    protected $start = 0;

    /**
     * Last resultset from the query instance.
     *
     * @var SelectResult
     */
    protected $result;

    /**
     * Iterator position.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Cursor mark.
     *
     * @var string
     */
    protected $cursormark;

    /**
     * Documents from the last resultset.
     *
     * @var DocumentInterface[]
     */
    protected $documents;

    /**
     * Set prefetch option.
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setPrefetch(int $value): self
    {
        $this->resetData();

        $this->setOption('prefetch', $value);

        return $this;
    }

    /**
     * Get prefetch option.
     *
     * @return int|null
     */
    public function getPrefetch(): ?int
    {
        return $this->getOption('prefetch');
    }

    /**
     * Set query to use for prefetching.
     *
     * @param SelectQuery $query
     *
     * @return self Provides fluent interface
     */
    public function setQuery(SelectQuery $query): self
    {
        $this->query = $query;
        $this->resetData();

        return $this;
    }

    /**
     * Get the query object used.
     *
     * @return SelectQuery
     */
    public function getQuery(): SelectQuery
    {
        return $this->query;
    }

    /**
     * Set endpoint to use.
     *
     * This overwrites any existing endpoint
     *
     * @param string|Endpoint $endpoint
     *
     * @return self Provides fluent interface
     */
    public function setEndpoint($endpoint): self
    {
        $this->setOption('endpoint', $endpoint);

        return $this;
    }

    /**
     * Get endpoint setting.
     *
     * @return string|Endpoint|null
     */
    public function getEndpoint()
    {
        return $this->getOption('endpoint');
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        // if no results are available yet, get them now
        if (null === $this->result) {
            $this->fetchNext();
        }

        return $this->result->getNumFound();
    }

    /**
     * Iterator implementation.
     */
    public function rewind(): void
    {
        $this->position = 0;

        // this condition prevents needlessly re-fetching if the iterator hasn't moved past its first set of results yet
        // (this includes when a count is done before the iterator is used)
        if ($this->start > $this->options['prefetch']) {
            $this->start = 0;
            $this->result = null;
            $this->documents = null;

            if (null !== $this->cursormark) {
                $this->cursormark = '*';
            }
        }
    }

    /**
     * Iterator implementation.
     *
     * @return DocumentInterface
     */
    public function current(): DocumentInterface
    {
        $adjustedIndex = $this->position % $this->options['prefetch'];

        return $this->documents[$adjustedIndex];
    }

    /**
     * Iterator implementation.
     *
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Iterator implementation.
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Iterator implementation.
     *
     * @return bool
     */
    public function valid(): bool
    {
        $adjustedIndex = $this->position % $this->options['prefetch'];

        // this condition prevents erroneously fetching the next set of results if a count is done before the iterator is used
        if (0 === $adjustedIndex && (0 !== $this->position || null === $this->result)) {
            $this->fetchNext();
        }

        return isset($this->documents[$adjustedIndex]);
    }

    /**
     * Fetch the next set of results.
     *
     * @return self Provides fluent interface
     */
    protected function fetchNext(): self
    {
        if (null === $this->cursormark && null !== $this->query->getCursorMark()) {
            $this->cursormark = '*';
        }

        if (null === $this->cursormark) {
            $this->query->setStart($this->start)->setRows($this->getPrefetch());
        } else {
            $this->query->setCursorMark($this->cursormark)->setRows($this->getPrefetch());
        }

        $this->result = $this->client->execute($this->query, $this->getOption('endpoint'));
        $this->cursormark = $this->result->getNextCursorMark();
        $this->documents = $this->result->getDocuments();
        $this->start += $this->getPrefetch();

        return $this;
    }

    /**
     * Reset any cached data / position.
     *
     * @return self Provides fluent interface
     */
    protected function resetData(): self
    {
        $this->position = 0;
        $this->result = null;
        $this->documents = null;
        $this->start = 0;
        $this->cursormark = null;

        return $this;
    }
}
