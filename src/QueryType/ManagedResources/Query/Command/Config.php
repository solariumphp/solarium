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
use Solarium\QueryType\ManagedResources\Query\InitArgsInterface;

/**
 * Config.
 */
class Config extends AbstractCommand
{
    /**
     * Configuration parameters to set.
     *
     * @var \Solarium\QueryType\ManagedResources\Query\InitArgsInterface
     */
    protected $initArgs;

    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Query::COMMAND_CONFIG;
    }

    /**
     * Returns request method.
     *
     * @return string
     */
    public function getRequestMethod(): string
    {
        return Request::METHOD_PUT;
    }

    /**
     * Returns configuration parameters.
     *
     * @return \Solarium\QueryType\ManagedResources\Query\InitArgsInterface|null
     */
    public function getInitArgs(): ?InitArgsInterface
    {
        return $this->initArgs;
    }

    /**
     * Set configuration parameters.
     *
     * @param \Solarium\QueryType\ManagedResources\Query\InitArgsInterface $initArgs
     *
     * @return self Provides fluent interface
     */
    public function setInitArgs(InitArgsInterface $initArgs): self
    {
        $this->initArgs = $initArgs;

        return $this;
    }

    /**
     * Returns the raw data to be sent to Solr.
     *
     * @return string|null
     */
    public function getRawData(): ?string
    {
        if (null !== $this->getInitArgs() && !empty($this->getInitArgs()->getInitArgs())) {
            return json_encode(['initArgs' => $this->getInitArgs()->getInitArgs()]);
        }

        return null;
    }
}
