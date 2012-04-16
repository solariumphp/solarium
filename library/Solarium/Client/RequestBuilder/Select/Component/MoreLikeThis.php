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
 * @subpackage Client
 */

/**
 * Add select component morelikethis to the request
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_RequestBuilder_Select_Component_MoreLikeThis
{

    /**
     * Add request settings for morelikethis
     *
     * @param Solarium_Query_Select_Component_MoreLikeThis $component
     * @param Solarium_Client_Request $request
     * @return Solarium_Client_Request
     */
    public function buildComponent($component, $request)
    {
        // enable morelikethis
        $request->addParam('mlt', 'true');

        $request->addParam('mlt.fl', $component->getFields());
        $request->addParam('mlt.mintf', $component->getMinimumTermFrequency());
        $request->addParam('mlt.mindf', $component->getMinimumDocumentFrequency());
        $request->addParam('mlt.minwl', $component->getMinimumWordLength());
        $request->addParam('mlt.maxwl', $component->getMaximumWordLength());
        $request->addParam('mlt.maxqt', $component->getMaximumQueryTerms());
        $request->addParam('mlt.maxntp', $component->getMaximumNumberOfTokens());
        $request->addParam('mlt.boost', $component->getBoost());
        $request->addParam('mlt.qf', $component->getQueryFields());
        $request->addParam('mlt.count', $component->getCount());

        return $request;
    }
}