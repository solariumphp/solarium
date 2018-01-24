<?php

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Document\DocumentInterface;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
 * Update query add command.
 *
 * @see http://wiki.apache.org/solr/UpdateXmlMessages#add.2BAC8-update
 */
class Add extends AbstractCommand
{
    /**
     * Documents to add.
     *
     * @var \Solarium\QueryType\Update\Query\Document\DocumentInterface[]
     */
    protected $documents = [];

    /**
     * Get command type.
     *
     * @return string
     */
    public function getType()
    {
        return UpdateQuery::COMMAND_ADD;
    }

    /**
     * Add a single document.
     *
     *
     * @param DocumentInterface $document
     *
     * @throws RuntimeException
     *
     * @return self Provides fluent interface
     */
    public function addDocument(DocumentInterface $document)
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
    public function addDocuments($documents)
    {
        //only check documents for type if in an array (iterating a Traversable may do unnecessary work)
        if (is_array($documents)) {
            foreach ($documents as $document) {
                if (!($document instanceof DocumentInterface)) {
                    throw new RuntimeException('Documents must implement DocumentInterface.');
                }
            }
        }

        //if we don't have documents so far, accept arrays or Traversable objects as-is
        if (empty($this->documents)) {
            $this->documents = $documents;

            return $this;
        }

        //if something Traversable is passed in, and there are existing documents, convert all to arrays before merging
        if ($documents instanceof \Traversable) {
            $documents = iterator_to_array($documents);
        }
        if ($this->documents instanceof \Traversable) {
            $this->documents = array_merge(iterator_to_array($this->documents), $documents);
        } else {
            $this->documents = array_merge($this->documents, $documents);
        }

        return $this;
    }

    /**
     * Get all documents.
     *
     * @return DocumentInterface[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set overwrite option.
     *
     * @param bool $overwrite
     *
     * @return self Provides fluent interface
     */
    public function setOverwrite($overwrite)
    {
        return $this->setOption('overwrite', $overwrite);
    }

    /**
     * Get overwrite option.
     *
     * @return bool
     */
    public function getOverwrite()
    {
        return $this->getOption('overwrite');
    }

    /**
     * Get commitWithin option.
     *
     * @param bool $commitWithin
     *
     * @return self Provides fluent interface
     */
    public function setCommitWithin($commitWithin)
    {
        return $this->setOption('commitwithin', $commitWithin);
    }

    /**
     * Set commitWithin option.
     *
     * @return bool
     */
    public function getCommitWithin()
    {
        return $this->getOption('commitwithin');
    }
}
