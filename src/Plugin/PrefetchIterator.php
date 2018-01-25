<?php

namespace Solarium\Plugin;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\DocumentInterface;
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
    protected $position;

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
    public function setPrefetch($value)
    {
        $this->resetData();

        return $this->setOption('prefetch', $value);
    }

    /**
     * Get prefetch option.
     *
     * @return int
     */
    public function getPrefetch()
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
    public function setQuery($query)
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
    public function getQuery()
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
    public function setEndpoint($endpoint)
    {
        return $this->setOption('endpoint', $endpoint);
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
    public function count()
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
    public function rewind()
    {
        $this->position = 0;

        // this condition prevent useless re-fetching of data if a count is done before the iterator is used
        if ($this->start !== $this->options['prefetch']) {
            $this->start = 0;

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
    public function current()
    {
        $adjustedIndex = $this->position % $this->options['prefetch'];

        return $this->documents[$adjustedIndex];
    }

    /**
     * Iterator implementation.
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Iterator implementation.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Iterator implementation.
     *
     * @return bool
     */
    public function valid()
    {
        $adjustedIndex = $this->position % $this->options['prefetch'];

        // this condition prevent useless re-fetching of data if a count is done before the iterator is used
        if (0 === $adjustedIndex && (0 !== $this->position || null === $this->result)) {
            $this->fetchNext();
        }

        return isset($this->documents[$adjustedIndex]);
    }

    /**
     * Fetch the next set of results.
     */
    protected function fetchNext()
    {
        if (null === $this->cursormark && null !== $this->query->getCursormark()) {
            $this->cursormark = '*';
        }

        if (null === $this->cursormark) {
            $this->query->setStart($this->start)->setRows($this->getPrefetch());
        } else {
            $this->query->setCursormark($this->cursormark)->setRows($this->getPrefetch());
        }

        $this->result = $this->client->execute($this->query, $this->getOption('endpoint'));
        $this->cursormark = $this->result->getNextCursorMark();
        $this->documents = $this->result->getDocuments();
        $this->start += $this->getPrefetch();
    }

    /**
     * Reset any cached data / position.
     */
    protected function resetData()
    {
        $this->position = null;
        $this->result = null;
        $this->documents = null;
        $this->start = 0;
        $this->cursormark = null;
    }
}
