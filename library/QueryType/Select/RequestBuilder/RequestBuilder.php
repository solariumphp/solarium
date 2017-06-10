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
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\QueryType\Select\RequestBuilder;

use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;

/**
 * Build a select request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for a select query.
     *
     * @param QueryInterface|SelectQuery $query
     *
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = parent::build($query);

        // add basic params to request
        $request->addParam(
            'q',
            $this->renderLocalParams(
                $query->getQuery(),
                array('tag' => $query->getTags())
            )
        );
        $request->addParam('start', $query->getStart());
        $request->addParam('rows', $query->getRows());
        $request->addParam('fl', implode(',', $query->getFields()));
        $request->addParam('q.op', $query->getQueryDefaultOperator());
        $request->addParam('df', $query->getQueryDefaultField());

        // add sort fields to request
        $sort = array();
        foreach ($query->getSorts() as $field => $order) {
            $sort[] = $field.' '.$order;
        }
        if (count($sort) !== 0) {
            $request->addParam('sort', implode(',', $sort));
        }

        // add filterqueries to request
        $filterQueries = $query->getFilterQueries();
        if (count($filterQueries) !== 0) {
            foreach ($filterQueries as $filterQuery) {
                $fq = $this->renderLocalParams(
                    $filterQuery->getQuery(),
                    array('tag' => $filterQuery->getTags())
                );
                $request->addParam('fq', $fq);
            }
        }

        // add components to request
        foreach ($query->getComponents() as $component) {
            $componentBuilder = $component->getRequestBuilder();
            if ($componentBuilder) {
                $request = $componentBuilder->buildComponent($component, $request);
            }
        }

        return $request;
    }
}
