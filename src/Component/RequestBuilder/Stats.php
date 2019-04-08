<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Stats\Stats as StatsComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Add select component stats to the request.
 */
class Stats implements ComponentRequestBuilderInterface
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
            $pivots = $field->getPivots();

            $prefix = (count($pivots) > 0) ? '{!tag='.implode(',', $pivots).'}' : '';
            $request->addParam('stats.field', $prefix.$field->getKey());

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
