<?php


namespace Solarium\QueryType\ManagedResources\Result\Synonyms;


class ManagedMap
{
    /**
     * @var string
     */
    protected $term;

    /**
     * @var array
     */
    protected $synonyms;

    /**
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
     * @param array $synonyms
     */
    public function setSynonyms(array $synonyms)
    {
        $this->synonyms = $synonyms;
    }
}