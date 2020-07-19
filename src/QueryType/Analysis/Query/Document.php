<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Analysis\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\DocumentInterface;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Analysis\RequestBuilder\Document as RequestBuilder;
use Solarium\QueryType\Analysis\ResponseParser\Document as ResponseParser;
use Solarium\QueryType\Analysis\Result\Document as ResultDocument;

/**
 * Analysis document query.
 */
class Document extends AbstractQuery
{
    /**
     * Documents to analyze.
     *
     * @var DocumentInterface[]
     */
    protected $documents = [];

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'analysis/document',
        'resultclass' => ResultDocument::class,
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
     * @param DocumentInterface $document
     *
     * @throws RuntimeException If the given document doesn't have the right interface
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
     * @param DocumentInterface[] $documents
     *
     * @throws RuntimeException If the given documents doesn't have the right interface
     *
     * @return self Provides fluent interface
     */
    public function addDocuments(array $documents): self
    {
        foreach ($documents as $document) {
            if (!($document instanceof DocumentInterface)) {
                throw new RuntimeException('Document must implement DocumentInterface.');
            }
        }

        $this->documents = array_merge($this->documents, $documents);

        return $this;
    }

    /**
     * Get all documents.
     *
     * @return DocumentInterface
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }
}
