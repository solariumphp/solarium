<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Event;

use Solarium\Core\Client\Response;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * PostCreateResult event, see Events for details.
 */
class PostCreateResult extends Event
{
    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var ResultInterface
     */
    protected $result;

    /**
     * Event constructor.
     *
     * @param QueryInterface  $query
     * @param Response        $response
     * @param ResultInterface $result
     */
    public function __construct(QueryInterface $query, Response $response, ResultInterface $result)
    {
        $this->query = $query;
        $this->response = $response;
        $this->result = $result;
    }

    /**
     * Get the query object for this event.
     *
     * @return QueryInterface
     */
    public function getQuery(): QueryInterface
    {
        return $this->query;
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
     * Get the result object for this event.
     *
     * @return ResultInterface
     */
    public function getResult(): ResultInterface
    {
        return $this->result;
    }
}
