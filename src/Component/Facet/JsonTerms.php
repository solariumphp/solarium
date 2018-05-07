<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet query.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Field_Value_Faceting_Parameters
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
    public function getType()
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
    public function setRefine(bool $refine)
    {
        return $this->setOption('refine', $refine);
    }

    /**
     * Get the refine parameter.
     *
     * @return bool
     */
    public function getRefine()
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
    public function setOverRequest(int $overrequest)
    {
        return $this->setOption('overrequest', $overrequest);
    }

    /**
     * Get the refine parameter.
     *
     * @return int
     */
    public function getOverRequest()
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
    public function setNumBuckets(bool $numBuckets)
    {
        return $this->setOption('numBuckets', $numBuckets);
    }

    /**
     * Get the numBuckets parameter.
     *
     * @return bool
     */
    public function getNumBuckets()
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
    public function setAllBuckets(bool $allBuckets)
    {
        return $this->setOption('allBuckets', $allBuckets);
    }

    /**
     * Get the allBuckets parameter.
     *
     * @return bool
     */
    public function getAllBuckets()
    {
        return $this->getOption('allBuckets');
    }
}
