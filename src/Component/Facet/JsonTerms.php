<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet query.
 *
 * @see https://lucene.apache.org/solr/guide/json-facet-api.html#terms-facet
 */
class JsonTerms extends AbstractField implements JsonFacetInterface, FacetSetInterface
{
    use JsonFacetTrait;

    /**
     * Facet method "dv" DocValues, collect into ordinal array.
     */
    const METHOD_DV = 'dv';

    /**
     * Facet method "uif" UnInvertedField, collect into ordinal array.
     */
    const METHOD_UIF = 'uif';

    /**
     * Facet method "dvhash" DocValues, collect into hash - improves efficiency over high cardinality fields.
     */
    const METHOD_DVHASH = 'dvhash';

    /**
     * Facet method "enum" TermsEnum then intersect DocSet (stream-able).
     */
    const METHOD_ENUM = 'enum';

    /**
     * Facet method "stream" Presently equivalent to "enum".
     */
    const METHOD_STREAM = 'stream';

    /**
     * Facet method "smart" Pick the best method for the field type (this is the default).
     */
    const METHOD_SMART = 'smart';

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::JSON_FACET_TERMS;
    }

    /**
     * Set the refine parameter.
     *
     * If true, turns on distributed facet refining. This uses a second phase to retrieve selected stats from shards so
     * that every shard contributes to every returned bucket in this facet and any sub-facets. This makes stats for
     * returned buckets exact.
     *
     * @param bool $refine
     *
     * @return self Provides fluent interface
     */
    public function setRefine(bool $refine): self
    {
        $this->setOption('refine', $refine);

        return $this;
    }

    /**
     * Get the refine parameter.
     *
     * @return bool|null
     */
    public function getRefine(): ?bool
    {
        return $this->getOption('refine');
    }

    /**
     * Set the overrequest parameter.
     *
     * Number of buckets beyond the limit to request internally during distributed search. -1 means default.
     *
     * @param int $overrequest
     *
     * @return self Provides fluent interface
     */
    public function setOverRequest(int $overrequest): self
    {
        $this->setOption('overrequest', $overrequest);

        return $this;
    }

    /**
     * Get the refine parameter.
     *
     * @return int|null
     */
    public function getOverRequest(): ?int
    {
        return $this->getOption('overrequest');
    }

    /**
     * Set the numBuckets parameter.
     *
     * A boolean. If true, adds “numBuckets” to the response, an integer representing the number of buckets for the
     * facet (as opposed to the number of buckets returned). Defaults to false.
     *
     * @param bool $numBuckets
     *
     * @return self Provides fluent interface
     */
    public function setNumBuckets(bool $numBuckets): self
    {
        $this->setOption('numBuckets', $numBuckets);

        return $this;
    }

    /**
     * Get the numBuckets parameter.
     *
     * @return bool|null
     */
    public function getNumBuckets(): ?bool
    {
        return $this->getOption('numBuckets');
    }

    /**
     * Set the allBuckets parameter.
     *
     * A boolean. If true, adds an “allBuckets” bucket to the response, representing the union of all of the buckets.
     * For multi-valued fields, this is different than a bucket for all of the documents in the domain since a single
     * document can belong to multiple buckets. Defaults to false.
     *
     * @param bool $allBuckets
     *
     * @return self Provides fluent interface
     */
    public function setAllBuckets(bool $allBuckets): self
    {
        $this->setOption('allBuckets', $allBuckets);

        return $this;
    }

    /**
     * Get the allBuckets parameter.
     *
     * @return bool|null
     */
    public function getAllBuckets(): ?bool
    {
        return $this->getOption('allBuckets');
    }
}
