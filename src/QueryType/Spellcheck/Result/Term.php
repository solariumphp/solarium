<?php

namespace Solarium\QueryType\Spellcheck\Result;

/**
 * Spellcheck query term result.
 */
class Term implements \IteratorAggregate, \Countable
{
    /**
     * NumFound.
     *
     * @var int
     */
    protected $numFound;

    /**
     * StartOffset.
     *
     * @var int
     */
    protected $startOffset;

    /**
     * EndOffset.
     *
     * @var int
     */
    protected $endOffset;

    /**
     * Suggestions.
     *
     * @var array
     */
    protected $suggestions;

    /**
     * Constructor.
     *
     * @param int   $numFound
     * @param int   $startOffset
     * @param int   $endOffset
     * @param array $suggestions
     */
    public function __construct($numFound, $startOffset, $endOffset, $suggestions)
    {
        $this->numFound = $numFound;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
        $this->suggestions = $suggestions;
    }

    /**
     * Get NumFound.
     *
     * @return int
     */
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * Get StartOffset.
     *
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * Get EndOffset.
     *
     * @return int
     */
    public function getEndOffset()
    {
        return $this->endOffset;
    }

    /**
     * Get suggestions.
     *
     * @return array
     */
    public function getSuggestions()
    {
        return $this->suggestions;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->suggestions);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->suggestions);
    }
}
