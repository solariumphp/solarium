<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Event;

use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * PreExecute event, see {@see Events} for details.
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
    public function getQuery(): QueryInterface
    {
        return $this->query;
    }

    /**
     * Get the result object for this event.
     *
     * @return ResultInterface|null
     */
    public function getResult(): ?ResultInterface
    {
        return $this->result;
    }

    /**
     * Set the result object for this event, overrides default execution.
     *
     * @param ResultInterface $result
     *
     * @return self Provides fluent interface
     */
    public function setResult(ResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}
