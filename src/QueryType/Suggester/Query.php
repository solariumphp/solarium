<?php

namespace Solarium\QueryType\Suggester;

use Solarium\Component\ComponentTraits\SuggesterTrait;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Component\SuggesterInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Result;
use Solarium\QueryType\Suggester\Result\Term;

/**
 * Suggester Query.
 *
 * Can be used for an autocomplete feature. See http://wiki.apache.org/solr/Suggester for more info.
 */
class Query extends BaseQuery implements SuggesterInterface, QueryInterface
{
    use SuggesterTrait;
    use QueryTrait;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'suggest',
        'resultclass' => Result::class,
        'dictionaryclass' => Dictionary::class,
        'termclass' => Term::class,
        'omitheader' => true,
        'build' => false,
        'reload' => false,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_SUGGESTER;
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
}
