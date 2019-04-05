<?php

namespace Solarium\Plugin\Loadbalancer\Event;

/**
 * Event definitions.
 */
interface Events
{
    /**
     * This event is called after and endpoint has failed.
     *
     * Gets the endpoint and the HttpException as params
     *
     * @var string
     */
    const ENDPOINT_FAILURE = 'solarium.loadbalancer.endpointFailure';
}
