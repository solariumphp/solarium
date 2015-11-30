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
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\RequestBuilder\Component\MoreLikeThis as RequestBuilder;
use Solarium\QueryType\Select\ResponseParser\Component\MoreLikeThis as ResponseParser;

/**
 * MoreLikeThis component.
 *
 * @link http://wiki.apache.org/solr/MoreLikeThis
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
        return SelectQuery::COMPONENT_MORELIKETHIS;
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
        if ($fields === null) {
            $fields = array();
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
     * @return integer|null
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
     * @return integer|null
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
     * @return integer|null
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
     * @return integer|null
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
     * @return integer|null
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
     * @return integer|null
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
     * @param boolean $boost
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
     * @return boolean|null
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
        if ($queryfields === null) {
            $queryfields = array();
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
