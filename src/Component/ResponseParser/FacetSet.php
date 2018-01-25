<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\Facet\Field as QueryFacetField;
use Solarium\Component\Facet\MultiQuery as QueryFacetMultiQuery;
use Solarium\Component\Facet\Pivot as QueryFacetPivot;
use Solarium\Component\Facet\Query as QueryFacetQuery;
use Solarium\Component\Facet\Range as QueryFacetRange;
use Solarium\Component\FacetSet as QueryFacetSet;
use Solarium\Component\Result\Facet\Field as ResultFacetField;
use Solarium\Component\Result\Facet\Interval as ResultFacetInterval;
use Solarium\Component\Result\Facet\MultiQuery as ResultFacetMultiQuery;
use Solarium\Component\Result\Facet\Pivot\Pivot as ResultFacetPivot;
use Solarium\Component\Result\Facet\Query as ResultFacetQuery;
use Solarium\Component\Result\Facet\Range as ResultFacetRange;
use Solarium\Component\Result\FacetSet as ResultFacetSet;
use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Query\Query;

/**
 * Parse select component FacetSet result from the data.
 */
class FacetSet extends ResponseParserAbstract implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     *
     * @param Query         $query
     * @param QueryFacetSet $facetSet
     * @param array         $data
     *
     * @throws RuntimeException
     *
     * @return ResultFacetSet
     */
    public function parse($query, $facetSet, $data)
    {
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
                        if ('facet_pivot' == $key) {
                            /* @var \Solarium\Component\Facet\Pivot $facetObject */
                            $facetObject->setFields($k);
                        }
                    }
                }
            }
        }

        $facets = [];
        foreach ($facetSet->getFacets() as $key => $facet) {
            switch ($facet->getType()) {
                case QueryFacetSet::FACET_FIELD:
                    $result = $this->facetField($query, $facet, $data);
                    break;
                case QueryFacetSet::FACET_QUERY:
                    $result = $this->facetQuery($facet, $data);
                    break;
                case QueryFacetSet::FACET_MULTIQUERY:
                    $result = $this->facetMultiQuery($facet, $data);
                    break;
                case QueryFacetSet::FACET_RANGE:
                    $result = $this->facetRange($query, $facet, $data);
                    break;
                case QueryFacetSet::FACET_PIVOT:
                    $result = $this->facetPivot($query, $facet, $data);
                    break;
                case QueryFacetSet::FACET_INTERVAL:
                    $result = $this->facetInterval($query, $facet, $data);
                    break;
                default:
                    throw new RuntimeException('Unknown facet type');
            }

            if (null !== $result) {
                $facets[$key] = $result;
            }
        }

        return $this->createFacetSet($facets);
    }

    /**
     * Create a facetset result object.
     *
     * @param array $facets
     *
     * @return ResultFacetSet
     */
    protected function createFacetSet($facets)
    {
        return new ResultFacetSet($facets);
    }

    /**
     * Add a facet result for a field facet.
     *
     * @param Query           $query
     * @param QueryFacetField $facet
     * @param array           $data
     *
     * @return ResultFacetField|null
     */
    protected function facetField($query, $facet, $data)
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_fields'][$key])) {
            return;
        }

        if ($query->getResponseWriter() == $query::WT_JSON) {
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
    protected function facetQuery($facet, $data)
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_queries'][$key])) {
            return;
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
    protected function facetMultiQuery($facet, $data)
    {
        $values = [];
        foreach ($facet->getQueries() as $query) {
            $key = $query->getKey();
            if (isset($data['facet_counts']['facet_queries'][$key])) {
                $count = $data['facet_counts']['facet_queries'][$key];
                $values[$key] = $count;
            }
        }

        if (count($values) <= 0) {
            return;
        }

        return new ResultFacetMultiQuery($values);
    }

    /**
     * Add a facet result for a range facet.
     *
     * @param Query           $query
     * @param QueryFacetRange $facet
     * @param array           $data
     *
     * @return ResultFacetRange|null
     */
    protected function facetRange($query, $facet, $data)
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_ranges'][$key])) {
            return;
        }

        $data = $data['facet_counts']['facet_ranges'][$key];
        $before = (isset($data['before'])) ? $data['before'] : null;
        $after = (isset($data['after'])) ? $data['after'] : null;
        $between = (isset($data['between'])) ? $data['between'] : null;
        $start = (isset($data['start'])) ? $data['start'] : null;
        $end = (isset($data['end'])) ? $data['end'] : null;
        $gap = (isset($data['gap'])) ? $data['gap'] : null;

        if ($query->getResponseWriter() == $query::WT_JSON) {
            $data['counts'] = $this->convertToKeyValueArray($data['counts']);
        }

        return new ResultFacetRange($data['counts'], $before, $after, $between, $start, $end, $gap);
    }

    /**
     * Add a facet result for a interval facet.
     *
     * @param Query              $query
     * @param QueryFacetInterval $facet
     * @param array              $data
     *
     * @return ResultFacetInterval|null
     */
    protected function facetInterval($query, $facet, $data)
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
     * @param Query           $query
     * @param QueryFacetPivot $facet
     * @param array           $data
     *
     * @return ResultFacetPivot|null
     */
    protected function facetPivot($query, $facet, $data)
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_pivot'][$key])) {
            return;
        }

        return new ResultFacetPivot($data['facet_counts']['facet_pivot'][$key]);
    }
}
