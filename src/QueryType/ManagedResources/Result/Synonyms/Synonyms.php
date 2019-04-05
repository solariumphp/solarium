<?php

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
     *
     * @return self
     */
    public function setTerm(string $term): self
    {
        $this->term = $term;
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
