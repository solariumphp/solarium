<?php
/**
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
 * @copyright Copyright 2011 Gasol Wu <gasol.wu@gmail.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Result
 */

/**
 * MoreLikeThis query result
 *
 * This is the standard resulttype for a moreLikeThis query. Example usage:
 * <code>
 * // total solr mlt results
 * $result->getNumFound();
 *
 * // results fetched
 * count($result);
 *
 * // iterate over fetched mlt docs
 * foreach ($result AS $doc) {
 *    ....
 * }
 * </code>
 *
 * @package Solarium
 * @subpackage Result
 */
class Solarium_Result_MoreLikeThis extends Solarium_Result_Select
{
    /**
     * MLT interesting terms
     */
    protected $_interestingTerms;

    /**
     * MLT match document
     */
    protected $_match;

    /**
     * Get MLT interesting terms
     * 
     * this will show what "interesting" terms are used for the MoreLikeThis
     * query. These are the top tf/idf terms. NOTE: if you select 'details',
     * this shows you the term and boost used for each term. Unless
     * mlt.boost=true all terms will have boost=1.0
     *
     * @return array
     */
    public function getInterestingTerms()
    {
        $query = $this->getQuery();
        if ('none' == $query->getInterestingTerms()) {
            throw new Solarium_Exception('interestingterms is none');
        }
        $this->_parseResponse();
        return $this->_interestingTerms;
    }

    /**
     * Get matched document
     *
     * Only available if matchinclude was set to true in the query.
     *
     * @throws Solarium_Exception
     * @return Solarium_Document
     */
    public function getMatch()
    {
        $query = $this->getQuery();
        if (true != $query->getMatchInclude()) {
            throw new Solarium_Exception('matchinclude was disabled in the MLT query');
        }
        $this->_parseResponse();
        return $this->_match;
    }
}
