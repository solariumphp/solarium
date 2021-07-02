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
use Solarium\Component\Grouping as GroupingComponent;
use Solarium\Component\Result\Grouping\FieldGroup;
use Solarium\Component\Result\Grouping\Result;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\Query\Query;

/**
 * Parse select component Grouping result from the data.
 */
class Grouping implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param ComponentAwareQueryInterface|Query  $query
     * @param GroupingComponent|AbstractComponent $grouping
     * @param array                               $data
     *
     * @throws InvalidArgumentException
     *
     * @return Result
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $grouping, array $data): Result
    {
        if (!isset($data['grouped'])) {
            return new Result([]);
        }

        if (!$query) {
            throw new InvalidArgumentException('A valid query object needs to be provided.');
        }
        if (!$grouping) {
            throw new InvalidArgumentException('A valid grouping component needs to be provided.');
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

            $matches = $result['matches'] ?? null;
            $groupCount = $result['ngroups'] ?? null;
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
                $matches = $result['matches'] ?? null;
                $numFound = $result['doclist']['numFound'] ?? null;
                $start = $result['doclist']['start'] ?? null;
                $maxScore = $result['doclist']['maxScore'] ?? null;

                /*
                 * https://issues.apache.org/jira/browse/SOLR-13839
                 * maxScore is returned as "NaN" when group.query doesn't match any docs
                 */
                if ('NaN' === $maxScore) {
                    $maxScore = null;
                }

                // create document instances
                $documentClass = $query->getOption('documentclass');
                $documents = [];
                if (isset($result['doclist']['docs']) && \is_array($result['doclist']['docs'])) {
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
     * @param string        $valueResultClass the grouping resultvaluegroupclass option
     * @param string        $documentClass    the name of the Solr document class to use
     * @param array         $valueGroupResult the group result from the Solr response
     * @param AbstractQuery $query            the current Solr query
     *
     * @return object
     */
    private function extractValueGroup(string $valueResultClass, string $documentClass, array $valueGroupResult, AbstractQuery $query)
    {
        $value = $valueGroupResult['groupValue'] ?? null;
        $numFound = $valueGroupResult['doclist']['numFound'] ?? null;
        $start = $valueGroupResult['doclist']['start'] ?? null;
        $maxScore = $valueGroupResult['doclist']['maxScore'] ?? null;

        // create document instances
        $documents = [];
        if (isset($valueGroupResult['doclist']['docs']) &&
            \is_array($valueGroupResult['doclist']['docs'])) {
            foreach ($valueGroupResult['doclist']['docs'] as $doc) {
                $documents[] = new $documentClass($doc);
            }
        }

        return new $valueResultClass($value, $numFound, $start, $documents, $maxScore, $query);
    }
}
