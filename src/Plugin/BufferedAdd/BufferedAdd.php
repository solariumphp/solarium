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
use Solarium\Plugin\BufferedAdd\Event\AddDocument as AddDocumentEvent;
use Solarium\Plugin\BufferedAdd\Event\PostCommit as PostCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PostFlush as PostFlushEvent;
use Solarium\Plugin\BufferedAdd\Event\PreCommit as PreCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\QueryType\Update\Result as UpdateResult;

/**
 * Buffered add plugin.
 *
 * If you need to add (or update) a big number of documents to Solr it's much more efficient to do so in batches.
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
     * Buffered documents.
     *
     * @var DocumentInterface[]
     */
    protected $buffer = [];

    /**
     * Set the endpoint for the documents.
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
        $this->buffer[] = $document;

        $event = new AddDocumentEvent($document);
        $this->client->getEventDispatcher()->dispatch($event);

        if (\count($this->buffer) === $this->options['buffersize']) {
            $this->flush();
        }

        return $this;
    }

    /**
     * Add multiple documents.
     *
     * @param array $documents
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
     * Get all documents currently in the buffer.
     *
     * Any previously flushed documents will not be included!
     *
     * @return DocumentInterface[]
     */
    public function getDocuments(): array
    {
        return $this->buffer;
    }

    /**
     * Clear any buffered documents.
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
     * Flush any buffered documents to Solr.
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

        $this->updateQuery->addDocuments($event->getBuffer(), $event->getOverwrite(), $event->getCommitWithin());
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

        $event = new PostFlushEvent($result);
        $this->client->getEventDispatcher()->dispatch($event);

        return $result;
    }

    /**
     * Commit changes.
     *
     * Any remaining documents in the buffer will also be flushed
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

        $this->updateQuery->addDocuments($event->getBuffer(), $event->getOverwrite());
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
}
