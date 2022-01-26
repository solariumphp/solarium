<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\Core\Query\DocumentInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query add command.
 *
 * @see https://solr.apache.org/guide/uploading-data-with-index-handlers.html#adding-documents
 */
class Add extends AbstractCommand
{
    /**
     * Documents to add.
     *
     * @var DocumentInterface[]
     */
    protected $documents = [];

    /**
     * Get command type.
     *
     * @return string
     */
    public function getType(): string
    {
        return UpdateQuery::COMMAND_ADD;
    }

    /**
     * Add a single document.
     *
     * @param DocumentInterface $document
     *
     * @return self Provides fluent interface
     */
    public function addDocument(DocumentInterface $document): self
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Add multiple documents.
     *
     * @param array|\Traversable $documents
     *
     * @throws RuntimeException If any of the given documents does not implement DocumentInterface
     *
     * @return self Provides fluent interface
     */
    public function addDocuments($documents): self
    {
        if ($documents instanceof \Traversable) {
            $documents = iterator_to_array($documents);
        }

        foreach ($documents as $document) {
            if (!($document instanceof DocumentInterface)) {
                throw new RuntimeException('Documents must implement DocumentInterface.');
            }
        }

        if (empty($this->documents)) {
            $this->documents = $documents;
        } else {
            $this->documents = array_merge($this->documents, $documents);
        }

        return $this;
    }

    /**
     * Set documents.
     *
     * This overwrite any previously added documents.
     *
     * @param DocumentInterface[] $documents
     *
     * @return self Provides fluent interface
     */
    public function setDocuments(array $documents): self
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * Get all documents.
     *
     * @return DocumentInterface[]
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    /**
     * Clear all documents.
     *
     * @return self Provides fluent interface
     */
    public function clear(): self
    {
        $this->documents = [];

        return $this;
    }

    /**
     * Set overwrite option.
     *
     * @param bool $overwrite
     *
     * @return self Provides fluent interface
     */
    public function setOverwrite(bool $overwrite): self
    {
        $this->setOption('overwrite', $overwrite);

        return $this;
    }

    /**
     * Get overwrite option.
     *
     * @return bool|null
     */
    public function getOverwrite(): ?bool
    {
        return $this->getOption('overwrite');
    }

    /**
     * Get commitWithin option.
     *
     * @param int $commitWithin
     *
     * @return self Provides fluent interface
     */
    public function setCommitWithin(int $commitWithin): self
    {
        $this->setOption('commitwithin', $commitWithin);

        return $this;
    }

    /**
     * Set commitWithin option.
     *
     * @return int|null
     */
    public function getCommitWithin(): ?int
    {
        return $this->getOption('commitwithin');
    }
}
