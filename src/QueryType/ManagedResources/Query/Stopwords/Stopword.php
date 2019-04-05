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
     *
     * @return self
     */
    public function setTerm(string $term): self
    {
        $this->term = $term;
        return $this;
    }
}
