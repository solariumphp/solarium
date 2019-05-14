<?php

namespace Solarium\QueryType\Select\Result;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Result\Debug\Result as DebugResult;
use Solarium\Component\Result\FacetSet as FacetSetResult;
use Solarium\Component\Result\Grouping\Result as GroupingResult;
use Solarium\Component\Result\Highlighting\Highlighting;
use Solarium\Component\Result\MoreLikeThis\MoreLikeThis;
use Solarium\Component\Result\Spellcheck\Result as SpellcheckResult;
use Solarium\Component\Result\Suggester\Result as SuggesterResult;
use Solarium\Component\Result\Stats\Stats;
use Solarium\Core\Query\DocumentInterface;
use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Select query result.
 *
 * This is the standard resulttype for a select query. Example usage:
 * <code>
 * // total solr results
 * $result->getNumFound();
 *
 * // results fetched
 * count($result);
 *
 * // get a single facet by key
 * $result->getFacet('category');
 *
 * // iterate over fetched docs
 * foreach ($result as $doc) {
 *    ....
 * }
 * </code>
 */
class Result extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * Solr numFound.
     *
     * This is NOT the number of document fetched from Solr!
     *
     * @var int
     */
    protected $numfound;

    /**
     * Solr maxscore.
     *
     * Will only be available if 'score' was one of the requested fields in your query
     *
     * @var float
     */
    protected $maxscore;

    /**
     * Solr nextcursormark.
     *
     * Will only be available if 'cursormark' was set for your query
     *
     * @var string
     */
    protected $nextcursormark;

    /**
     * Document instances array.
     *
     * @var array
     */
    protected $documents;

    /**
     * Component results.
     */
    protected $components;

    /**
     * Status code returned by Solr.
     *
     * @var int
     */
    protected $status;

    /**
     * Solr index queryTime.
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @var int
     */
    protected $queryTime;

    /**
     * Get Solr status code.
     *
     * This is not the HTTP status code! The normal value for success is 0.
     *
     * @return int
     */
    public function getStatus(): int
    {
        $this->parseResponse();

        return $this->status;
    }

    /**
     * Get Solr query time.
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @return int
     */
    public function getQueryTime(): int
    {
        $this->parseResponse();

        return $this->queryTime;
    }

    /**
     * get Solr numFound.
     *
     * Returns the total number of documents found by Solr (this is NOT the
     * number of document fetched from Solr!)
     *
     * @return int|null
     */
    public function getNumFound(): ?int
    {
        $this->parseResponse();

        return $this->numfound;
    }

    /**
     * get Solr maxscore.
     *
     * Returns the highest score of the documents in the total result for your current query (ignoring paging)
     * Will only be available if 'score' was one of the requested fields in your query
     *
     * @return float|null
     */
    public function getMaxScore(): ?float
    {
        $this->parseResponse();

        return $this->maxscore;
    }

    /**
     * get Solr nextcursormark.
     *
     * Returns the next cursor mark for deep paging
     * Will only be available if 'cursormark' was set for your query against Solr 4.7+
     *
     * @return string|null
     */
    public function getNextCursorMark(): ?string
    {
        $this->parseResponse();

        return $this->nextcursormark;
    }

    /**
     * Get all documents.
     *
     * @return DocumentInterface[]
     */
    public function getDocuments(): array
    {
        $this->parseResponse();

        return $this->documents;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        $this->parseResponse();

        return new \ArrayIterator($this->documents);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        $this->parseResponse();

        return count($this->documents);
    }

    /**
     * Get all component results.
     *
     * @return array
     */
    public function getComponents(): array
    {
        $this->parseResponse();

        return $this->components;
    }

    /**
     * Get a component result by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getComponent(string $key)
    {
        $this->parseResponse();

        return $this->components[$key] ?? null;
    }

    /**
     * Get morelikethis component result.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return MoreLikeThis|null
     */
    public function getMoreLikeThis(): ?MoreLikeThis
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS);
    }

    /**
     * Get highlighting component result.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Highlighting|null
     */
    public function getHighlighting(): ?Highlighting
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING);
    }

    /**
     * Get grouping component result.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return GroupingResult|null
     */
    public function getGrouping(): ?GroupingResult
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_GROUPING);
    }

    /**
     * Get facetset component result.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return FacetSetResult|null
     */
    public function getFacetSet(): ?FacetSetResult
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_FACETSET);
    }

    /**
     * Get spellcheck component result.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return SpellcheckResult|null
     */
    public function getSpellcheck(): ?SpellcheckResult
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_SPELLCHECK);
    }

    /**
     * Get suggester component result.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return SuggesterResult|null
     */
    public function getSuggester(): ?SuggesterResult
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_SUGGESTER);
    }

    /**
     * Get stats component result.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Stats|null
     */
    public function getStats(): ?Stats
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_STATS);
    }

    /**
     * Get debug component result.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return DebugResult|null
     */
    public function getDebug(): ?DebugResult
    {
        return $this->getComponent(ComponentAwareQueryInterface::COMPONENT_DEBUG);
    }
}
