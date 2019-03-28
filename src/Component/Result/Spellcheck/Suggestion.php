<?php

namespace Solarium\Component\Result\Spellcheck;

/**
 * Select component spellcheck suggestion result.
 */
class Suggestion
{
    /**
     * Constructor.
     *
     * @param int   $numFound
     * @param int   $startOffset
     * @param int   $endOffset
     * @param int   $originalFrequency
     * @param array $words
     */
    public function __construct(int $numFound, int $startOffset, int $endOffset, int $originalFrequency, array $words)
    {
        $this->numFound = $numFound;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
        $this->originalFrequency = $originalFrequency;
        $this->words = $words;
    }

    /**
     * Get numFound value.
     *
     * @return int
     */
    public function getNumFound(): int
    {
        return $this->numFound;
    }

    /**
     * Get startOffset value.
     *
     * @return int
     */
    public function getStartOffset(): int
    {
        return $this->startOffset;
    }

    /**
     * Get endOffset value.
     *
     * @return int
     */
    public function getEndOffset(): int
    {
        return $this->endOffset;
    }

    /**
     * Get originalFrequency value.
     *
     * Only available if CollateExtendedResults was enabled in your query
     *
     * @return int
     */
    public function getOriginalFrequency(): int
    {
        return $this->originalFrequency;
    }

    /**
     * Get first word.
     *
     * @return string|null
     */
    public function getWord(): ?string
    {
        $word = reset($this->words);
        if (isset($word['word'])) {
            return $word['word'];
        }

        return $word;
    }

    /**
     * Get all words (and frequencies).
     *
     * @return array
     */
    public function getWords(): array
    {
        return $this->words;
    }

    /**
     * Get frequency value.
     *
     * Only available if CollateExtendedResults was enabled in your query
     *
     * @return int
     */
    public function getFrequency(): int
    {
        $word = reset($this->words);
        if (isset($word['freq'])) {
            return $word['freq'];
        }
    }
}
