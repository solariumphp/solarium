<?php

namespace Solarium\Core\Event;

use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * PostExecute event, see Events for details.
 */
class PostExecute extends Event
{
    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var ResultInterface
     */
    protected $result;

    /**
     * Event constructor.
     *
     * @param QueryInterface  $query
     * @param ResultInterface $result
     */
    public function __construct(QueryInterface $query, ResultInterface $result)
    {
        $this->query = $query;
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
     * Get the result object for this event.
     *
     * @return ResultInterface
     */
    public function getResult(): ResultInterface
    {
        return $this->result;
    }
}
