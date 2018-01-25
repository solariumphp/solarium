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
    public function __construct($numFound, $startOffset, $endOffset, $originalFrequency, $words)
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
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * Get startOffset value.
     *
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * Get endOffset value.
     *
     * @return int
     */
    public function getEndOffset()
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
    public function getOriginalFrequency()
    {
        return $this->originalFrequency;
    }

    /**
     * Get first word.
     *
     * @return string|null
     */
    public function getWord()
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
    public function getWords()
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
    public function getFrequency()
    {
        $word = reset($this->words);
        if (isset($word['freq'])) {
            return $word['freq'];
        }
    }
}
