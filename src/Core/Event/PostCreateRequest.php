<?php

namespace Solarium\Core\Event;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\QueryInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * PostCreateRequest event, see Events for details.
 */
class PostCreateRequest extends Event
{
    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Event constructor.
     *
     * @param QueryInterface $query
     * @param Request        $request
     */
    public function __construct(QueryInterface $query, Request $request)
    {
        $this->query = $query;
        $this->request = $request;
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
     * Get the request object for this event.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
