<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Spatial as SpatialComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Add select component spatial to the request.
 */
class Spatial implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Spatial.
     *
     * @param SpatialComponent $component
     * @param Request          $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        $request->addParam('sfield', $component->getField());
        $request->addParam('pt', $component->getPoint());
        $request->addParam('d', $component->getDistance());

        return $request;
    }
}
