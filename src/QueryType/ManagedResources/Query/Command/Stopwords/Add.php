<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query\Command\Stopwords;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractAdd;

/**
 * Add.
 */
class Add extends AbstractAdd
{
    /**
     * Stopwords to add.
     *
     * @var array
     */
    protected $stopwords;

    /**
     * Get stopwords.
     *
     * @return array|null
     */
    public function getStopwords(): ?array
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
     * Returns the raw data to be sent to Solr.
     *
     * @return string|null
     */
    public function getRawData(): ?string
    {
        if (!empty($this->stopwords)) {
            return json_encode($this->stopwords);
        }

        return null;
    }
}
