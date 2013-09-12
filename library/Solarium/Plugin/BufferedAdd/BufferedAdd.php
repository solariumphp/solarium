<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\Plugin\BufferedAdd;

use Solarium\Client;
use Solarium\Core\Plugin\Plugin;
use Solarium\QueryType\Update\Result as UpdateResult;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\QueryType\Select\Result\DocumentInterface;
use Solarium\Plugin\BufferedAdd\Event\Events;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;
use Solarium\Plugin\BufferedAdd\Event\PostFlush as PostFlushEvent;
use Solarium\Plugin\BufferedAdd\Event\PreCommit as PreCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PostCommit as PostCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\AddDocument as AddDocumentEvent;

/**
 * Buffered add plugin
 *
 * If you need to add (or update) a big number of documents to Solr it's much more efficient to do so in batches.
 * This plugin makes this as easy as possible.
 */
class BufferedAdd extends Plugin
{
    /**
     * Default options
     *
     * @var array
     */
    protected $options = array(
        'buffersize' => 100,
    );

    /**
     * Update query instance
     *
     * @var UpdateQuery
     */
    protected $updateQuery;

    /**
     * Buffered documents
     *
     * @var DocumentInterface[]
     */
    protected $buffer = array();

    /**
     * End point to execute updates against.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Plugin init function
     *
     * This is an extension point for plugin implementations.
     * Will be called as soon as $this->client and options have been set.
     *
     * @return void
     */
    protected function initPluginType()
    {
        $this->updateQuery = $this->client->createUpdate();
    }

    /**
     * Set the endpoint for the documents
     *
     * @param string $endpoint The endpoint to set
     *
     * @return self
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * Return the endpoint
     *
     * @return string
     */
    public function getEndPoint()
    {
        return $this->endpoint;
    }

    /**
     * Set buffer size option
     *
     * @param  int  $size
     * @return self
     */
    public function setBufferSize($size)
    {
        return $this->setOption('buffersize', $size);
    }

    /**
     * Get buffer size option value
     *
     * @return int
     */
    public function getBufferSize()
    {
        return $this->getOption('buffersize');
    }

    /**
     * Create a document object instance and add it to the buffer
     *
     * @param  array $fields
     * @param  array $boosts
     * @return self  Provides fluent interface
     */
    public function createDocument($fields, $boosts = array())
    {
        $doc = $this->updateQuery->createDocument($fields, $boosts);
        $this->addDocument($doc);

        return $this;
    }

    /**
     * Add a document
     *
     * @param  DocumentInterface $document
     * @return self              Provides fluent interface
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
     * Add multiple documents
     *
     * @param array
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
     * Get all documents currently in the buffer
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
     * Clear any buffered documents
     *
     * @return self Provides fluent interface
     */
    public function clear()
    {
        $this->updateQuery = $this->client->createUpdate();
        $this->buffer = array();

        return $this;
    }

    /**
     * Flush any buffered documents to Solr
     *
     * @param  boolean              $overwrite
     * @param  int                  $commitWithin
     * @return boolean|UpdateResult
     */
    public function flush($overwrite = null, $commitWithin = null)
    {
        if (count($this->buffer) == 0) {
            // nothing to do
            return false;
        }

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
     * Commit changes
     *
     * Any remaining documents in the buffer will also be flushed
     *
     * @param  boolean      $overwrite
     * @param  boolean      $softCommit
     * @param  boolean      $waitSearcher
     * @param  boolean      $expungeDeletes
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
}
