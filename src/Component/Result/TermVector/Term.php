<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\TermVector;

/**
 * Select component term vector document field term result.
 */
class Term implements \ArrayAccess
{
    /**
     * The term.
     *
     * @var string
     */
    protected $term;

    /**
     * Term frequency.
     *
     * @var int|null
     */
    protected $tf;

    /**
     * Positions.
     *
     * @var int[]|null
     */
    protected $positions;

    /**
     * Offsets.
     *
     * @var array|null
     */
    protected $offsets;

    /**
     * Payloads.
     *
     * @var string[]|null
     */
    protected $payloads;

    /**
     * Document frequency.
     *
     * @var int|null
     */
    protected $df;

    /**
     * TF / DF (i.e., TF * IDF).
     *
     * @var float|null
     */
    protected $tfIdf;

    /**
     * Constructor.
     *
     * @param string        $term
     * @param int|null      $tf
     * @param int[]|null    $positions
     * @param array[]|null  $offsets
     * @param string[]|null $payloads
     * @param int|null      $df
     * @param float|null    $tfIdf
     */
    public function __construct(string $term, ?int $tf, ?array $positions, ?array $offsets, ?array $payloads, ?int $df, ?float $tfIdf)
    {
        $this->term = $term;
        $this->tf = $tf;
        $this->positions = $positions;
        $this->offsets = $offsets;
        $this->payloads = $payloads;
        $this->df = $df;
        $this->tfIdf = $tfIdf;
    }

    /**
     * Returns the term.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * Returns the term frequency.
     *
     * @return int|null
     */
    public function getTermFrequency(): ?int
    {
        return $this->tf;
    }

    /**
     * Returns position information.
     *
     * @return int[]|null
     */
    public function getPositions(): ?array
    {
        return $this->positions;
    }

    /**
     * Returns offset information.
     *
     * @return array[]|null
     */
    public function getOffsets(): ?array
    {
        return $this->offsets;
    }

    /**
     * Returns payload information.
     *
     * @return string[]|null
     */
    public function getPayloads(): ?array
    {
        return $this->payloads;
    }

    /**
     * Returns the document frequency.
     *
     * @return int|null
     */
    public function getDocumentFrequency(): ?int
    {
        return $this->df;
    }

    /**
     * Returns the TF / DF (i.e., TF * IDF).
     *
     * @return float|null
     */
    public function getTermFreqInverseDocFreq(): ?float
    {
        return $this->tfIdf;
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return \in_array($offset, ['tf', 'positions', 'offsets', 'payloads', 'df', 'tf-idf']);
    }

    #[\ReturnTypeWillChange]
    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if ('tf-idf' === $offset) {
            $offset = 'tfIdf';
        }

        return $this->{$offset};
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        // Details are immutable.
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        // Details are immutable.
    }
}
