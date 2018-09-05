<?php


namespace Solarium\QueryType\ManagedResources\Query\Synonyms\Command;


use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\Synonyms;

class Exists extends AbstractCommand
{
    /**
     * Term to be checked if exists.
     *
     * @var string
     */
    protected $term;

    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType()
    {
        return Synonyms::COMMAND_EXISTS;
    }

    /**
     * Returns request method.
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return Request::METHOD_GET;
    }

    /**
     * Empty.
     *
     * @return string
     */
    public function getRawData(): string
    {
        return '';
    }

    /**
     * Returns the term to be checked if exists.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * Set the term to be checked if exists.
     *
     * @param string $term
     * @return string
     */
    public function setTerm(string $term)
    {
        $this->term = $term;
    }
}