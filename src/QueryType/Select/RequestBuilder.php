<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Select;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

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
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);

        // add basic params to request
        $request->addParam(
            'q',
            $this->renderLocalParams(
                $query->getQuery(),
                $query->getLocalParameters()->getParameters()
            )
        );
        $request->addParam('start', $query->getStart());
        $request->addParam('rows', $query->getRows());
        $request->addParam('fl', implode(',', $query->getFields()));
        $request->addParam('q.op', $query->getQueryDefaultOperator());
        $request->addParam('df', $query->getQueryDefaultField());
        $request->addParam('cursorMark', $query->getCursorMark());
        $request->addParam('sow', $query->getSplitOnWhitespace());

        // add sort fields to request
        $sort = [];
        foreach ($query->getSorts() as $field => $order) {
            $sort[] = $field.' '.$order;
        }
        if (0 !== \count($sort)) {
            $request->addParam('sort', implode(',', $sort));
        }

        // add filterqueries to request
        $filterQueries = $query->getFilterQueries();
        if (0 !== \count($filterQueries)) {
            foreach ($filterQueries as $filterQuery) {
                $fq = $this->renderLocalParams(
                    $filterQuery->getQuery(),
                    $filterQuery->getLocalParameters()->getParameters()
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
