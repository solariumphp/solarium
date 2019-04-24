<?php

namespace Solarium\Core\Event;

use Solarium\Core\Query\QueryInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * PostCreateQuery event, see Events for details.
 */
class PostCreateQuery extends Event
{
    /**
     * @var QueryInterface
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
     * @param string         $type
     * @param array|null     $options
     * @param QueryInterface $query
     */
    public function __construct(string $type, ?array $options, QueryInterface $query)
    {
        $this->type = $type;
        $this->options = $options;
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
