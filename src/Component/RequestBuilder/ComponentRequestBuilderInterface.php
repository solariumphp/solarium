<?php

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
