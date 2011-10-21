<?php
/**
 * Copyright 2011 Bas de Nooijer.
 * Copyright 2011 Gasol Wu. PIXNET Digital Media Corporation.
 * All rights reserved.
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
 * @copyright Copyright 2011 Gasol Wu <gasol.wu@gmail.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * MoreLikeThis Query
 *
 * Can be used to select documents and/or facets from Solr. This querytype has
 * lots of options and there are many Solarium subclasses for it.
 * See the Solr documentation and the relevant Solarium classes for more info.
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_MoreLikeThis extends Solarium_Query_Select
{

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType()
    {
        return Solarium_Client::QUERYTYPE_MORELIKETHIS;
    }
    
    /**
     * Default options
     * 
     * @var array
     */
    protected $_options = array(
        'handler'       => 'mlt',
        'resultclass'   => 'Solarium_Result_MoreLikeThis',
        'documentclass' => 'Solarium_Document_ReadOnly',
        'query'         => '*:*',
        'start'         => 0,
        'rows'          => 10,
        'fields'        => '*,score',
        'interestingTerms' => 'none',
        'matchinclude'  => false,
        'stream'        => false
    );

    /**
     * Set query stream option
     *
     * Set to true to post query content instead of using the URL param
     *
     * @link http://wiki.apache.org/solr/ContentStream ContentStream
     *
     * @param boolean $stream
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
     */
    public function setQueryStream($stream)
    {
        return $this->_setOption('stream', $stream);
    }

    /**
     * Get stream option
     *
     * @return boolean
     */
    public function getQueryStream()
    {
        return $this->getOption('stream');
    }

    /**
     * Set the interestingTerms parameter.  Must be one of: none, list, details.
     *
     * @see http://wiki.apache.org/solr/MoreLikeThisHandler#Params
     * @param string $term
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
     */
    public function setInterestingTerms($term)
    {
        return $this->_setOption('interestingTerms', $term);
    }
    
    /**
     * Get the interestingTerm parameter.
     *
     * @return string
     */
    public function getInterestingTerms() 
    {
        return $this->getOption('interestingTerms');
    }
    
    /**
     * Set the match.include parameter, which is either 'true' or 'false'.  
     * 
     * @see http://wiki.apache.org/solr/MoreLikeThisHandler#Params
     *
     * @param boolean $include
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
     */
    public function setMatchInclude($include)
    {
        return $this->_setOption('matchinclude', $include);
    }
    
    /**
     * Get the match.include parameter.
     *
     * @return string
     */
    public function getMatchInclude() 
    {
        return $this->getOption('matchinclude');
    }

    /**
     * Set MLT fields option
     *
     * The fields to use for similarity. NOTE: if possible, these should have a
     * stored TermVector
     *
     * Separate multiple fields with commas.
     *
     * @param string $fields
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
     */
    public function setMltFields($fields)
    {
        return $this->_setOption('mltfields', $fields);
    }

    /**
     * Get MLT fields option
     *
     * @return string|null
     */
    public function getMltFields()
    {
        return $this->getOption('mltfields');
    }

    /**
     * Set minimumtermfrequency option
     *
     * Minimum Term Frequency - the frequency below which terms will be ignored
     * in the source doc.
     *
     * @param int $minimum
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
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
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
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
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
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
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
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
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
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
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
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
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
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
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
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

}
