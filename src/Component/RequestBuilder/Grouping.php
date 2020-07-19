<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Grouping as GroupingComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Add select component Grouping to the request.
 */
class Grouping implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Grouping.
     *
     * @param GroupingComponent $component
     * @param Request           $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        // enable grouping
        $request->addParam('group', 'true');

        $request->addParam('group.field', $component->getFields());
        $request->addParam('group.query', $component->getQueries());
        $request->addParam('group.limit', $component->getLimit());
        $request->addParam('group.offset', $component->getOffset());
        $request->addParam('group.sort', $component->getSort());
        $request->addParam('group.main', $component->getMainResult());
        $request->addParam('group.ngroups', $component->getNumberOfGroups());
        $request->addParam('group.cache.percent', $component->getCachePercentage());
        $request->addParam('group.truncate', $component->getTruncate());
        $request->addParam('group.func', $component->getFunction());
        $request->addParam('group.facet', $component->getFacet());
        $request->addParam('group.format', $component->getFormat());

        return $request;
    }
}
