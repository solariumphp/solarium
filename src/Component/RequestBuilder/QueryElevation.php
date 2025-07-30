<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\QueryElevation as QueryelevationComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Add select component queryelevation to the request.
 */
class QueryElevation implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for QueryelevationComponent.
     *
     * @param QueryelevationComponent $component
     * @param Request                 $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        // add document transformers to request field list
        if (null !== ($transformers = $component->getTransformers())) {
            $fl = $request->getParam('fl');
            $fields = implode(',', null === $fl ? $transformers : array_merge([$fl], $transformers));
            $request->addParam('fl', $fields, true);
        }

        // add basic params to request
        $request->addParam('enableElevation', $component->getEnableElevation());
        $request->addParam('forceElevation', $component->getForceElevation());
        $request->addParam('exclusive', $component->getExclusive());
        $request->addParam('useConfiguredElevatedOrder', $component->getUseConfiguredElevatedOrder());
        $request->addParam('markExcludes', $component->getMarkExcludes());

        // add overrides for pre-configured elevations
        $request->addParam('elevateIds', null === ($ids = $component->getElevateIds()) ? null : implode(',', $ids));
        $request->addParam('excludeIds', null === ($ids = $component->getExcludeIds()) ? null : implode(',', $ids));

        // add tags of filter queries to exclude for elevated documents
        $request->addParam('elevate.excludeTags', null === ($tags = $component->getExcludeTags()) ? null : implode(',', $tags));

        return $request;
    }
}
