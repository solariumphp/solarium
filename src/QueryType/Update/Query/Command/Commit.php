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
 * Update query commit command.
 *
 * @see https://solr.apache.org/guide/uploading-data-with-index-handlers.html#commit-and-optimize-during-updates
 */
class Commit extends AbstractCommand
{
    /**
     * Get command type.
     *
     * @return string
     */
    public function getType(): string
    {
        return UpdateQuery::COMMAND_COMMIT;
    }

    /**
     * Get softCommit option.
     *
     * @return bool|null
     */
    public function getSoftCommit(): ?bool
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
    public function setSoftCommit(bool $softCommit): self
    {
        $this->setOption('softcommit', $softCommit);

        return $this;
    }

    /**
     * Get waitSearcher option.
     *
     * @return bool|null
     */
    public function getWaitSearcher(): ?bool
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
    public function setWaitSearcher(bool $waitSearcher): self
    {
        $this->setOption('waitsearcher', $waitSearcher);

        return $this;
    }

    /**
     * Get expungeDeletes option.
     *
     * @return bool|null
     */
    public function getExpungeDeletes(): ?bool
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
    public function setExpungeDeletes(bool $expungeDeletes): self
    {
        $this->setOption('expungedeletes', $expungeDeletes);

        return $this;
    }
}
