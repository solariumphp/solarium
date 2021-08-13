<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\ReRankQuery as RequestBuilder;

/**
 * Rerank query.
 *
 * @see https://solr.apache.org/guide/query-re-ranking.html#rerank-query-parser
 */
class ReRankQuery extends AbstractComponent implements QueryInterface
{
    use QueryTrait;

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_RERANKQUERY;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get reRankDocs value.
     *
     * @return int|null
     */
    public function getDocs(): ?int
    {
        return $this->getOption('docs');
    }

    /**
     * Set reRankDocs value.
     *
     * @param int $value
     *
     * @return self
     */
    public function setDocs(int $value): self
    {
        $this->setOption('docs', $value);

        return $this;
    }

    /**
     * Get reRankWeight value.
     *
     * @return float|null
     */
    public function getWeight(): ?float
    {
        return $this->getOption('weight');
    }

    /**
     * Set reRankWeight value.
     *
     * @param float $value
     *
     * @return self Provides fluent interface
     */
    public function setWeight(float $value): self
    {
        $this->setOption('weight', $value);

        return $this;
    }
}
