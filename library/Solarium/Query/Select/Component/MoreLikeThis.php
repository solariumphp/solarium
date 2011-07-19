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
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * MoreLikeThis component
 *
 * @link http://wiki.apache.org/solr/MoreLikeThis
 * 
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select_Component_MoreLikeThis extends Solarium_Query_Select_Component
{

    /**
     * Component type
     * 
     * @var string
     */
    protected $_type = Solarium_Query_Select::COMPONENT_MORELIKETHIS;

    /**
     * Set fields option
     *
     * The fields to use for similarity. NOTE: if possible, these should have a
     * stored TermVector
     *
     * Separate multiple fields with commas.
     *
     * @param string $fields
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setFields($fields)
    {
        return $this->_setOption('fields', $fields);
    }

    /**
     * Get fields option
     *
     * @return string|null
     */
    public function getFields()
    {
        return $this->getOption('fields');
    }

    /**
     * Set minimumtermfrequency option
     *
     * Minimum Term Frequency - the frequency below which terms will be ignored
     * in the source doc.
     *
     * @param int $minimum
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setMinimumTermFrequency($minimum)
    {
        return $this->_setOption('minimumtermfrequency', $minimum);
    }

    /**
     * Get minimumtermfrequency option
     *
     * @return integer|null
     */
    public function getMinimumTermFrequency()
    {
        return $this->getOption('minimumtermfrequency');
    }

    /**
     * Set minimumdocumentfrequency option
     *
     * Minimum Document Frequency - the frequency at which words will be
     * ignored which do not occur in at least this many docs.
     *
     * @param int $minimum
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setMinimumDocumentFrequency($minimum)
    {
        return $this->_setOption('minimumdocumentfrequency', $minimum);
    }

    /**
     * Get minimumdocumentfrequency option
     *
     * @return integer|null
     */
    public function getMinimumDocumentFrequency()
    {
        return $this->getOption('minimumdocumentfrequency');
    }

    /**
     * Set minimumwordlength option
     *
     * Minimum word length below which words will be ignored.
     *
     * @param int $minimum
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setMinimumWordLength($minimum)
    {
        return $this->_setOption('minimumwordlength', $minimum);
    }

    /**
     * Get minimumwordlength option
     *
     * @return integer|null
     */
    public function getMinimumWordLength()
    {
        return $this->getOption('minimumwordlength');
    }

    /**
     * Set maximumwordlength option
     *
     * Maximum word length above which words will be ignored.
     *
     * @param int $maximum
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setMaximumWordLength($maximum)
    {
        return $this->_setOption('maximumwordlength', $maximum);
    }

    /**
     * Get maximumwordlength option
     *
     * @return integer|null
     */
    public function getMaximumWordLength()
    {
        return $this->getOption('maximumwordlength');
    }

    /**
     * Set maximumqueryterms option
     *
     * Maximum number of query terms that will be included in any generated
     * query.
     *
     * @param int $maximum
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setMaximumQueryTerms($maximum)
    {
        return $this->_setOption('maximumqueryterms', $maximum);
    }

    /**
     * Get maximumqueryterms option
     *
     * @return integer|null
     */
    public function getMaximumQueryTerms()
    {
        return $this->getOption('maximumqueryterms');
    }

    /**
     * Set maximumnumberoftokens option
     *
     * Maximum number of tokens to parse in each example doc field that is not
     * stored with TermVector support.
     *
     * @param int $maximum
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setMaximumNumberOfTokens($maximum)
    {
        return $this->_setOption('maximumnumberoftokens', $maximum);
    }

    /**
     * Get maximumnumberoftokens option
     *
     * @return integer|null
     */
    public function getMaximumNumberOfTokens()
    {
        return $this->getOption('maximumnumberoftokens');
    }

    /**
     * Set boost option
     *
     * If true the query will be boosted by the interesting term relevance.
     *
     * @param boolean $boost
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setBoost($boost)
    {
        return $this->_setOption('boost', $boost);
    }

    /**
     * Get boost option
     *
     * @return boolean|null
     */
    public function getBoost()
    {
        return $this->getOption('boost');
    }

    /**
     * Set queryfields option
     *
     * Query fields and their boosts using the same format as that used in
     * DisMaxQParserPlugin. These fields must also be specified in fields.
     *
     * Separate multiple fields with commas.
     *
     * @param string $queryFields
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setQueryFields($queryFields)
    {
        return $this->_setOption('queryfields', $queryFields);
    }

    /**
     * Get queryfields option
     *
     * @return string|null
     */
    public function getQueryFields()
    {
        return $this->getOption('queryfields');
    }

    /**
     * Set count option
     *
     * The number of similar documents to return for each result
     *
     * @param int $count
     * @return Solarium_Query_Select_Component_MoreLikeThis Provides fluent interface
     */
    public function setCount($count)
    {
        return $this->_setOption('count', $count);
    }

    /**
     * Get count option
     *
     * @return int|null
     */
    public function getCount()
    {
        return $this->getOption('count');
    }

}