<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\Loadbalancer\Event;

use Solarium\Core\Client\Endpoint;
use Solarium\Exception\HttpException;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * EndpointFailure event, see Events for details.
 */
class EndpointFailure extends Event
{
    /**
     * @var Endpoint
     */
    protected $endpoint;

    /**
     * @var HttpException
     */
    protected $exception;

    /**
     * Constructor.
     *
     * @param Endpoint      $endpoint
     * @param HttpException $exception
     */
    public function __construct(Endpoint $endpoint, HttpException $exception)
    {
        $this->endpoint = $endpoint;
        $this->exception = $exception;
    }

    /**
     * @return Endpoint
     */
    public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }

    /**
     * @return HttpException
     */
    public function getException(): HttpException
    {
        return $this->exception;
    }
}
