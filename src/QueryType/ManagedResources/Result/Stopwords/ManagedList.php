<?php


namespace Solarium\QueryType\ManagedResources\Result\Stopwords;


class ManagedList
{
    /**
     * @var string
     */
    protected $stopword;

    /**
     * @return string
     */
    public function getStopword(): string
    {
        return $this->stopword;
    }

    /**
     * @param string $stopword
     */
    public function setStopword(string $stopword)
    {
        $this->stopword = $stopword;
    }
}