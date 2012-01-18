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
 * Add select component Grouping to the request
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_RequestBuilder_Select_Component_Grouping
{

    /**
     * Add request settings for Grouping
     *
     * @param Solarium_Query_Select_Component_Grouping $component
     * @param Solarium_Client_Request $request
     * @return Solarium_Client_Request
     */
    public function buildComponent($component, $request)
    {
        // enable grouping
        $request->addParam('group', 'true');

        $request->addParam('group.field', $component->getFields());
        $request->addParam('group.query', $component->getQueries());
        $request->addParam('group.limit', $component->getLimit());
        $request->addParam('group.offset', $component->getOffset());
        $request->addParam('group.sort', $component->getSort());
        $request->addParam('group.main', $component->getMainResult());
        $request->addParam('group.ngroups', $component->getNumberOfGroups());
        $request->addParam('group.cache.percent', $component->getCachePercentage());
        $request->addParam('group.truncate', $component->getTruncate());

        return $request;
    }
}