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
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Select\RequestBuilder\Component;

use Solarium\QueryType\Select\Query\Component\Spellcheck as SpellcheckComponent;
use Solarium\Core\Client\Request;

/**
 * Add select component Spellcheck to the request
 */
class Spellcheck implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for Spellcheck
     *
     * @param  SpellcheckComponent $component
     * @param  Request             $request
     * @return Request
     */
    public function buildComponent($component, $request)
    {
        // enable spellcheck
        $request->addParam('spellcheck', 'true');

        $request->addParam('spellcheck.q', $component->getQuery());
        $request->addParam('spellcheck.build', $component->getBuild());
        $request->addParam('spellcheck.reload', $component->getReload());
        $request->addParam('spellcheck.dictionary', $component->getDictionary());
        $request->addParam('spellcheck.count', $component->getCount());
        $request->addParam('spellcheck.onlyMorePopular', $component->getOnlyMorePopular());
        $request->addParam('spellcheck.extendedResults', $component->getExtendedResults());
        $request->addParam('spellcheck.collate', $component->getCollate());
        $request->addParam('spellcheck.maxCollations', $component->getMaxCollations());
        $request->addParam('spellcheck.maxCollationTries', $component->getMaxCollationTries());
        $request->addParam('spellcheck.maxCollationEvaluations', $component->getMaxCollationEvaluations());
        $request->addParam('spellcheck.collateExtendedResults', $component->getCollateExtendedResults());
        $request->addParam('spellcheck.accuracy', $component->getAccuracy());

        foreach ($component->getCollateParams() as $param => $value) {
            $request->addParam('spellcheck.collateParam.'.$param, $value);
        }

        return $request;
    }
}
