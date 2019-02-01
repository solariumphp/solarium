<?php

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query rollback command.
 *
 * @see http://wiki.apache.org/solr/UpdateXmlMessages#A.22rollback.22
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
