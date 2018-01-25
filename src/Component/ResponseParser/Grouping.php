<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\Grouping as GroupingComponent;
use Solarium\Component\Result\Grouping\FieldGroup;
use Solarium\Component\Result\Grouping\Result;
use Solarium\QueryType\Select\Query\Query;

/**
 * Parse select component Grouping result from the data.
 */
class Grouping implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param Query             $query
     * @param GroupingComponent $grouping
     * @param array             $data
     *
     * @return Result
     */
    public function parse($query, $grouping, $data)
    {
        if (!isset($data['grouped'])) {
            return new Result([]);
        }

        $groups = [];

        // parse field groups
        $valueResultClass = $grouping->getOption('resultvaluegroupclass');
        $documentClass = $query->getOption('documentclass');

        // check grouping fields as well as the grouping function (either can be used in the query)
        foreach (array_merge($grouping->getFields(), [$grouping->getFunction()]) as $field) {
            if (!isset($data['grouped'][$field])) {
                continue;
            }

            $result = $data['grouped'][$field];

            $matches = (isset($result['matches'])) ? $result['matches'] : null;
            $groupCount = (isset($result['ngroups'])) ? $result['ngroups'] : null;
            if (GroupingComponent::FORMAT_SIMPLE === $grouping->getFormat()) {
                $valueGroups = [$this->extractValueGroup($valueResultClass, $documentClass, $result, $query)];
                $groups[$field] = new FieldGroup($matches, $groupCount, $valueGroups);
                continue;
            }

            $valueGroups = [];
            foreach ($result['groups'] as $valueGroupResult) {
                $valueGroups[] = $this->extractValueGroup($valueResultClass, $documentClass, $valueGroupResult, $query);
            }

            $groups[$field] = new FieldGroup($matches, $groupCount, $valueGroups);
        }

        // parse query groups
        $groupResultClass = $grouping->getOption('resultquerygroupclass');
        foreach ($grouping->getQueries() as $groupQuery) {
            if (isset($data['grouped'][$groupQuery])) {
                $result = $data['grouped'][$groupQuery];

                // get statistics
                $matches = (isset($result['matches'])) ? $result['matches'] : null;
                $numFound = (isset($result['doclist']['numFound'])) ? $result['doclist']['numFound'] : null;
                $start = (isset($result['doclist']['start'])) ? $result['doclist']['start'] : null;
                $maxScore = (isset($result['doclist']['maxScore'])) ? $result['doclist']['maxScore'] : null;

                // create document instances
                $documentClass = $query->getOption('documentclass');
                $documents = [];
                if (isset($result['doclist']['docs']) && is_array($result['doclist']['docs'])) {
                    foreach ($result['doclist']['docs'] as $doc) {
                        $documents[] = new $documentClass($doc);
                    }
                }

                // create a group result object
                $group = new $groupResultClass($matches, $numFound, $start, $maxScore, $documents, $query);
                $groups[$groupQuery] = $group;
            }
        }

        return new Result($groups);
    }

    /**
     * Helper method to extract a ValueGroup object from the given value group result array.
     *
     * @param string $valueResultClass the grouping resultvaluegroupclass option
     * @param string $documentClass    the name of the solr document class to use
     * @param array  $valueGroupResult the group result from the solr response
     * @param Query  $query            the current solr query
     *
     * @return object
     */
    private function extractValueGroup($valueResultClass, $documentClass, $valueGroupResult, $query)
    {
        $value = (isset($valueGroupResult['groupValue'])) ?
                $valueGroupResult['groupValue'] : null;

        $numFound = (isset($valueGroupResult['doclist']['numFound'])) ?
                $valueGroupResult['doclist']['numFound'] : null;

        $start = (isset($valueGroupResult['doclist']['start'])) ?
                $valueGroupResult['doclist']['start'] : null;

        $maxScore = (isset($valueGroupResult['doclist']['maxScore'])) ?
                $valueGroupResult['doclist']['maxScore'] : null;

        // create document instances
        $documents = [];
        if (isset($valueGroupResult['doclist']['docs']) &&
            is_array($valueGroupResult['doclist']['docs'])) {
            foreach ($valueGroupResult['doclist']['docs'] as $doc) {
                $documents[] = new $documentClass($doc);
            }
        }

        return new $valueResultClass($value, $numFound, $start, $documents, $maxScore, $query);
    }
}
