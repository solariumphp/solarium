<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\InitArgsInterface;
use Solarium\QueryType\ManagedResources\Query\Stopwords;

class Config extends AbstractCommand
{
    /**
     * Configuration parameters to set.
     *
     * @var InitArgsInterface
     */
    protected $initArgs;

    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Stopwords::COMMAND_CONFIG;
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
     * @return InitArgsInterface
     */
    public function getInitArgs(): InitArgsInterface
    {
        return $this->initArgs;
    }

    /**
     * Set configuration parameters.
     *
     * @param InitArgsInterface $initArgs
     *
     * @return self
     */
    public function setInitArgs(InitArgsInterface $initArgs): self
    {
        $this->initArgs = $initArgs;
        return $this;
    }

    /**
     * Returns the raw data to be sent to Solr.
     *
     * @return string
     */
    public function getRawData(): string
    {
        if (null !== $this->getInitArgs() && !empty($this->getInitArgs()->getInitArgs())) {
            return json_encode(['initArgs' => $this->getInitArgs()->getInitArgs()]);
        }

        return '';
    }

    /**
     * Empty.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return '';
    }
}
