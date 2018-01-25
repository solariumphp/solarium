<?php

namespace Solarium\QueryType\Terms;

use Solarium\Component\ComponentTraits\TermsTrait;
use Solarium\Component\TermsInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;

/**
 * Terms query.
 *
 * A terms query provides access to the indexed terms in a field and the number of documents that match each term.
 * This can be useful for doing auto-suggest or other things that operate at the term level instead of the search
 * or document level. Retrieving terms in index order is very fast since the implementation directly uses Lucene's
 * TermEnum to iterate over the term dictionary.
 */
class Query extends BaseQuery implements TermsInterface
{
    use TermsTrait;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'resultclass' => 'Solarium\QueryType\Terms\Result',
        'handler' => 'terms',
        'omitheader' => true,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_TERMS;
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
}
