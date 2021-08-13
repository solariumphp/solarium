<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Spellcheck;

use Solarium\Component\ComponentTraits\SpellcheckTrait;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Component\SpellcheckInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Spellcheck\Result\Result;
use Solarium\QueryType\Spellcheck\Result\Term;

/**
 * Spellcheck Query.
 *
 * Can be used for an autocomplete feature.
 *
 * @see https://solr.apache.org/guide/spell-checking.html
 */
class Query extends BaseQuery implements SpellcheckInterface, QueryInterface
{
    use QueryTrait;
    use SpellcheckTrait;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'spell',
        'resultclass' => Result::class,
        'termclass' => Term::class,
        'omitheader' => true,
        'build' => false,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_SPELLCHECK;
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
