<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Event;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * PreExecuteRequest event, see {@see Events} for details.
 */
class PreExecuteRequest extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Endpoint
     */
    protected $endpoint;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Event constructor.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     */
    public function __construct(Request $request, Endpoint $endpoint)
    {
        $this->request = $request;
        $this->endpoint = $endpoint;
    }

    /**
     * Get the endpoint object for this event.
     *
     * @return Endpoint
     */
    public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }

    /**
     * Get the request object for this event.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get the request object for this event.
     *
     * @param Request $request
     *
     * @return self Provides fluent interface
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the response object for this event.
     *
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Set the response object for this event, overrides default execution.
     *
     * @param Response $response
     *
     * @return self Provides fluent interface
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }
}
