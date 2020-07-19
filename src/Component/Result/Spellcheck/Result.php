<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
     * @param int|null $key
     *
     * @return Collation|null
     */
    public function getCollation(?int $key = null): ?Collation
    {
        $nrOfCollations = \count($this->collations);
        if (0 === $nrOfCollations) {
            return null;
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
    public function getCollations(): array
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
    public function getCorrectlySpelled(): bool
    {
        return $this->correctlySpelled;
    }

    /**
     * Get a suggestion by key.
     *
     * @param mixed $key
     *
     * @return Suggestion|null
     */
    public function getSuggestion($key): ?Suggestion
    {
        return $this->suggestions[$key] ?? null;
    }

    /**
     * Get all suggestions.
     *
     * @return Suggestion[]
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->suggestions);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->suggestions);
    }
}
