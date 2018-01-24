<?php

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query optimize command.
 *
 * @see http://wiki.apache.org/solr/UpdateXmlMessages#A.22commit.22_and_.22optimize.22
 */
class Optimize extends AbstractCommand
{
    /**
     * Get command type.
     *
     * @return string
     */
    public function getType()
    {
        return UpdateQuery::COMMAND_OPTIMIZE;
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
     * Get maxSegments option.
     *
     * @return bool
     */
    public function getMaxSegments()
    {
        return $this->getOption('maxsegments');
    }

    /**
     * Set maxSegments option.
     *
     * @param bool $maxSegments
     *
     * @return self Provides fluent interface
     */
    public function setMaxSegments($maxSegments)
    {
        return $this->setOption('maxsegments', $maxSegments);
    }
}
