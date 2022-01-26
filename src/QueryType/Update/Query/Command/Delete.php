<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query delete command.
 *
 * @see https://solr.apache.org/guide/uploading-data-with-index-handlers.html#delete-operations
 */
class Delete extends AbstractCommand
{
    /**
     * Ids to delete.
     *
     * @var array
     */
    protected $ids = [];

    /**
     * Delete queries.
     *
     * @var array
     */
    protected $queries = [];

    /**
     * Get command type.
     *
     * @return string
     */
    public function getType(): string
    {
        return UpdateQuery::COMMAND_DELETE;
    }

    /**
     * Add a single ID to the delete command.
     *
     * @param int|string $id
     *
     * @return self Provides fluent interface
     */
    public function addId($id): self
    {
        $this->ids[] = $id;

        return $this;
    }

    /**
     * Add multiple IDs to the delete command.
     *
     * @param array $ids
     *
     * @return self Provides fluent interface
     */
    public function addIds(array $ids): self
    {
        $this->ids = array_merge($this->ids, $ids);

        return $this;
    }

    /**
     * Get all ids of this delete command.
     *
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * Add a single query to the delete command.
     *
     * @param string $query
     *
     * @return self Provides fluent interface
     */
    public function addQuery(string $query): self
    {
        $this->queries[] = $query;

        return $this;
    }

    /**
     * Add multiple queries to the delete command.
     *
     * @param array $queries
     *
     * @return self Provides fluent interface
     */
    public function addQueries(array $queries): self
    {
        $this->queries = array_merge($this->queries, $queries);

        return $this;
    }

    /**
     * Get all queries of this delete command.
     *
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * Clear all ids and queries.
     *
     * @return self Provides fluent interface
     */
    public function clear(): self
    {
        $this->ids = [];
        $this->queries = [];

        return $this;
    }

    /**
     * Build ids/queries based on options.
     */
    protected function init(): void
    {
        $id = $this->getOption('id');
        if (null !== $id) {
            if (\is_array($id)) {
                $this->addIds($id);
            } else {
                $this->addId($id);
            }
        }

        $queries = $this->getOption('query');
        if (null !== $queries) {
            if (\is_array($queries)) {
                $this->addQueries($queries);
            } else {
                $this->addQuery($queries);
            }
        }
    }
}
