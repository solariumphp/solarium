<?php

namespace Solarium\QueryType\ManagedResources\Query\Synonyms;

/**
 * To set a list of symmetric synonyms leave the term empty.
 */
class Synonyms
{
    /**
     * @var string
     */
    protected $term = '';

    /**
     * @var array
     */
    protected $synonyms = [];

    /**
     * Set the term.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @param string $term
     */
    public function setTerm(string $term)
    {
        $this->term = $term;
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
     */
    public function setSynonyms(array $synonyms)
    {
        $this->synonyms = $synonyms;
    }
}
