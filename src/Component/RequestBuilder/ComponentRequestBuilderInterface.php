<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * ComponentRequestBuilderInterface.
 */
interface ComponentRequestBuilderInterface
{
    /**
     * Add request settings for the debug component.
     *
     * @param ConfigurableInterface $component
     * @param Request               $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request;
}
