<?php

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\MoreLikeThis as RequestBuilder;
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
    public function getType()
    {
        return ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS;
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
    public function setFields($fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        return $this->setOption('fields', $fields);
    }

    /**
     * Get fields option.
     *
     * @return array
     */
    public function getFields()
    {
        $fields = $this->getOption('fields');
        if (null === $fields) {
            $fields = [];
        }

        return $fields;
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
    public function setMinimumTermFrequency($minimum)
    {
        return $this->setOption('minimumtermfrequency', $minimum);
    }

    /**
     * Get minimumtermfrequency option.
     *
     * @return int|null
     */
    public function getMinimumTermFrequency()
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
    public function setMinimumDocumentFrequency($minimum)
    {
        return $this->setOption('minimumdocumentfrequency', $minimum);
    }

    /**
     * Get minimumdocumentfrequency option.
     *
     * @return int|null
     */
    public function getMinimumDocumentFrequency()
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
    public function setMinimumWordLength($minimum)
    {
        return $this->setOption('minimumwordlength', $minimum);
    }

    /**
     * Get minimumwordlength option.
     *
     * @return int|null
     */
    public function getMinimumWordLength()
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
    public function setMaximumWordLength($maximum)
    {
        return $this->setOption('maximumwordlength', $maximum);
    }

    /**
     * Get maximumwordlength option.
     *
     * @return int|null
     */
    public function getMaximumWordLength()
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
    public function setMaximumQueryTerms($maximum)
    {
        return $this->setOption('maximumqueryterms', $maximum);
    }

    /**
     * Get maximumqueryterms option.
     *
     * @return int|null
     */
    public function getMaximumQueryTerms()
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
    public function setMaximumNumberOfTokens($maximum)
    {
        return $this->setOption('maximumnumberoftokens', $maximum);
    }

    /**
     * Get maximumnumberoftokens option.
     *
     * @return int|null
     */
    public function getMaximumNumberOfTokens()
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
    public function setBoost($boost)
    {
        return $this->setOption('boost', $boost);
    }

    /**
     * Get boost option.
     *
     * @return bool|null
     */
    public function getBoost()
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
     * @param string $queryFields
     *
     * @return self Provides fluent interface
     */
    public function setQueryFields($queryFields)
    {
        if (is_string($queryFields)) {
            $queryFields = explode(',', $queryFields);
            $queryFields = array_map('trim', $queryFields);
        }

        return $this->setOption('queryfields', $queryFields);
    }

    /**
     * Get queryfields option.
     *
     * @return array
     */
    public function getQueryFields()
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
    public function setCount($count)
    {
        return $this->setOption('count', $count);
    }

    /**
     * Get count option.
     *
     * @return int|null
     */
    public function getCount()
    {
        return $this->getOption('count');
    }
}
