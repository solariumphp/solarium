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
 * PostExecuteRequest event, see {@see Events} for details.
 */
class PostExecuteRequest extends Event
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
     * @param Response $response
     */
    public function __construct(Request $request, Endpoint $endpoint, Response $response)
    {
        $this->request = $request;
        $this->endpoint = $endpoint;
        $this->response = $response;
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
     * Get the response object for this event.
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
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
}
