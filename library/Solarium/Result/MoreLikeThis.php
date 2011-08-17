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
 * @subpackage Result
 */

/**
 * MoreLikeThis query result
 *
 * This is the standard resulttype for a select query. Example usage:
 * <code>
 * // total solr results
 * $result->getNumFound();
 *
 * // results fetched
 * count($result);
 *
 * // iterate over fetched docs
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
     * this will show what "interesting" terms are used for the MoreLikeThis
     * query. These are the top tf/idf terms. NOTE: if you select 'details',
     * this shows you the term and boost used for each term. Unless
     * mlt.boost=true all terms will have boost=1.0
     *
     * This is NOT the number of document fetched from Solr!
     *
     * @var array
     */
    protected $_interestingTerms;

    public function getInterestingTerms()
    {
        $query = $this->getQuery();
        if ('none' == $query->getInterestingTerms()) {
            throw new Solarium_Exception('mlt.interestingTerms is none');
        }
        $this->_parseResponse();
        return $this->_interestingTerms;
    }
}
