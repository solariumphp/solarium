<?php

namespace Solarium\QueryType\MoreLikeThis;

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
 */
class Query extends SelectQuery
{
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
        'interestingTerms' => 'none',
        'matchinclude' => false,
        'matchoffset' => 0,
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
     * @see http://wiki.apache.org/solr/ContentStream ContentStream
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
     * Set the interestingTerms parameter.  Must be one of: none, list, details.
     *
     * @see http://wiki.apache.org/solr/MoreLikeThisHandler#Params
     *
     * @param string $term
     *
     * @return self Provides fluent interface
     */
    public function setInterestingTerms(string $term): self
    {
        $this->setOption('interestingTerms', $term);
        return $this;
    }

    /**
     * Get the interestingTerm parameter.
     *
     * @return string|null
     */
    public function getInterestingTerms(): ?string
    {
        return $this->getOption('interestingTerms');
    }

    /**
     * Set the match.include parameter, which is either 'true' or 'false'.
     *
     * @see http://wiki.apache.org/solr/MoreLikeThisHandler#Params
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
     * Set the mlt.match.offset parameter, which determines the which result from the query should be used for MLT
     * For paging of MLT use setStart / setRows.
     *
     * @see http://wiki.apache.org/solr/MoreLikeThisHandler#Params
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

    /**
     * Set MLT fields option.
     *
     * The fields to use for similarity. NOTE: if possible, these should have a
     * stored TermVector
     *
     * Separate multiple fields with commas if you use string input.
     *
     * @param string|array $fields
     *
     * @return self Provides fluent interface
     */
    public function setMltFields($fields): self
    {
        if (is_string($fields)) {
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
     * Set minimumtermfrequency option.
     *
     * Minimum Term Frequency - the frequency below which terms will be ignored
     * in the source doc.
     *
     * @param int $minimum
     *
     * @return self Provides fluent interface
     */
    public function setMinimumTermFrequency(int $minimum): self
    {
        $this->setOption('minimumtermfrequency', $minimum);
        return $this;
    }

    /**
     * Get minimumtermfrequency option.
     *
     * @return int|null
     */
    public function getMinimumTermFrequency(): ?int
    {
        return $this->getOption('minimumtermfrequency');
    }

    /**
     * Set minimumdocumentfrequency option.
     *
     * Minimum Document Frequency - the frequency at which words will be
     * ignored which do not occur in at least this many docs.
     *
     * @param int $minimum
     *
     * @return self Provides fluent interface
     */
    public function setMinimumDocumentFrequency(int $minimum): self
    {
        $this->setOption('minimumdocumentfrequency', $minimum);
        return $this;
    }

    /**
     * Get minimumdocumentfrequency option.
     *
     * @return int|null
     */
    public function getMinimumDocumentFrequency(): ?int
    {
        return $this->getOption('minimumdocumentfrequency');
    }

    /**
     * Set minimumwordlength option.
     *
     * Minimum word length below which words will be ignored.
     *
     * @param int $minimum
     *
     * @return self Provides fluent interface
     */
    public function setMinimumWordLength(int $minimum): self
    {
        $this->setOption('minimumwordlength', $minimum);
        return $this;
    }

    /**
     * Get minimumwordlength option.
     *
     * @return int|null
     */
    public function getMinimumWordLength(): ?int
    {
        return $this->getOption('minimumwordlength');
    }

    /**
     * Set maximumwordlength option.
     *
     * Maximum word length above which words will be ignored.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaximumWordLength(int $maximum): self
    {
        $this->setOption('maximumwordlength', $maximum);
        return $this;
    }

    /**
     * Get maximumwordlength option.
     *
     * @return int|null
     */
    public function getMaximumWordLength(): ?int
    {
        return $this->getOption('maximumwordlength');
    }

    /**
     * Set maximumqueryterms option.
     *
     * Maximum number of query terms that will be included in any generated
     * query.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaximumQueryTerms(int $maximum): self
    {
        $this->setOption('maximumqueryterms', $maximum);
        return $this;
    }

    /**
     * Get maximumqueryterms option.
     *
     * @return int|null
     */
    public function getMaximumQueryTerms(): ?int
    {
        return $this->getOption('maximumqueryterms');
    }

    /**
     * Set maximumnumberoftokens option.
     *
     * Maximum number of tokens to parse in each example doc field that is not
     * stored with TermVector support.
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaximumNumberOfTokens(int $maximum): self
    {
        $this->setOption('maximumnumberoftokens', $maximum);
        return $this;
    }

    /**
     * Get maximumnumberoftokens option.
     *
     * @return int|null
     */
    public function getMaximumNumberOfTokens(): ?int
    {
        return $this->getOption('maximumnumberoftokens');
    }

    /**
     * Set boost option.
     *
     * If true the query will be boosted by the interesting term relevance.
     *
     * @param bool $boost
     *
     * @return self Provides fluent interface
     */
    public function setBoost(bool $boost): self
    {
        $this->setOption('boost', $boost);
        return $this;
    }

    /**
     * Get boost option.
     *
     * @return bool|null
     */
    public function getBoost(): ?bool
    {
        return $this->getOption('boost');
    }

    /**
     * Set queryfields option.
     *
     * Query fields and their boosts using the same format as that used in
     * DisMaxQParserPlugin. These fields must also be specified in fields.
     *
     * Separate multiple fields with commas if you use string input.
     *
     * @param string|array $queryFields
     *
     * @return self Provides fluent interface
     */
    public function setQueryFields($queryFields): self
    {
        if (is_string($queryFields)) {
            $queryFields = explode(',', $queryFields);
            $queryFields = array_map('trim', $queryFields);
        }

        $this->setOption('queryfields', $queryFields);
        return $this;
    }

    /**
     * Get queryfields option.
     *
     * @return array
     */
    public function getQueryFields(): array
    {
        $value = $this->getOption('queryfields');
        if (null === $value) {
            $value = [];
        }

        return $value;
    }
}
