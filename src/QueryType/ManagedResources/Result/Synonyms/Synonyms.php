<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Result\Synonyms;

/**
 * Synonym result.
 */
class Synonyms
{
    /**
     * @var string
     */
    protected $term;

    /**
     * @var array
     */
    protected $synonyms = [];

    /**
     * Synonyms constructor.
     *
     * @param string $term
     * @param array  $synonyms
     */
    public function __construct(string $term, array $synonyms)
    {
        $this->term = $term;
        $this->synonyms = $synonyms;
    }

    /**
     * Get the term.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * Set the term.
     *
     * @param string $term
     *
     * @return self Provides fluent interface
     */
    public function setTerm(string $term): self
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get the synonyms.
     *
     * @return array
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }

    /**
     * Sets the synonyms. To set a list of symmetric synonyms leave the term empty.
     *
     * @param array $synonyms
     *
     * @return self Provides fluent interface
     */
    public function setSynonyms(array $synonyms): self
    {
        $this->synonyms = $synonyms;

        return $this;
    }
}
