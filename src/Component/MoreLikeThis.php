<?php

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\MoreLikeThis as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\MoreLikeThis as ResponseParser;

/**
 * MoreLikeThis component.
 *
 * @see http://wiki.apache.org/solr/MoreLikeThis
 */
class MoreLikeThis extends AbstractComponent
{
    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ?ComponentParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Set fields option.
     *
     * The fields to use for similarity. NOTE: if possible, these should have a
     * stored TermVector
     *
     * When using string input you can separate multiple fields with commas.
     *
     * @param string|array $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields): self
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        $this->setOption('fields', $fields);
        return $this;
    }

    /**
     * Get fields option.
     *
     * @return array
     */
    public function getFields(): array
    {
        $fields = $this->getOption('fields');
        if (null === $fields) {
            $fields = [];
        }

        return $fields;
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
     * When using string input you can separate multiple fields with commas.
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
        $queryfields = $this->getOption('queryfields');
        if (null === $queryfields) {
            $queryfields = [];
        }

        return $queryfields;
    }

    /**
     * Set count option.
     *
     * The number of similar documents to return for each result
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setCount(int $count): self
    {
        $this->setOption('count', $count);
        return $this;
    }

    /**
     * Get count option.
     *
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->getOption('count');
    }

    /**
     * Initialize options.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'fields':
                    $this->setFields($value);
                    break;
                case 'queryfields':
                    $this->setQueryFields($value);
                    break;
            }
        }
    }
}
