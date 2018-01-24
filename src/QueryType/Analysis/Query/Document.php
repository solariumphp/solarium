<?php

namespace Solarium\QueryType\Analysis\Query;

use Solarium\Core\Client\Client;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Analysis\RequestBuilder\Document as RequestBuilder;
use Solarium\QueryType\Analysis\ResponseParser\Document as ResponseParser;
use Solarium\QueryType\Select\Result\DocumentInterface as ReadOnlyDocumentInterface;
use Solarium\QueryType\Update\Query\Document\DocumentInterface as DocumentInterface;

/**
 * Analysis document query.
 */
class Document extends AbstractQuery
{
    const DOCUMENT_TYPE_HINT_EXCEPTION_MESSAGE = 'The document argument must either implement
        \Solarium\QueryType\Select\Result\DocumentInterface (read-only) or
        \Solarium\QueryType\Update\Query\Document\DocumentInterface (read-write), instance of %s given.';

    /**
     * Documents to analyze.
     *
     * @var ReadOnlyDocumentInterface[]|DocumentInterface[]
     */
    protected $documents = [];

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'analysis/document',
        'resultclass' => 'Solarium\QueryType\Analysis\Result\Document',
        'omitheader' => true,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_ANALYSIS_DOCUMENT;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser();
    }

    /**
     * Add a single document.
     *
     * @param ReadOnlyDocumentInterface|DocumentInterface $document
     *
     * @throws RuntimeException If the given document doesn't have the right interface
     *
     * @return self Provides fluent interface
     */
    public function addDocument($document)
    {
        if (!($document instanceof ReadOnlyDocumentInterface) && !($document instanceof DocumentInterface)) {
            throw new RuntimeException(sprintf(static::DOCUMENT_TYPE_HINT_EXCEPTION_MESSAGE, get_class($document)));
        }

        $this->documents[] = $document;

        return $this;
    }

    /**
     * Add multiple documents.
     *
     * @param ReadOnlyDocumentInterface[]|DocumentInterface[] $documents
     *
     * @throws RuntimeException If the given documents doesn't have the right interface
     *
     * @return self Provides fluent interface
     */
    public function addDocuments($documents)
    {
        foreach ($documents as $document) {
            if (!($document instanceof ReadOnlyDocumentInterface) && !($document instanceof DocumentInterface)) {
                throw new RuntimeException(sprintf(static::DOCUMENT_TYPE_HINT_EXCEPTION_MESSAGE, get_class($document)));
            }
        }

        $this->documents = array_merge($this->documents, $documents);

        return $this;
    }

    /**
     * Get all documents.
     *
     * @return ReadOnlyDocumentInterface[]|DocumentInterface[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }
}
