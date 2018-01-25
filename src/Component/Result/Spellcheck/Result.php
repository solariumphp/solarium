<?php

namespace Solarium\Component\Result\Spellcheck;

/**
 * Select component spellcheck result.
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * Suggestions array.
     *
     * @var array
     */
    protected $suggestions;

    /**
     * Collation object array.
     *
     * @var array
     */
    protected $collations;

    /**
     * Correctly spelled?
     *
     * @var bool
     */
    protected $correctlySpelled;

    /**
     * Constructor.
     *
     * @param Suggestion[] $suggestions
     * @param Collation[]  $collations
     * @param bool         $correctlySpelled
     */
    public function __construct(array $suggestions, array $collations, bool $correctlySpelled)
    {
        $this->suggestions = $suggestions;
        $this->collations = $collations;
        $this->correctlySpelled = $correctlySpelled;
    }

    /**
     * Get the collation result.
     *
     * @param int $key
     *
     * @return Collation
     */
    public function getCollation($key = null)
    {
        $nrOfCollations = count($this->collations);
        if (0 == $nrOfCollations) {
            return;
        }

        if (null === $key) {
            return reset($this->collations);
        }

        return $this->collations[$key];
    }

    /**
     * Get all collations.
     *
     * @return Collation[]
     */
    public function getCollations()
    {
        return $this->collations;
    }

    /**
     * Get correctly spelled status.
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return bool
     */
    public function getCorrectlySpelled()
    {
        return $this->correctlySpelled;
    }

    /**
     * Get a result by key.
     *
     * @param mixed $key
     *
     * @return Suggestion|null
     */
    public function getSuggestion($key)
    {
        if (isset($this->suggestions[$key])) {
            return $this->suggestions[$key];
        }
    }

    /**
     * Get all suggestions.
     *
     * @return Suggestion[]
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
