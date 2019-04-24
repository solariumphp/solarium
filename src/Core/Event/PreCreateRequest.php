<?php

namespace Solarium\Core\Event;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\QueryInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * PreCreateRequest event, see Events for details.
 */
class PreCreateRequest extends Event
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
     */
    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
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
     * Set request.
     *
     * If you set this request value the default execution is skipped and this request is directly returned
     *
     * @param Request $request
     *
     * @return self
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get the result.
     *
     * @return null|Request
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }
}
