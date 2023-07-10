<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query\Command\Synonyms;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractAdd;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms as SynonymsData;

/**
 * Add.
 */
class Add extends AbstractAdd
{
    /**
     * Synonyms to add.
     *
     * @var \Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms
     */
    protected $synonyms;

    /**
     * Get synonyms.
     *
     * @return \Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms|null
     */
    public function getSynonyms(): ?SynonymsData
    {
        return $this->synonyms;
    }

    /**
     * Set synonyms.
     *
     * @param \Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms $synonyms
     *
     * @return self Provides fluent interface
     */
    public function setSynonyms(SynonymsData $synonyms): self
    {
        $this->synonyms = $synonyms;

        return $this;
    }

    /**
     * Returns the raw data to be sent to Solr.
     *
     * @return string|null
     */
    public function getRawData(): ?string
    {
        if (null !== $this->getSynonyms() && !empty($this->getSynonyms()->getSynonyms())) {
            if (null !== $this->getSynonyms()->getTerm() && '' !== trim($this->getSynonyms()->getTerm())) {
                return json_encode([$this->getSynonyms()->getTerm() => $this->getSynonyms()->getSynonyms()]);
            }

            return json_encode($this->getSynonyms()->getSynonyms());
        }

        return null;
    }
}
