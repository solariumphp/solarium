<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Stats\Stats as StatsComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;

/**
 * Add select component stats to the request.
 */
class Stats extends BaseRequestBuilder implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for the stats component.
     *
     * @param StatsComponent $component
     * @param Request        $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        // enable stats
        $request->addParam('stats', 'true');

        // add fields
        foreach ($component->getFields() as $field) {
            $statsField = $field->getKey();
            $pivots = $field->getPivots();

            if (0 !== \count($pivots)) {
                $statsField = $this->renderLocalParams($statsField, ['tag' => $pivots]);
            }

            $request->addParam('stats.field', $statsField);

            // add field specific facet stats
            foreach ($field->getFacets() as $facet) {
                $request->addParam('f.'.$field->getKey().'.stats.facet', $facet);
            }
        }

        // add facet stats for all fields
        foreach ($component->getFacets() as $facet) {
            $request->addParam('stats.facet', $facet);
        }

        return $request;
    }
}
