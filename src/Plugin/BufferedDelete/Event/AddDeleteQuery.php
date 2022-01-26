<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedDelete\Event;

use Solarium\Plugin\BufferedDelete\Delete\Query;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * AddDeleteQuery event, see {@see Events} for details.
 */
class AddDeleteQuery extends Event
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * Event constructor.
     *
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Get the query for this event.
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query->getQuery();
    }

    /**
     * Set the query for this event, this way you can alter the query before it is sent to Solr.
     *
     * @param string $query
     *
     * @return self Provides fluent interface
     */
    public function setQuery(string $query): self
    {
        $this->query->setQuery($query);

        return $this;
    }
}
