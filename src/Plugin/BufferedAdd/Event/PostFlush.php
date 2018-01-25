<?php

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\QueryType\Select\Result\DocumentInterface;
use Solarium\QueryType\Update\Result;
use Symfony\Component\EventDispatcher\Event;

/**
 * PostFlush event, see Events for details.
 */
class PostFlush extends Event
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
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Get the result for this event.
     *
     * @return DocumentInterface[]
     */
    public function getResult()
    {
        return $this->result;
    }
}
