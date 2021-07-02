<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Spellcheck;

/**
 * Select component spellcheck suggestion result.
 */
class Suggestion
{
    /**
     * @var int
     */
    private $numFound;

    /**
     * @var int
     */
    private $startOffset;

    /**
     * @var int
     */
    private $endOffset;

    /**
     * @var int
     */
    private $originalFrequency;

    /**
     * @var array
     */
    private $words;

    /**
     * @var string
     */
    private $originalTerm;

    /**
     * Constructor.
     *
     * @param int         $numFound
     * @param int         $startOffset
     * @param int         $endOffset
     * @param int|null    $originalFrequency
     * @param array       $words
     * @param string|null $originalTerm
     */
    public function __construct(int $numFound, int $startOffset, int $endOffset, ?int $originalFrequency, array $words, ?string $originalTerm = null)
    {
        $this->numFound = $numFound;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
        $this->originalFrequency = $originalFrequency;
        $this->words = $words;
        $this->originalTerm = $originalTerm;
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
     * @return int|null
     */
    public function getOriginalFrequency(): ?int
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
     * @return int|null
     */
    public function getFrequency(): int
    {
        $word = reset($this->words);

        if (false === isset($word['freq'])) {
            return null;
        }

        return $word['freq'];
    }

    /**
     * Get original term.
     *
     * @return string|null
     */
    public function getOriginalTerm(): ?string
    {
        return $this->originalTerm;
    }
}
