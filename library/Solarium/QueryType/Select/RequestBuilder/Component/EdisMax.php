<?php
/**
 * Copyright 2012 Marc Morera. All rights reserved.
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
 * @copyright Copyright 2012 Marc Morera <yuhu@mmoreram.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Select\RequestBuilder\Component;

use Solarium\QueryType\Select\Query\Component\Edismax as EdismaxComponent;
use Solarium\Core\Client\Request;

/**
 * Add select component edismax to the request
 *
 */
class EdisMax implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for EdismaxComponent
     *
     * @param  EdismaxComponent $component
     * @param  Request          $request
     * @return Request
     */
    public function buildComponent($component, $request)
    {
        // enable edismax
        $request->addParam('defType', $component->getQueryParser());

        $request->addParam('q.alt', $component->getQueryAlternative());
        $request->addParam('qf', $component->getQueryFields());
        $request->addParam('mm', $component->getMinimumMatch());
        $request->addParam('pf', $component->getPhraseFields());
        $request->addParam('ps', $component->getPhraseSlop());
        $request->addParam('pf2', $component->getPhraseBigramFields());
        $request->addParam('ps2', $component->getPhraseBigramSlop());
        $request->addParam('pf3', $component->getPhraseTrigramFields());
        $request->addParam('ps3', $component->getPhraseTrigramSlop());
        $request->addParam('qs', $component->getQueryPhraseSlop());
        $request->addParam('tie', $component->getTie());
        $request->addParam('bq', $component->getBoostQuery());
        $request->addParam('bf', $component->getBoostFunctions());
        $request->addParam('boost', $component->getBoostFunctionsMult());
        $request->addParam('uf', $component->getUserFields());

        return $request;
    }
}
