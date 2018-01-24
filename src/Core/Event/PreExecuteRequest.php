<?php

namespace Solarium\Core\Event;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Symfony\Component\EventDispatcher\Event;

/**
 * PreExecuteRequest event, see Events for details.
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
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Get the request object for this event.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the request object for this event.
     *
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Get the response object for this event.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set the response object for this event, overrides default execution.
     *
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
}
