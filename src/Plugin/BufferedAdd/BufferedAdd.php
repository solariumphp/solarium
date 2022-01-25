<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedAdd;

use Solarium\Core\Query\DocumentInterface;
use Solarium\Plugin\BufferedAdd\Event\AddDocument as AddDocumentEvent;
use Solarium\Plugin\BufferedAdd\Event\PostCommit as PostCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PostFlush as PostFlushEvent;
use Solarium\Plugin\BufferedAdd\Event\PreCommit as PreCommitEvent;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Result as UpdateResult;

/**
 * Buffered add plugin.
 *
 * If you need to add (or update) a big number of documents to Solr it's much more efficient to do so in batches.
 * This plugin makes this as easy as possible.
 *
 * You can use the lightweight {@see BufferedAddLite} if you don't need the plugin to dispatch events.
 */
class BufferedAdd extends BufferedAddLite
{
    /**
     * Add a document.
     *
     * @param DocumentInterface $document
     *
     * @return self Provides fluent interface
     */
    public function addDocument(DocumentInterface $document)
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
     * Flush any buffered documents to Solr.
     *
     * @param bool|null $overwrite
     * @param int|null  $commitWithin
     *
     * @return UpdateResult|false
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

        $command = new AddCommand();
        $command->setDocuments($event->getBuffer());

        if (null !== $overwrite = $event->getOverwrite()) {
            $command->setOverwrite($overwrite);
        }

        if (null !== $commitWithin = $event->getCommitWithin()) {
            $command->setCommitWithin($commitWithin);
        }

        $this->updateQuery->add(null, $command);
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

        $command = new AddCommand();
        $command->setDocuments($event->getBuffer());

        if (null !== $overwrite = $event->getOverwrite()) {
            $command->setOverwrite($overwrite);
        }

        $this->updateQuery->add(null, $command);
        $this->updateQuery->addCommit($event->getSoftCommit(), $event->getWaitSearcher(), $event->getExpungeDeletes());
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

        $event = new PostCommitEvent($result);
        $this->client->getEventDispatcher()->dispatch($event);

        return $result;
    }
}
