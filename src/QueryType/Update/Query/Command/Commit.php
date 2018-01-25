<?php

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query commit command.
 *
 * @see http://wiki.apache.org/solr/UpdateXmlMessages#A.22commit.22_and_.22optimize.22
 */
class Commit extends AbstractCommand
{
    /**
     * Get command type.
     *
     * @return string
     */
    public function getType()
    {
        return UpdateQuery::COMMAND_COMMIT;
    }

    /**
     * Get softCommit option.
     *
     * @return bool
     */
    public function getSoftCommit()
    {
        return $this->getOption('softcommit');
    }

    /**
     * Set softCommit option.
     *
     * @param bool $softCommit
     *
     * @return self Provides fluent interface
     */
    public function setSoftCommit($softCommit)
    {
        return $this->setOption('softcommit', $softCommit);
    }

    /**
     * Get waitSearcher option.
     *
     * @return bool
     */
    public function getWaitSearcher()
    {
        return $this->getOption('waitsearcher');
    }

    /**
     * Set waitSearcher option.
     *
     * @param bool $waitSearcher
     *
     * @return self Provides fluent interface
     */
    public function setWaitSearcher($waitSearcher)
    {
        return $this->setOption('waitsearcher', $waitSearcher);
    }

    /**
     * Get expungeDeletes option.
     *
     * @return bool
     */
    public function getExpungeDeletes()
    {
        return $this->getOption('expungedeletes');
    }

    /**
     * Set expungeDeletes option.
     *
     * @param bool $expungeDeletes
     *
     * @return self Provides fluent interface
     */
    public function setExpungeDeletes($expungeDeletes)
    {
        return $this->setOption('expungedeletes', $expungeDeletes);
    }
}
