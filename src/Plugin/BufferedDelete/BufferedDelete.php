<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedDelete;

use Solarium\Exception\RuntimeException;
use Solarium\Plugin\AbstractBufferedUpdate\AbstractBufferedUpdate;
use Solarium\Plugin\BufferedDelete\Delete\Id as DeleteById;
use Solarium\Plugin\BufferedDelete\Delete\Query as DeleteQuery;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteById as AddDeleteByIdEvent;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteQuery as AddDeleteQueryEvent;
use Solarium\Plugin\BufferedDelete\Event\PostCommit as PostCommitEvent;
use Solarium\Plugin\BufferedDelete\Event\PostFlush as PostFlushEvent;
use Solarium\Plugin\BufferedDelete\Event\PreCommit as PreCommitEvent;
use Solarium\Plugin\BufferedDelete\Event\PreFlush as PreFlushEvent;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Result as UpdateResult;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Buffered delete plugin.
 *
 * If you need to delete a big number of documents in Solr it's much more efficient to do so in batches.
 * This plugin makes this as easy as possible.
 */
class BufferedDelete extends AbstractBufferedUpdate
{
    /**
     * Buffered document ids and/or queries to delete.
     *
     * @var AbstractDelete[]
     */
    protected $buffer = [];

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
        $event = new AddDeleteByIdEvent($delete);
        $this->addToBuffer($delete, $event);

        return $this;
    }

    /**
     * Add multiple document ids to delete.
     *
     * @param (int|string)[] $ids
     *
     * @return self Provides fluent interface
     */
    public function addDeleteByIds(array $ids): self
    {
        foreach ($ids as $id) {
            $this->addDeleteById($id);
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
        $event = new AddDeleteQueryEvent($delete);
        $this->addToBuffer($delete, $event);

        return $this;
    }

    /**
     * Add multiple queries to delete matching documents.
     *
     * @param string[] $queries
     *
     * @return self Provides fluent interface
     */
    public function addDeleteQueries(array $queries): self
    {
        foreach ($queries as $query) {
            $this->addDeleteQuery($query);
        }

        return $this;
    }

    /**
     * Get all deletes currently in the buffer.
     *
     * Any previously flushed deletes will not be included!
     *
     * @return AbstractDelete[]
     */
    public function getDeletes(): array
    {
        return $this->buffer;
    }

    /**
     * Flush any buffered deletes to Solr.
     *
     * @return bool|UpdateResult
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

    /**
     * Add a delete to the buffer and determine if the buffer needs to be flushed.
     *
     * @param AbstractDelete $delete   Delete to add
     * @param Event          $addEvent Event to trigger
     */
    protected function addToBuffer($delete, Event $addEvent): void
    {
        $this->buffer[] = $delete;

        $this->client->getEventDispatcher()->dispatch($addEvent);

        if (\count($this->buffer) === $this->options['buffersize']) {
            $this->flush();
        }
    }

    /**
     * Add all deletes from the buffer as commands to the update query.
     *
     * @param AbstractDelete[] $buffer
     */
    protected function addBufferToQuery(array $buffer): void
    {
        $it = new \ArrayIterator($buffer);

        $command = new DeleteCommand();

        while ($it->valid()) {
            $delete = $it->current();

            switch ($delete->getType()) {
                case AbstractDelete::TYPE_ID:
                    $command->addId($delete->getId());
                    break;
                case AbstractDelete::TYPE_QUERY:
                    $command->addQuery($delete->getQuery());
                    break;
                default:
                    throw new RuntimeException('Unsupported delete type in buffer');
            }

            $it->next();
        }

        $this->updateQuery->add(null, $command);
    }
}
