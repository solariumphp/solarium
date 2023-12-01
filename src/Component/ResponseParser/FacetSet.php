<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\ResponseParser;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Facet\FacetInterface;
use Solarium\Component\Facet\Field as QueryFacetField;
use Solarium\Component\Facet\Interval as QueryFacetInterval;
use Solarium\Component\Facet\JsonAggregation;
use Solarium\Component\Facet\JsonFacetInterface;
use Solarium\Component\Facet\JsonRange as QueryFacetJsonRange;
use Solarium\Component\Facet\MultiQuery as QueryFacetMultiQuery;
use Solarium\Component\Facet\Query as QueryFacetQuery;
use Solarium\Component\Facet\Range as QueryFacetRange;
use Solarium\Component\FacetSet as QueryFacetSet;
use Solarium\Component\FacetSetInterface;
use Solarium\Component\Result\Facet\Aggregation;
use Solarium\Component\Result\Facet\Bucket;
use Solarium\Component\Result\Facet\Buckets;
use Solarium\Component\Result\Facet\Field as ResultFacetField;
use Solarium\Component\Result\Facet\Interval as ResultFacetInterval;
use Solarium\Component\Result\Facet\JsonRange as ResultFacetJsonRange;
use Solarium\Component\Result\Facet\MultiQuery as ResultFacetMultiQuery;
use Solarium\Component\Result\Facet\Pivot\Pivot as ResultFacetPivot;
use Solarium\Component\Result\Facet\Pivot\PivotItem;
use Solarium\Component\Result\Facet\Query as ResultFacetQuery;
use Solarium\Component\Result\Facet\Range as ResultFacetRange;
use Solarium\Component\Result\FacetSet as ResultFacetSet;
use Solarium\Component\Result\Stats\Result;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;

/**
 * Parse select component FacetSet result from the data.
 */
class FacetSet extends ResponseParserAbstract implements ComponentParserInterface
{
    use NormalizeParsedJsonStatsTrait;

