<?php

namespace Solarium\QueryType\ManagedResources\Query\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;

abstract class AbstractDelete extends AbstractCommand
{
    /**
     * Term to be deleted.
     *
     * @var string
     */
    protected $term = '';

    /**
     * Returns request method.
     *
     * @return string
     */
    public function getRequestMethod(): string
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
     *
     * @return self
     */
    public function setTerm(string $term): self
    {
        $this->term = $term;
        return $this;
    }
}
