<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords;

class Add extends AbstractCommand
{
    /**
     * Stopwords to add.
     *
     * @var array
     */
    protected $stopwords;

    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Stopwords::COMMAND_ADD;
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
     * Get stopwords.
     *
     * @return array
     */
    public function getStopwords(): array
    {
        return $this->stopwords;
    }

    /**
     * Set stopwords.
     *
     * @param array $stopwords
     *
     * @return self
     */
    public function setStopwords(array $stopwords): self
    {
        $this->stopwords = $stopwords;
        return $this;
    }

    /**
     * Returns the data to be send to Solr.
     *
     * @return string
     */
    public function getRawData(): string
    {
        return json_encode($this->stopwords);
    }

    /**
     * Returns the term to be send to Solr.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return '';
    }
}
