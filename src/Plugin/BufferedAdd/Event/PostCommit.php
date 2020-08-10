<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\QueryType\Update\Result;
use Symfony\Contracts\EventDispatcher\Event;

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
