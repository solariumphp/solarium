<?php

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\QueryType\Update\Result;
use Symfony\Component\EventDispatcher\Event;

/**
 * PostCommit event, see Events for details.
 */
class PostCommit extends Event
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * Event constructor.
     *
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Get the result for this event.
     *
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }
}
