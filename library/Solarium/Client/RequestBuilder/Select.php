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
 * Build a select request
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_RequestBuilder_Select extends Solarium_Client_RequestBuilder
{

    /**
     * Build request for a select query
     *
     * @param Solarium_Query_Select $query
     * @return Solarium_Client_Request
     */
    public function build($query)
    {
        $request = parent::build($query);

        // add basic params to request
        $request->addParam('q', $query->getQuery());
        $request->addParam('start', $query->getStart());
        $request->addParam('rows', $query->getRows());
        $request->addParam('fl', implode(',', $query->getFields()));
        $request->addParam('q.op', $query->getQueryDefaultOperator());
        $request->addParam('df', $query->getQueryDefaultField());

        // add sort fields to request
        $sort = array();
        foreach ($query->getSorts() AS $field => $order) {
            $sort[] = $field . ' ' . $order;
        }
        if (count($sort) !== 0) {
            $request->addParam('sort', implode(',', $sort));
        }

        // add filterqueries to request
        $filterQueries = $query->getFilterQueries();
        if (count($filterQueries) !== 0) {
            foreach ($filterQueries AS $filterQuery) {
                $fq = $this->renderLocalParams(
                    $filterQuery->getQuery(),
                    array('tag' => $filterQuery->getTags())
                );
                $request->addParam('fq', $fq);
            }
        }

        // add components to request
        $types = $query->getComponentTypes();
        foreach ($query->getComponents() as $component) {
            $componentBuilderClass = $types[$component->getType()]['requestbuilder'];
            if (!empty($componentBuilderClass)) {
                $componentBuilder = new $componentBuilderClass;
                $request = $componentBuilder->buildComponent($component, $request);
            }
        }

        return $request;
    }

}
