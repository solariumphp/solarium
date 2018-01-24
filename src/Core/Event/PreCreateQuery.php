<?php

namespace Solarium\Core\Event;

use Solarium\Core\Query\QueryInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * PreCreateQuery event, see Events for details.
 */
class PreCreateQuery extends Event
{
    /**
     * @var null|QueryInterface
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
    public function __construct($type, $options)
    {
        $this->type = $type;
        $this->options = $options;
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
     * Set the query object for this event, this overrides default execution.
     *
     * @param QueryInterface $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Get the querytype for this event.
     *
     * @return string
     */
    public function getQueryType()
    {
        return $this->type;
    }

    /**
     * Get the options for this event.
     *
     * @return array|null
     */
    public function getOptions()
    {
        return $this->options;
    }
}
