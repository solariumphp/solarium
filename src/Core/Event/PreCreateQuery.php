<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Event;

use Solarium\Core\Query\QueryInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * PreCreateQuery event, see {@see Events} for details.
 */
class PreCreateQuery extends Event
{
    /**
     * @var QueryInterface|null
     */
    protected $query;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * Event constructor.
     *
     * @param string     $type
     * @param array|null $options
     */
    public function __construct(string $type, ?array $options = null)
    {
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * Get the query object for this event.
     *
     * @return QueryInterface|null
     */
    public function getQuery(): ?QueryInterface
    {
        return $this->query;
    }

    /**
     * Set the query object for this event, this overrides default execution.
     *
     * @param QueryInterface $query
     *
     * @return self Provides fluent interface
     */
    public function setQuery(QueryInterface $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get the querytype for this event.
     *
     * @return string
     */
    public function getQueryType(): string
    {
        return $this->type;
    }

    /**
     * Get the options for this event.
     *
     * @return array|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }
}
