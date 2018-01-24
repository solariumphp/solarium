<?php

namespace Solarium\Core\Event;

use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * PostExecute event, see Events for details.
 */
class PreExecute extends Event
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
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the result object for this event.
     *
     * @return ResultInterface
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set the result object for this event, overrides default execution.
     *
     * @param ResultInterface $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}
