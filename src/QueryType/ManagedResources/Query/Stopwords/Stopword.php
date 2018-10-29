<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords;

class Stopword
{
    /**
     * @var string
     */
    protected $term;

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
}
