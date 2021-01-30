<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;

/**
 * Exists.
 */
class Exists extends AbstractCommand
{
    /**
     * Name of the child resource to be checked if exists.
     *
     * @var string|null
     */
    protected $term = null;

    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Query::COMMAND_EXISTS;
    }

    /**
     * Returns request method.
     *
     * @return string
     */
    public function getRequestMethod(): string
    {
        $method = Request::METHOD_HEAD;

        // there's a bug since Solr 8.7 with HEAD requests if a term is set (SOLR-15116)
        if (null !== $this->getTerm()) {
            $method = Request::METHOD_GET;
        }

        return $method;
    }

    /**
     * Returns the name of the child resource to be checked if exists.
     *
     * @return string|null
     */
    public function getTerm(): ?string
    {
        return $this->term;
    }

    /**
     * Set the name of the child resource to be checked if exists.
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

    /**
     * Remove the name of the child resource. This reverts to checking if the managed resource exists.
     *
     * @return self
     */
    public function removeTerm(): self
    {
        $this->term = null;

        return $this;
    }
}
