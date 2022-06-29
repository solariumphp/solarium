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
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'useHeadRequest' => false,
    ];

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
        if ($this->options['useHeadRequest']) {
            return Request::METHOD_HEAD;
        }

        // use GET by default to avoid SOLR-15116 and SOLR-16274
        return Request::METHOD_GET;
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

    /**
     * Use a HEAD request to check if a resource or child resource exists?
     *
     * @return bool
     */
    public function getUseHeadRequest(): bool
    {
        return $this->getOption('useHeadRequest');
    }

    /**
     * Use a HEAD request to check if a resource or child resource exists?
     *
     * Solarium defaults to GET requests because multiple Solr versions have bugs in the
     * handling of HEAD requests. Only set this to true if you know that your Solr version
     * isn't affected by {@link https://issues.apache.org/jira/browse/SOLR-15116 SOLR-15116}
     * or {@link https://issues.apache.org/jira/browse/SOLR-16274 SOLR-16274}.
     *
     * @param bool $useHeadRequest
     *
     * @return self
     */
    public function setUseHeadRequest(bool $useHeadRequest): self
    {
        $this->setOption('useHeadRequest', $useHeadRequest);

        return $this;
    }
}
