<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query\Synonyms;

/**
 * To set a list of symmetric synonyms leave the term empty.
 */
class Synonyms
{
    /**
     * @var string|null
     */
    protected $term = null;

    /**
     * @var array
     */
    protected $synonyms = [];

    /**
     * Get the term.
     *
     * @return string|null
     */
    public function getTerm(): ?string
    {
        return $this->term;
    }

    /**
     * Set the term for a single mapping.
     *
     * @param string $term
     *
     * @return self
     */
    public function setTerm(string $term): self
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Remove the term. This reverts to symmetrical synonyms.
     *
     * @return self
     */
    public function removeTerm(): self
    {
        $this->term = null;

        return $this;
    }

    /**
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
     * @return self
     */
    public function setSynonyms(array $synonyms): self
    {
        $this->synonyms = $synonyms;

        return $this;
    }
}
