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
    public const ENDPOINT_FAILURE = EndpointFailure::class;
}
