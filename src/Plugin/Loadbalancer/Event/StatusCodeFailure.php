<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\Loadbalancer\Event;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * StatusCodeFailure event, see {@see Events} for details.
 */
class StatusCodeFailure extends Event
{
    /**
     * @var Endpoint
     */
    protected $endpoint;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Constructor.
     *
     * @param Endpoint $endpoint
     * @param Response $response
     */
    public function __construct(Endpoint $endpoint, Response $response)
    {
        $this->endpoint = $endpoint;
        $this->response = $response;
    }

    /**
     * @return Endpoint
     */
    public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}
