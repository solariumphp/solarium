<?php

namespace Solarium\QueryType\ManagedResources\Query\Synonyms\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms;

class Exists extends AbstractCommand
{
    /**
     * Term to be checked if exists.
     *
     * @var string
     */
    protected $term = '';

    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Synonyms::COMMAND_EXISTS;
    }

    /**
     * Returns request method.
     *
     * @return string
     */
    public function getRequestMethod(): string
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
     *
     * @return self
     */
    public function setTerm(string $term): self
    {
        $this->term = $term;
        return $this;
    }
}
