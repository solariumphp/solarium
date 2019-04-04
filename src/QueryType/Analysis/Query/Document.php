<?php

namespace Solarium\QueryType\Analysis\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Analysis\RequestBuilder\Document as RequestBuilder;
use Solarium\QueryType\Analysis\ResponseParser\Document as ResponseParser;
use Solarium\QueryType\Select\Result\ResultDocumentInterface;
use Solarium\QueryType\Update\Query\Document\UpdateDocumentInterface;

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
     * @var ResultDocumentInterface[]|UpdateDocumentInterface[]
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
    public function getType(): string
    {
        return Client::QUERY_ANALYSIS_DOCUMENT;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Add a single document.
     *
     * @param ResultDocumentInterface|UpdateDocumentInterface $document
     *
     * @throws RuntimeException If the given document doesn't have the right interface
     *
     * @return self Provides fluent interface
     */
    public function addDocument($document): self
    {
        if (!($document instanceof ResultDocumentInterface) && !($document instanceof UpdateDocumentInterface)) {
            throw new RuntimeException(sprintf(static::DOCUMENT_TYPE_HINT_EXCEPTION_MESSAGE, get_class($document)));
        }

        $this->documents[] = $document;

        return $this;
    }

    /**
     * Add multiple documents.
     *
     * @param ResultDocumentInterface[]|UpdateDocumentInterface[] $documents
     *
     * @throws RuntimeException If the given documents doesn't have the right interface
     *
     * @return self Provides fluent interface
     */
    public function addDocuments(array $documents): self
    {
        foreach ($documents as $document) {
            if (!($document instanceof ResultDocumentInterface) && !($document instanceof UpdateDocumentInterface)) {
                throw new RuntimeException(sprintf(static::DOCUMENT_TYPE_HINT_EXCEPTION_MESSAGE, get_class($document)));
            }
        }

        $this->documents = array_merge($this->documents, $documents);

        return $this;
    }

    /**
     * Get all documents.
     *
     * @return ResultDocumentInterface[]|UpdateDocumentInterface[]
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }
}
