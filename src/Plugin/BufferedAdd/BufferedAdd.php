<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedAdd;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Core\Query\DocumentInterface;
use Solarium\Exception\RuntimeException;
use Solarium\Plugin\BufferedAdd\Delete\Id as DeleteById;
use Solarium\Plugin\BufferedAdd\Delete\Query as DeleteQuery;
use Solarium\Plugin\BufferedAdd\Event\AddDeleteById as AddDeleteByIdEvent;
use Solarium\Plugin\BufferedAdd\Event\AddDeleteQuery as AddDeleteQueryEvent;
use Solarium\Plugin\BufferedAdd\Event\AddDocument as AddDocumentEvent;
use Solarium\Plugin\BufferedAdd\Event\PostCommit as PostCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PostFlush as PostFlushEvent;
use Solarium\Plugin\BufferedAdd\Event\PreCommit as PreCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\QueryType\Update\Result as UpdateResult;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Buffered add plugin.
 *
 * If you need to add (or update) and/or delete a big number of documents to Solr it's much more efficient to do so in batches.
 * This plugin makes this as easy as possible.
 */
class BufferedAdd extends AbstractPlugin
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'buffersize' => 100,
    ];

    /**
     * Update query instance.
     *
     * @var UpdateQuery
     */
    protected $updateQuery;

    /**
     * Buffered documents to add, and/or document ids and/or queries to delete.
     *
     * @var (DocumentInterface|AbstractDelete)[]
     */
    protected $buffer = [];

    /**
     * Set the endpoint for the documents and deletes.
     *
     * @param Endpoint $endpoint The endpoint to set
     *
     * @return self Provides fluent interface
     */
    public function setEndpoint(Endpoint $endpoint): self
    {
        $this->setOption('endpoint', $endpoint);

        return $this;
    }

    /**
     * Return the endpoint.
     *
     * @return Endpoint|null
     */
    public function getEndpoint(): ?Endpoint
    {
        return $this->getOption('endpoint');
    }

    /**
     * Set buffer size option.
     *
     * @param int $size
     *
     * @return self Provides fluent interface
     */
    public function setBufferSize(int $size): self
    {
        $this->setOption('buffersize', $size);

        return $this;
    }

    /**
     * Get buffer size option value.
     *
     * @return int|null
     */
    public function getBufferSize(): ?int
    {
        return $this->getOption('buffersize');
    }

    /**
     * Set commitWithin time option.
     *
     * @param int $time
     *
     * @return self Provides fluent interface
     */
    public function setCommitWithin(int $time): self
    {
        $this->setOption('commitwithin', $time);

        return $this;
    }

    /**
     * Get commitWithin time option value.
     *
     * @return int|null
     */
    public function getCommitWithin(): ?int
    {
        return $this->getOption('commitwithin');
    }

    /**
     * Set overwrite boolean option.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setOverwrite(bool $value): self
    {
        $this->setOption('overwrite', $value);

        return $this;
    }

    /**
     * Get overwrite boolean option value.
     *
     * @return bool|null
     */
    public function getOverwrite(): ?bool
    {
        return $this->getOption('overwrite');
    }

    /**
     * Create a document object instance and add it to the buffer.
     *
     * @param array $fields
     * @param array $boosts
     *
     * @return self Provides fluent interface
     */
    public function createDocument(array $fields, array $boosts = []): self
    {
        $doc = $this->updateQuery->createDocument($fields, $boosts);
        $this->addDocument($doc);

        return $this;
    }

    /**
     * Add a document.
     *
     * @param DocumentInterface $document
     *
     * @return self Provides fluent interface
     */
    public function addDocument(DocumentInterface $document): self
    {
        $event = new AddDocumentEvent($document);
        $this->addToBuffer($document, $event);

        return $this;
    }

    /**
     * Add multiple documents.
     *
     * @param DocumentInterface[] $documents
     *
     * @return self Provides fluent interface
     */
    public function addDocuments(array $documents): self
    {
        foreach ($documents as $document) {
            $this->addDocument($document);
        }

        return $this;
    }

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
     * Get all documents and deletes currently in the buffer.
     *
     * Any previously flushed documents and deletes will not be included!
     *
     * @return (DocumentInterface|AbstractDelete)[]
     */
    public function getDocuments(): array
    {
        return $this->buffer;
    }

    /**
     * Clear any buffered documents and deletes.
     *
     * @return self Provides fluent interface
     */
    public function clear(): self
    {
        $this->updateQuery = $this->client->createUpdate();
        $this->buffer = [];

        return $this;
    }

    /**
     * Flush any buffered documents and deletes to Solr.
     *
     * @param bool|null $overwrite
     * @param int|null  $commitWithin
     *
     * @return bool|UpdateResult
     */
    public function flush(?bool $overwrite = null, ?int $commitWithin = null)
    {
        if (0 === \count($this->buffer)) {
            // nothing to do
            return false;
        }

        $overwrite = $overwrite ?? $this->getOverwrite();
        $commitWithin = $commitWithin ?? $this->getCommitWithin();

        $event = new PreFlushEvent($this->buffer, $overwrite, $commitWithin);
        $this->client->getEventDispatcher()->dispatch($event);

        $this->addBufferToQuery($event->getBuffer(), $event->getOverwrite(), $event->getCommitWithin());
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

        $event = new PostFlushEvent($result);
        $this->client->getEventDispatcher()->dispatch($event);

        return $result;
    }

    /**
     * Commit changes.
     *
     * Any remaining documents and deletes in the buffer will also be flushed
     *
     * @param bool|null $overwrite
     * @param bool|null $softCommit
     * @param bool|null $waitSearcher
     * @param bool|null $expungeDeletes
     *
     * @return UpdateResult
     */
    public function commit(?bool $overwrite = null, ?bool $softCommit = null, ?bool $waitSearcher = null, ?bool $expungeDeletes = null): UpdateResult
    {
        $overwrite = $overwrite ?? $this->getOverwrite();

        $event = new PreCommitEvent($this->buffer, $overwrite, $softCommit, $waitSearcher, $expungeDeletes);
        $this->client->getEventDispatcher()->dispatch($event);

        $this->addBufferToQuery($event->getBuffer(), $event->getOverwrite());
        $this->updateQuery->addCommit($event->getSoftCommit(), $event->getWaitSearcher(), $event->getExpungeDeletes());
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

        $event = new PostCommitEvent($result);
        $this->client->getEventDispatcher()->dispatch($event);

        return $result;
    }

    /**
     * Plugin init function.
     *
     * This is an extension point for plugin implementations.
     * Will be called as soon as $this->client and options have been set.
     */
    protected function initPluginType(): void
    {
        $this->updateQuery = $this->client->createUpdate();
    }

    /**
     * Add a document or delete to the buffer and determine if the buffer needs to be flushed.
     *
     * @param DocumentInterface|AbstractDelete $documentOrDelete Document or delete to add
     * @param Event                            $postAddEvent     Event to trigger after adding but before flushing
     */
    protected function addToBuffer($documentOrDelete, Event $postAddEvent): void
    {
        $this->buffer[] = $documentOrDelete;

        $this->client->getEventDispatcher()->dispatch($postAddEvent);

        if (\count($this->buffer) === $this->options['buffersize']) {
            $this->flush();
        }
    }

    /**
     * Add all documents and deletes from a buffer as commands to the update query.
     *
     * @param (DocumentInterface|AbstractDelete)[] $buffer
     * @param bool|null $overwrite
     * @param int|null  $commitWithin
     */
    protected function addBufferToQuery(array $buffer, ?bool $overwrite = null, ?int $commitWithin = null): void
    {
        $it = new \ArrayIterator($buffer);

        $add = new AddCommand();
        $del = new DeleteCommand();

        if (null !== $overwrite) {
            $add->setOverwrite($overwrite);
        }

        if (null !== $commitWithin) {
            $add->setCommitWithin($commitWithin);
        }

        while ($it->valid()) {
            $docOrDel = $it->current();

            switch (true) {
                case is_a($docOrDel, $isA = DocumentInterface::class):
                    $command = $add->addDocument($docOrDel);
                    break;
                case is_a($docOrDel, $isA = AbstractDelete::class):
                    switch ($docOrDel->getType()) {
                        case AbstractDelete::TYPE_ID:
                            $command = $del->addId($docOrDel->getId());
                            break;
                        case AbstractDelete::TYPE_QUERY:
                            $command = $del->addQuery($docOrDel->getQuery());
                            break;
                        default:
                            throw new RuntimeException('Unsupported delete type in buffer');
                    }
                    break;
                default:
                    throw new RuntimeException('Unsupported type in buffer');
            }

            $it->next();

            if (!$it->valid() || !is_a($it->current(), $isA)) {
                $this->updateQuery->add(null, clone $command);
                $command->clear();
            }
        }
    }
}
