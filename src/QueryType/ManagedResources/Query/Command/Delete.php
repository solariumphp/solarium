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
 * Delete.
 */
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
    public function getType(): string
    {
        return Query::COMMAND_DELETE;
    }

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
     * Returns the term to be deleted.
     *
     * @return string|null
     */
    public function getTerm(): ?string
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
