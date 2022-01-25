<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedDelete;

use Solarium\Plugin\BufferedDelete\Delete\Id as DeleteById;
use Solarium\Plugin\BufferedDelete\Delete\Query as DeleteQuery;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteById as AddDeleteByIdEvent;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteQuery as AddDeleteQueryEvent;
use Solarium\Plugin\BufferedDelete\Event\PostCommit as PostCommitEvent;
use Solarium\Plugin\BufferedDelete\Event\PostFlush as PostFlushEvent;
use Solarium\Plugin\BufferedDelete\Event\PreCommit as PreCommitEvent;
use Solarium\Plugin\BufferedDelete\Event\PreFlush as PreFlushEvent;
use Solarium\QueryType\Update\Result as UpdateResult;

/**
 * Buffered delete plugin.
 *
 * If you need to delete a big number of documents in Solr it's much more efficient to do so in batches.
 * This plugin makes this as easy as possible.
 *
 * You can use the lightweight {@see BufferedDeleteLite} if you don't need the plugin to dispatch events.
 */
class BufferedDelete extends BufferedDeleteLite
{
    /**
     * Add a document id to delete.
     *
     * @param int|string $id
     *
     * @return self Provides fluent interface
     */
    public function addDeleteById($id): self
    {
        $delete = new DeleteById($id);
        $this->buffer[] = $delete;

        $event = new AddDeleteByIdEvent($delete);
        $this->client->getEventDispatcher()->dispatch($event);

        if (\count($this->buffer) === $this->options['buffersize']) {
            $this->flush();
        }

        return $this;
    }

    /**
     * Add a query to delete matching documents.
     *
     * @param string $query
     *
     * @return self Provides fluent interface
     */
    public function addDeleteQuery(string $query): self
    {
        $delete = new DeleteQuery($query);
        $this->buffer[] = $delete;

        $event = new AddDeleteQueryEvent($delete);
        $this->client->getEventDispatcher()->dispatch($event);

        if (\count($this->buffer) === $this->options['buffersize']) {
            $this->flush();
        }

        return $this;
    }

    /**
     * Flush any buffered deletes to Solr.
     *
     * @return UpdateResult|false
     */
    public function flush()
    {
        if (0 === \count($this->buffer)) {
            // nothing to do
            return false;
        }

        $event = new PreFlushEvent($this->buffer);
        $this->client->getEventDispatcher()->dispatch($event);

        $this->addBufferToQuery($event->getBuffer());
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

        $event = new PostFlushEvent($result);
        $this->client->getEventDispatcher()->dispatch($event);

        return $result;
    }

    /**
     * Commit changes.
     *
     * Any remaining deletes in the buffer will also be flushed
     *
     * @param bool|null $softCommit
     * @param bool|null $waitSearcher
     * @param bool|null $expungeDeletes
     *
     * @return UpdateResult
     */
    public function commit(?bool $softCommit = null, ?bool $waitSearcher = null, ?bool $expungeDeletes = null): UpdateResult
    {
        $event = new PreCommitEvent($this->buffer, $softCommit, $waitSearcher, $expungeDeletes);
        $this->client->getEventDispatcher()->dispatch($event);

        $this->addBufferToQuery($event->getBuffer());
        $this->updateQuery->addCommit($event->getSoftCommit(), $event->getWaitSearcher(), $event->getExpungeDeletes());
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

        $event = new PostCommitEvent($result);
        $this->client->getEventDispatcher()->dispatch($event);

        return $result;
    }
}
