<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\Loadbalancer\Event;

/**
 * Event definitions.
 *
 * @codeCoverageIgnore
 */
class Events
{
    /**
     * This event is called after and endpoint has failed.
     *
     * Gets the endpoint and the HttpException as params
     *
     * @var string
     */
    public const ENDPOINT_FAILURE = EndpointFailure::class;

    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }
}
