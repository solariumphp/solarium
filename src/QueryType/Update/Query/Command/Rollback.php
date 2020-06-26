<?php

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query rollback command.
 *
 * @see https://lucene.apache.org/solr/guide/uploading-data-with-index-handlers.html#rollback-operations
 */
class Rollback extends AbstractCommand
{
    /**
     * Get command type.
     *
     * @return string
     */
    public function getType(): string
    {
        return UpdateQuery::COMMAND_ROLLBACK;
    }
}
