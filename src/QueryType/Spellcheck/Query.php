<?php

namespace Solarium\QueryType\Spellcheck;

use Solarium\Component\ComponentTraits\SpellcheckTrait;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Component\SpellcheckInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;

/**
 * Spellcheck Query.
 *
 * Can be used for an autocomplete feature. See http://wiki.apache.org/solr/SpellcheckComponent for more info.
 */
class Query extends BaseQuery implements SpellcheckInterface, QueryInterface
{
    use SpellcheckTrait;
    use QueryTrait;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'spell',
        'resultclass' => 'Solarium\QueryType\Spellcheck\Result\Result',
        'termclass' => 'Solarium\QueryType\Spellcheck\Result\Term',
        'omitheader' => true,
        'build' => false,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_SPELLCHECK;
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
