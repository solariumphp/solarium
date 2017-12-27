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
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\QueryType\MoreLikeThis;

use Solarium\Core\Client\Request;
use Solarium\QueryType\Select\RequestBuilder\RequestBuilder as SelectRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Build a MoreLikeThis request.
 */
class RequestBuilder extends SelectRequestBuilder
{
    /**
     * Build request for a MoreLikeThis query.
     *
     * @param QueryInterface|Query $query
     *
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = parent::build($query);

        // add mlt params to request
        $request->addParam('mlt.interestingTerms', $query->getInterestingTerms());
        $request->addParam('mlt.match.include', $query->getMatchInclude());
        $request->addParam('mlt.match.offset', $query->getMatchOffset());
        $request->addParam('mlt.fl', implode(',', $query->getMltFields()));
        $request->addParam('mlt.mintf', $query->getMinimumTermFrequency());
        $request->addParam('mlt.mindf', $query->getMinimumDocumentFrequency());
        $request->addParam('mlt.minwl', $query->getMinimumWordLength());
        $request->addParam('mlt.maxwl', $query->getMaximumWordLength());
        $request->addParam('mlt.maxqt', $query->getMaximumQueryTerms());
        $request->addParam('mlt.maxntp', $query->getMaximumNumberOfTokens());
        $request->addParam('mlt.boost', $query->getBoost());
        $request->addParam('mlt.qf', $query->getQueryFields());

        // convert query to stream if necessary
        if (true === $query->getQueryStream()) {
            $request->removeParam('q');
            $request->setRawData($query->getQuery());
            $request->setMethod(Request::METHOD_POST);
            $request->addHeader('Content-Type: text/plain; charset=utf-8');
        }

        return $request;
    }
}
