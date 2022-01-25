<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedAdd;

use Solarium\Core\Query\DocumentInterface;
use Solarium\Plugin\AbstractBufferedUpdate\AbstractBufferedUpdate;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Result as UpdateResult;

/**
 * Buffered add lite plugin.
 *
 * If you need to add (or update) a big number of documents to Solr it's much more efficient to do so in batches.
 * This plugin makes this as easy as possible.
 *
 * Unlike {@see BufferedAdd}, this plugin doesn't dispatch any events.
 */
class BufferedAddLite extends AbstractBufferedUpdate
{
    /**
     * Buffered documents to add.
     *
     * @var DocumentInterface[]
     */
    protected $buffer = [];

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
    public function addDocument(DocumentInterface $document)
    {
        $this->buffer[] = $document;

        if (\count($this->buffer) === $this->options['buffersize']) {
            $this->flush();
        }

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

        $command = new AddCommand();
        $command->setDocuments($this->buffer);

        if (null !== $overwrite) {
            $command->setOverwrite($overwrite);
        }

        if (null !== $commitWithin) {
            $command->setCommitWithin($commitWithin);
        }

        $this->updateQuery->add(null, $command);
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

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

        $command = new AddCommand();
        $command->setDocuments($this->buffer);

        if (null !== $overwrite) {
            $command->setOverwrite($overwrite);
        }

        $this->updateQuery->add(null, $command);
        $this->updateQuery->addCommit($softCommit, $waitSearcher, $expungeDeletes);
        $result = $this->client->update($this->updateQuery, $this->getEndpoint());
        $this->clear();

        return $result;
    }
}
