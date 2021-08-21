<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\MoreLikeThis;

use Solarium\Component\ComponentTraits\MoreLikeThisTrait;
use Solarium\Component\MoreLikeThisInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Document;

/**
 * MoreLikeThis Query.
 *
 * Can be used to select documents and/or facets from Solr. This querytype has
 * lots of options and there are many Solarium subclasses for it.
 * See the Solr documentation and the relevant Solarium classes for more info.
 *
 * @see https://solr.apache.org/guide/other-parsers.html#more-like-this-query-parser
 */
class Query extends SelectQuery implements MoreLikeThisInterface
{
    use MoreLikeThisTrait;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'mlt',
        'resultclass' => Result::class,
        'documentclass' => Document::class,
        'query' => '*:*',
        'start' => 0,
        'rows' => 10,
        'fields' => '*,score',
        'matchinclude' => false,
        'matchoffset' => 0,
        'interestingTerms' => 'none',
        'stream' => false,
        'omitheader' => true,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_MORELIKETHIS;
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
     * Set query stream option.
     *
     * Set to true to post query content instead of using the URL param
     *
     * @see https://solr.apache.org/guide/content-streams.html
     *
     * @param bool $stream
     *
     * @return self Provides fluent interface
     */
    public function setQueryStream(bool $stream): self
    {
        $this->setOption('stream', $stream);

        return $this;
    }

    /**
     * Get stream option.
     *
     * @return bool|null
     */
    public function getQueryStream(): ?bool
    {
        return $this->getOption('stream');
    }

    /**
     * Set MLT fields option.
     *
     * The fields to use for similarity. NOTE: if possible, these should have a
     * stored TermVector.
     *
     * Separate multiple fields with commas if you use string input.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param string|array $fields
     *
     * @return self Provides fluent interface
     */
    public function setMltFields($fields): self
    {
        if (\is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        $this->setOption('mltfields', $fields);

        return $this;
    }

    /**
     * Get MLT fields option.
     *
     * @return array
     */
    public function getMltFields(): array
    {
        $value = $this->getOption('mltfields');
        if (null === $value) {
            $value = [];
        }

        return $value;
    }

    /**
     * Set the match.include parameter, which is either 'true' or 'false'.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#request-handler-parameters
     *
     * @param bool $include
     *
     * @return self Provides fluent interface
     */
    public function setMatchInclude(bool $include): self
    {
        $this->setOption('matchinclude', $include);

        return $this;
    }

    /**
     * Get the match.include parameter.
     *
     * @return bool|null
     */
    public function getMatchInclude(): ?bool
    {
        return $this->getOption('matchinclude');
    }

    /**
     * Set the mlt.match.offset parameter, which determines on which result from the query MLT should operate.
     * For paging of MLT use setStart / setRows.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#request-handler-parameters
     *
     * @param int $offset
     *
     * @return self Provides fluent interface
     */
    public function setMatchOffset(int $offset): self
    {
        $this->setOption('matchoffset', $offset);

        return $this;
    }

    /**
     * Get the mlt.match.offset parameter.
     *
     * @return int|null
     */
    public function getMatchOffset(): ?int
    {
        return $this->getOption('matchoffset');
    }
}
