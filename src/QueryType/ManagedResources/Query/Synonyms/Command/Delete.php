<?php


namespace Solarium\QueryType\ManagedResources\Query\Synonyms\Command;


use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\Synonyms;

class Delete extends AbstractCommand
{
    /**
     * Term to be deleted.
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
        return Synonyms::COMMAND_DELETE;
    }

    /**
     * Returns request method.
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return Request::METHOD_DELETE;
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
     * Returns the term to be deleted.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * Set the term to be deleted.
     *
     * @param string $term
     * @return string
     */
    public function setTerm(string $term)
    {
        $this->term = $term;
    }
}