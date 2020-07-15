<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\Command\AbstractAdd;
use Solarium\QueryType\ManagedResources\Query\Stopwords;

class Add extends AbstractAdd
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
     * Returns the data to be sent to Solr.
     *
     * @return string
     */
    public function getRawData(): string
    {
        return json_encode($this->stopwords);
    }
}
