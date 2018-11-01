<?php

namespace Solarium\Plugin\BufferedAdd;

use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Plugin\BufferedAdd\Event\AddDocument as AddDocumentEvent;
use Solarium\Plugin\BufferedAdd\Event\Events;
use Solarium\Plugin\BufferedAdd\Event\PostCommit as PostCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PostFlush as PostFlushEvent;
use Solarium\Plugin\BufferedAdd\Event\PreCommit as PreCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;
use Solarium\QueryType\Select\Result\DocumentInterface;
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
     * @param string $endpoint The endpoint to set
     *
     * @return self
     */
    public function setEndpoint($endpoint)
    {
        return $this->setOption('endpoint', $endpoint);
    }

    /**
     * Return the endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getOption('endpoint');
    }

    /**
     * Set buffer size option.
     *
     * @param int $size
     *
     * @return self
     */
    public function setBufferSize($size)
    {
        return $this->setOption('buffersize', $size);
    }

    /**
     * Get buffer size option value.
     *
     * @return int
     */
    public function getBufferSize()
    {
        return $this->getOption('buffersize');
    }

    /**
     * Set commitWithin time option.
     *
     * @param int $time
     *
     * @return self
     */
    public function setCommitWithin($time)
    {
        return $this->setOption('commitwithin', $time);
    }

    /**
     * Get commitWithin time option value.
     *
     * @return int
     */
    public function getCommitWithin()
    {
        return $this->getOption('commitwithin');
    }

    /**
     * Set overwrite boolean option.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setOverwrite($value)
    {
        return $this->setOption('overwrite', $value);
    }

    /**
     * Get overwrite boolean option value.
     *
     * @return bool
     */
    public function getOverwrite()
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
    public function createDocument($fields, $boosts = [])
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
    public function addDocument($document)
    {
        $this->buffer[] = $document;

        $event = new AddDocumentEvent($document);
        $this->client->getEventDispatcher()->dispatch(Events::ADD_DOCUMENT, $event);

        if (count($this->buffer) == $this->options['buffersize']) {
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
    public function addDocuments($documents)
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
    public function getDocuments()
    {
        return $this->buffer;
    }

    /**
     * Clear any buffered documents.
     *
     * @return self Provides fluent interface
     */
    public function clear()
    {
        $this->updateQuery = $this->client->createUpdate();
        $this->buffer = [];

        return $this;
    }

    /**
     * Flush any buffered documents to Solr.
     *
     * @param bool $overwrite
     * @param int  $commitWithin
     *
     * @return bool|UpdateResult
     */
    public function flush($overwrite = null, $commitWithin = null)
    {
        if (0 == count($this->buffer)) {
            // nothing to do
            return false;
        }

        $overwrite = is_null($overwrite) ? $this->getOverwrite() : $overwrite;
        $commitWithin = is_null($commitWithin) ? $this->getCommitWithin() : $commitWithin;

        $event = new PreFlushEvent($this->buffer, $overwrite, $commitWithin);
        $this->client->getEventDispatcher()->dispatch(Events::PRE_FLUSH, $event);

        $this->updateQuery->addDocuments($event->getBuffer(), $event->getOverwrite(), $event->getCommitWithin());
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

        $event = new PostFlushEvent($result);
        $this->client->getEventDispatcher()->dispatch(Events::POST_FLUSH, $event);

        return $result;
    }

    /**
     * Commit changes.
     *
     * Any remaining documents in the buffer will also be flushed
     *
     * @param bool $overwrite
     * @param bool $softCommit
     * @param bool $waitSearcher
     * @param bool $expungeDeletes
     *
     * @return UpdateResult
     */
    public function commit($overwrite = null, $softCommit = null, $waitSearcher = null, $expungeDeletes = null)
    {
        $event = new PreCommitEvent($this->buffer, $overwrite, $softCommit, $waitSearcher, $expungeDeletes);
        $this->client->getEventDispatcher()->dispatch(Events::PRE_COMMIT, $event);

        $this->updateQuery->addDocuments($this->buffer, $event->getOverwrite());
        $this->updateQuery->addCommit($event->getSoftCommit(), $event->getWaitSearcher(), $event->getExpungeDeletes());
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

        $event = new PostCommitEvent($result);
        $this->client->getEventDispatcher()->dispatch(Events::POST_COMMIT, $event);

        return $result;
    }

    /**
     * Plugin init function.
     *
     * This is an extension point for plugin implementations.
     * Will be called as soon as $this->client and options have been set.
     */
    protected function initPluginType()
    {
        $this->updateQuery = $this->client->createUpdate();
    }
}
