<?php

namespace Solarium\QueryType\ManagedResources\Query\Synonyms\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms as SynonymsData;

class Add extends AbstractCommand
{
    /**
     * Synonyms to add.
     *
     * @var SynonymsData
     */
    protected $synonyms;

    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Synonyms::COMMAND_ADD;
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
     * @return SynonymsData
     */
    public function getSynonyms(): SynonymsData
    {
        return $this->synonyms;
    }

    /**
     * Set synonyms.
     *
     * @param SynonymsData $synonyms
     *
     * @return self
     */
    public function setSynonyms(SynonymsData $synonyms): self
    {
        $this->synonyms = $synonyms;
        return $this;
    }

    /**
     * Returns the raw data to be sent to Solr.
     */
    public function getRawData(): string
    {
        if (null !== $this->getSynonyms() && !empty($this->getSynonyms()->getSynonyms())) {
            if ('' !== trim($this->getSynonyms()->getTerm())) {
                return json_encode([$this->getSynonyms()->getTerm() => $this->getSynonyms()->getSynonyms()]);
            }

            return json_encode($this->getSynonyms()->getSynonyms());
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
