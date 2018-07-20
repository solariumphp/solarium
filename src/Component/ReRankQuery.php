<?php

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ReRankQuery as RequestBuilder;

/**
 * Rerank query.
 *
 * @see https://lucene.apache.org/solr/guide/7_3/query-re-ranking.html#rerank-query-parser
 */
class ReRankQuery extends AbstractComponent implements QueryInterface
{
    use QueryTrait;

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType()
    {
        return ComponentAwareQueryInterface::COMPONENT_RERANKQUERY;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get reRankDocs value.
     *
     * @return int
     */
    public function getDocs()
    {
        return $this->getOption('docs');
    }

    /**
     * Set reRankDocs value.
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setDocs(int $value)
    {
        return $this->setOption('docs', $value);
    }

    /**
     * Get reRankWeight value.
     *
     * @return float
     */
    public function getWeight()
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
    public function setWeight(float $value)
    {
        return $this->setOption('weight', $value);
    }
}
