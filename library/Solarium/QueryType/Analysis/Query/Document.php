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
namespace Solarium\QueryType\Analysis\Query;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Analysis\ResponseParser\Document as ResponseParser;
use Solarium\QueryType\Analysis\RequestBuilder\Document as RequestBuilder;
use Solarium\QueryType\Select\Result\Document as ResultDocument;

/**
 * Analysis document query
 */
class Document extends Query
{

    /**
     * Documents to analyze
     *
     * @var ResultDocument[]
     */
    protected $documents = array();

    /**
     * Default options
     *
     * @var array
     */
    protected $options = array(
        'handler'       => 'analysis/document',
        'resultclass'   => 'Solarium\QueryType\Analysis\Result\Document',
        'omitheader'    => true,
    );

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_ANALYSIS_DOCUMENT;
    }

    /**
     * Get a requestbuilder for this query
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder;
    }

    /**
     * Get a response parser for this query
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser;
    }

    /**
     * Add a single document
     *
     * @param  object $document
     * @return self   Provides fluent interface
     */
    public function addDocument($document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Add multiple documents
     *
     * @param  DocumentInterface[] $documents
     * @return self                fluent interface
     */
    public function addDocuments($documents)
    {
        $this->documents = array_merge($this->documents, $documents);

        return $this;
    }

    /**
     * Get all documents
     *
     * @return ResultDocument[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

}