    /**
     * Parse result data into result objects.
     *
     * @param ComponentAwareQueryInterface|AbstractQuery $query
     * @param AbstractComponent|QueryFacetSet            $facetSet
     * @param array                                      $data
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     *
     * @return ResultFacetSet
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $facetSet, array $data): ?ResultFacetSet
    {
        if (!$query) {
            throw new InvalidArgumentException('A valid query object needs to be provided.');
        }
        if (!$facetSet) {
            throw new InvalidArgumentException('A valid facet set component needs to be provided.');
        }

        if (true === $facetSet->getExtractFromResponse()) {
            if (false === empty($data['facet_counts'])) {
                foreach ($data['facet_counts'] as $key => $facets) {
                    switch ($key) {
                        case 'facet_fields':
                            $method = 'createFacetField';
                            break;
                        case 'facet_queries':
                            $method = 'createFacetQuery';
                            break;
                        case 'facet_ranges':
                            $method = 'createFacetRange';
                            break;
                        case 'facet_pivot':
                            $method = 'createFacetPivot';
                            break;
                        case 'facet_interval':
                            $method = 'createFacetInterval';
                            break;
                        default:
                            throw new RuntimeException('Unknown facet class identifier');
                    }
                    foreach ($facets as $k => $facet) {
                        $facetObject = $facetSet->$method($k);
                        if ('facet_pivot' === $key) {
                            /* @var \Solarium\Component\Facet\Pivot $facetObject */
                            $facetObject->setFields($k);
                        }
                    }
                }
            }
        }

        $facets = [];
        foreach ($facetSet->getFacets() as $key => $facet) {
            $result = null;
            switch ($facet->getType()) {
                case FacetSetInterface::FACET_FIELD:
                    $result = $this->facetField($query, $facet, $data);
                    break;
                case FacetSetInterface::FACET_QUERY:
                    $result = $this->facetQuery($facet, $data);
                    break;
                case FacetSetInterface::FACET_MULTIQUERY:
                    $result = $this->facetMultiQuery($facet, $data);
                    break;
                case FacetSetInterface::FACET_RANGE:
                    $result = $this->facetRange($query, $facet, $data);
                    break;
                case FacetSetInterface::FACET_PIVOT:
                    $result = $this->facetPivot($query, $facet, $data);
                    break;
                case FacetSetInterface::FACET_INTERVAL:
                    $result = $this->facetInterval($facet, $data);
                    break;
                case FacetSetInterface::JSON_FACET_AGGREGATION:
                case FacetSetInterface::JSON_FACET_QUERY:
                case FacetSetInterface::JSON_FACET_RANGE:
                case FacetSetInterface::JSON_FACET_TERMS:
                    break;
                default:
                    throw new RuntimeException(sprintf('Unknown facet type %s', $facet->getType()));
            }

            if (null !== $result) {
                $facets[$key] = $result;
            }
        }

        if (!empty($data['facets'])) {
            /* @noinspection AdditionOperationOnArraysInspection */
            $facets += $this->parseJsonFacetSet($data['facets'], $facetSet->getFacets());
        }

        return $this->createFacetSet($facets);
    }

    /**
     * Parse JSON facets.
     *
     * @param array            $facetData
     * @param FacetInterface[] $facets
     *
     * @return array
     */
    protected function parseJsonFacetSet(array $facetData, array $facets): array
    {
        $bucketsAndAggregations = [];

        foreach ($facetData as $key => $values) {
            if (\is_array($values)) {
                if (isset($values['buckets'])) {
                    $buckets = [];
                    // Parse buckets.
                    foreach ($values['buckets'] as $bucket) {
                        $val = $bucket['val'];
                        $count = $bucket['count'];
                        unset($bucket['val'], $bucket['count']);

                        $buckets[] = new Bucket($val, $count, new ResultFacetSet($this->parseJsonFacetSet(
                            $bucket,
                            (isset($facets[$key]) && $facets[$key] instanceof JsonFacetInterface) ? $facets[$key]->getFacets() : []
                        )));
                    }
                    if (isset($values['numBuckets'])) {
                        $numBuckets = $values['numBuckets'];
                    } else {
                        $numBuckets = null;
                    }
                    if (isset($facets[$key]) && $facets[$key] instanceof QueryFacetJsonRange) {
                        if (isset($values['before']['count'])) {
                            $before = $values['before']['count'];
                        } else {
                            $before = null;
                        }
                        if (isset($values['after']['count'])) {
                            $after = $values['after']['count'];
                        } else {
                            $after = null;
                        }
                        if (isset($values['between']['count'])) {
                            $between = $values['between']['count'];
                        } else {
                            $between = null;
                        }
                        $bucketsAndAggregations[$key] = new ResultFacetJsonRange($buckets, $before, $after, $between);
                    } elseif ($buckets || $numBuckets) {
                        $bucketsAndAggregations[$key] = new Buckets($buckets, $numBuckets);
                    }
                } else {
                    $bucketsAndAggregations[$key] = new ResultFacetSet($this->parseJsonFacetSet(
                        $values,
                        (isset($facets[$key]) && $facets[$key] instanceof JsonFacetInterface) ? $facets[$key]->getFacets() : []
                    ));
                }
            } else {
                if (isset($facets[$key]) && $facets[$key] instanceof JsonAggregation) {
                    $min = $facets[$key]->getMin();
                    if (null !== $min && $values < $min) {
                        continue;
                    }
                }
                $bucketsAndAggregations[$key] = new Aggregation($values);
            }
        }

        return $bucketsAndAggregations;
    }

    /**
     * Create a facetset result object.
     *
     * @param array $facets
     *
     * @return ResultFacetSet
     */
    protected function createFacetSet(array $facets): ResultFacetSet
    {
        return new ResultFacetSet($facets);
    }

    /**
     * Add a facet result for a field facet.
     *
     * @param AbstractQuery   $query
     * @param QueryFacetField $facet
     * @param array           $data
     *
     * @return ResultFacetField|null
     */
    protected function facetField(AbstractQuery $query, FacetInterface $facet, array $data): ?ResultFacetField
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_fields'][$key])) {
            return null;
        }

        if ($query::WT_JSON === $query->getResponseWriter()) {
            $data['facet_counts']['facet_fields'][$key] = $this->convertToKeyValueArray(
                $data['facet_counts']['facet_fields'][$key]
            );
        }

        return new ResultFacetField($data['facet_counts']['facet_fields'][$key]);
    }

    /**
     * Add a facet result for a facet query.
     *
     * @param QueryFacetQuery $facet
     * @param array           $data
     *
     * @return ResultFacetQuery|null
     */
    protected function facetQuery(FacetInterface $facet, array $data): ?ResultFacetQuery
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_queries'][$key])) {
            return null;
        }

        return new ResultFacetQuery($data['facet_counts']['facet_queries'][$key]);
    }

    /**
     * Add a facet result for a multiquery facet.
     *
     * @param QueryFacetMultiQuery $facet
     * @param array                $data
     *
     * @return ResultFacetMultiQuery|null
     */
    protected function facetMultiQuery(FacetInterface $facet, array $data): ?ResultFacetMultiQuery
    {
        $values = [];
        foreach ($facet->getQueries() as $query) {
            $key = $query->getKey();
            if (isset($data['facet_counts']['facet_queries'][$key])) {
                $count = $data['facet_counts']['facet_queries'][$key];
                $values[$key] = $count;
            }
        }

        if (\count($values) <= 0) {
            return null;
        }

        return new ResultFacetMultiQuery($values);
    }

    /**
     * Add a facet result for a range facet.
     *
     * @param AbstractQuery   $query
     * @param QueryFacetRange $facet
     * @param array           $data
     *
     * @return ResultFacetRange|null
     */
    protected function facetRange(AbstractQuery $query, FacetInterface $facet, array $data): ?ResultFacetRange
    {
        if (null !== $pivot = $facet->getPivot()) {
            foreach ($pivot->getLocalParameters()->getKeys() as $key) {
                if (isset($data['facet_counts']['facet_pivot'][$key])) {
                    $pivot = $data['facet_counts']['facet_pivot'][$key];

                    foreach ($pivot as $pivotKey => $piv) {
                        if (isset($piv['ranges'])) {
                            foreach ($piv['ranges'] as $rangeKey => $range) {
                                if (isset($range['counts'])) {
                                    $pivot[$pivotKey]['ranges'][$rangeKey]['counts'] = $this->convertToKeyValueArray($range['counts']);
                                }
                            }
                        }
                    }

                    $pivot = new ResultFacetPivot($pivot);
                } else {
                    $pivot = null;
                }
            }
        }

        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_ranges'][$key])) {
            return null;
        }

        $data = $data['facet_counts']['facet_ranges'][$key];
        $before = $data['before'] ?? null;
        $after = $data['after'] ?? null;
        $between = $data['between'] ?? null;
        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;
        $gap = $data['gap'] ?? null;

        if ($query::WT_JSON === $query->getResponseWriter()) {
            $data['counts'] = $this->convertToKeyValueArray($data['counts']);
        }

        return new ResultFacetRange($data['counts'], $before, $after, $between, $start, $end, $gap, $pivot);
    }

    /**
     * Add a facet result for a interval facet.
     *
     * @param QueryFacetInterval $facet
     * @param array              $data
     *
     * @return ResultFacetInterval|null
     */
    protected function facetInterval(FacetInterface $facet, array $data): ?ResultFacetInterval
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_intervals'][$key])) {
            return null;
        }

        return new ResultFacetInterval($data['facet_counts']['facet_intervals'][$key]);
    }

    /**
     * Add a facet result for a range facet.
     *
     * @param AbstractQuery  $query
     * @param FacetInterface $facet
     * @param array          $data
     *
     * @return ResultFacetPivot|null
     */
    protected function facetPivot(AbstractQuery $query, FacetInterface $facet, array $data): ?ResultFacetPivot
    {
        $key = $facet->getKey();

        if (!isset($data['facet_counts']['facet_pivot'][$key])) {
            return null;
        }

        $facetPivot = new ResultFacetPivot($data['facet_counts']['facet_pivot'][$key]);

        foreach ($facetPivot->getPivot() as $pivot) {
            $this->pivotStats($query, $pivot);
        }

        return $facetPivot;
    }

    /**
     * Add stats to a pivot.
     *
     * @param PivotItem $pivotItem
     */
    protected function pivotStats(AbstractQuery $query, PivotItem $pivotItem): void
    {
        foreach ($pivotItem->getPivot() as $pivot) {
            $this->pivotStats($query, $pivot);
        }

        if (null !== $stats = $pivotItem->getStats()) {
            foreach ($stats->getResults() as $key => $result) {
                if ('stats_fields' !== $key) {
                    continue;
                }

                // remove unparsed results
                $stats->removeResult($key);

                foreach ($result as $field => $values) {
                    if ($query::WT_JSON === $query->getResponseWriter()) {
                        $values = $this->normalizeParsedJsonStats($values);
                    }

                    $stats->setResult($field, new Result($field, $values));
                }
            }
        }
    }
}
