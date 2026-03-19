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
 * @see https://solr.apache.org/guide/json-facet-api.html#terms-facet
 */
class JsonTerms extends AbstractFacet implements JsonFacetInterface, FacetSetInterface
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
     * @deprecated Use {@link SORT_COUNT_ASC} or {@link SORT_COUNT_DESC}
     */
    const SORT_COUNT = 'count';

    /**
     * Sort buckets by 'count asc'.
     */
    const SORT_COUNT_ASC = 'count asc';

    /**
     * Sort buckets by 'count desc'.
     */
    const SORT_COUNT_DESC = 'count desc';

    /**
     * @deprecated Use {@link SORT_INDEX_ASC} or {@link SORT_INDEX_DESC}
     */
    const SORT_INDEX = 'index';

    /**
     * Sort buckets by 'index asc'.
     */
    const SORT_INDEX_ASC = 'index asc';

    /**
     * Sort buckets by 'index desc'.
     */
    const SORT_INDEX_DESC = 'index desc';

    /**
     * Default options.
     */
    protected array $options = [
        'field' => 'id',
    ];

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
     * Set the field name to facet over.
     *
     * @param string $field
     */
    public function setField(string $field): static
    {
        $this->setOption('field', $field);

        return $this;
    }

    /**
     * Get the field name to facet over.
     *
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->getOption('field');
    }

    /**
     * Set the number of buckets to skip over.
     *
     * @param int $offset
     */
    public function setOffset(int $offset): static
    {
        $this->setOption('offset', $offset);

        return $this;
    }

    /**
     * Get the number of buckets to skip over.
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->getOption('offset');
    }

    /**
     * Set the limit for number of buckets returned.
     *
     * @param int $limit
     */
    public function setLimit(int $limit): static
    {
        $this->setOption('limit', $limit);

        return $this;
    }

    /**
     * Get the limit for number of buckets returned.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->getOption('limit');
    }

    /**
     * Specify how to sort the buckets produced.
     *
     * Use one of the SORT_* constants; or any facet function / statistic that
     * occurs in the bucket followed by the order, e.g., 'avg_price desc'.
     *
     * @see https://solr.apache.org/guide/json-facet-api.html#sorting-facets-by-nested-functions
     *
     * @param self::SORT_*|string $sort
     */
    public function setSort(string $sort): static
    {
        $this->setOption('sort', $sort);

        return $this;
    }

    /**
     * Get the sort order of the buckets produced.
     *
     * @return string|null
     */
    public function getSort(): ?string
    {
        return $this->getOption('sort');
    }

    /**
     * Set the overrequest parameter.
     *
     * Number of buckets beyond the limit to request internally during distributed search. -1 means default.
     *
     * @param int $overrequest
     */
    public function setOverRequest(int $overrequest): static
    {
        $this->setOption('overrequest', $overrequest);

        return $this;
    }

    /**
     * Get the overrequest parameter.
     *
     * @return int|null
     */
    public function getOverRequest(): ?int
    {
        return $this->getOption('overrequest');
    }

    /**
     * Set the refine parameter.
     *
     * If true, turns on distributed facet refining. This uses a second phase to retrieve selected stats from shards so
     * that every shard contributes to every returned bucket in this facet and any sub-facets. This makes stats for
     * returned buckets exact.
     *
     * @param bool $refine
     */
    public function setRefine(bool $refine): static
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
     * Set the number of buckets beyond the limit to consider internally during
     * a distributed search when determining which buckets to refine.
     *
     * The default of -1 causes a heuristic to be applied based on other options specified.
     *
     * @param int $overrefine
     */
    public function setOverRefine(int $overrefine): static
    {
        $this->setOption('overrefine', $overrefine);

        return $this;
    }

    /**
     * Get the overrefine parameter.
     *
     * @return int|null
     */
    public function getOverRefine(): ?int
    {
        return $this->getOption('overrefine');
    }

    /**
     * Set the mincount for buckets to return.
     *
     * @param int $minCount
     */
    public function setMinCount(int $minCount): static
    {
        $this->setOption('mincount', $minCount);

        return $this;
    }

    /**
     * Get the mincount for buckets to return.
     *
     * @return int|null
     */
    public function getMinCount(): ?int
    {
        return $this->getOption('mincount');
    }

    /**
     * Specify if a special "missing" bucket should be returned.
     *
     * @param bool $missing
     */
    public function setMissing(bool $missing): static
    {
        $this->setOption('missing', $missing);

        return $this;
    }

    /**
     * Get if a special "missing" bucket should be returned.
     *
     * @return bool|null
     */
    public function getMissing(): ?bool
    {
        return $this->getOption('missing');
    }

    /**
     * Set the numBuckets parameter.
     *
     * A boolean. If true, adds “numBuckets” to the response, an integer representing the number of buckets for the
     * facet (as opposed to the number of buckets returned). Defaults to false.
     *
     * @param bool $numBuckets
     */
    public function setNumBuckets(bool $numBuckets): static
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
     */
    public function setAllBuckets(bool $allBuckets): static
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

    /**
     * Only produce buckets for terms starting with the specified prefix.
     *
     * @param string $prefix
     */
    public function setPrefix(string $prefix): static
    {
        $this->setOption('prefix', $prefix);

        return $this;
    }

    /**
     * Get the prefix for buckets produced.
     *
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->getOption('prefix');
    }

    /**
     * Set the facet algorithm to use.
     *
     * @param self::METHOD_* $method
     */
    public function setMethod(string $method): static
    {
        $this->setOption('method', $method);

        return $this;
    }

    /**
     * Get the facet algorithm to use.
     *
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->getOption('method');
    }

    /**
     * Specify an approximation of the final sort to use during initial collection of top buckets.
     *
     * @see setSort() For the possible sort values.
     *
     * @param string $prelimSort
     */
    public function setPrelimSort(string $prelimSort): static
    {
        $this->setOption('prelim_sort', $prelimSort);

        return $this;
    }

    /**
     * Get the preliminary sort order to use during initial collection of top buckets.
     *
     * @return string|null
     */
    public function getPrelimSort(): ?string
    {
        return $this->getOption('prelim_sort');
    }
}
