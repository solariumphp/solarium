<?php

namespace Solarium\QueryType\Server\Collections;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Server\Collections\Query\Action\ActionInterface;
use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;

/**
 * Build a Collection API admin request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for a Collection API query.
     *
     * @param QueryInterface|CollectionsQuery $query
     *
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = parent::build($query);
        $request->setMethod(Request::METHOD_GET);
        $request = $this->addOptionsFromAction($query->getAction(), $request);
        return $request;
    }

    /**
     * @param ActionInterface $action
     * @param Request $request
     * @return Request
     */
    protected function addOptionsFromAction(ActionInterface $action, Request $request)
    {
        $options = ['action' => $action->getType()];
        $options = array_merge($options, $action->getOptions());
        $request->addParams($options);
        return $request;
    }
}
