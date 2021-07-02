<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\ResponseParser;

use Solarium\Component\AbstractComponent;
use Solarium\Component\Analytics\Grouping as ComponentGrouping;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Result\Analytics\Expression;
use Solarium\Component\Result\Analytics\Facet;
use Solarium\Component\Result\Analytics\Grouping;
use Solarium\Component\Result\Analytics\Result;

/**
 * Analytics.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Analytics implements ComponentParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $component, array $data): ?Result
    {
        if (false === isset($data['analytics_response']) || null === $component) {
            return null;
        }

        $response = $data['analytics_response'];
        $result = new Result();
        $results = [];

        /** @var \Solarium\Component\Analytics\Analytics $component */
        foreach ($component->getExpressions() as $name => $expression) {
            if (false === isset($response['results'][$name])) {
                continue;
            }

            $results[] = new Expression($name, $expression, $response['results'][$name]);
        }

        $result->setResults($results);

        $groupings = [];

        foreach ($component->getGroupings() as $groupingName => $componentGrouping) {
            if (false === isset($response['groupings'][$groupingName])) {
                continue;
            }

            $grouping = new Grouping($groupingName);

            foreach ($componentGrouping->getFacets() as $facetName => $componentFacet) {
                if (false === isset($response['groupings'][$groupingName][$facetName])) {
                    continue;
                }

                $facets = [];

                foreach ($response['groupings'][$groupingName][$facetName] as $facet) {
                    $facets[] = $this->facet($componentGrouping, $facet);
                }

                $grouping->addFacets($facetName, $facets);
            }

            $groupings[] = $grouping;
        }

        $result->setGroupings($groupings);

        return $result;
    }

    /**
     * @param \Solarium\Component\Analytics\Grouping $grouping
     * @param array                                  $result
     *
     * @return \Solarium\Component\Result\Analytics\Facet
     */
    private function facet(ComponentGrouping $grouping, array $result): Facet
    {
        $facet = new Facet($result['value'], $result['pivot'] ?? null);

        foreach ($grouping->getExpressions() as $expressionName => $expression) {
            if (false === isset($result['results'][$expressionName])) {
                continue;
            }

            $facet->addResult(new Expression($expressionName, $expression, $result['results'][$expressionName]));
        }

        if (false === isset($result['children'])) {
            return $facet;
        }

        foreach ($result['children'] as $child) {
            $facet->addChild($this->facet($grouping, $child));
        }

        return $facet;
    }
}
