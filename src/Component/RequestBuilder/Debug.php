<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Debug as DebugComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Add select component debug to the request.
 */
class Debug implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for the debug component.
     *
     * @param DebugComponent $component
     * @param Request        $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        $request->addParam('debugQuery', 'true');
        $request->addParam('debug.explain.structured', 'true');
        $request->addParam('explainOther', $component->getExplainOther());

        return $request;
    }
}
