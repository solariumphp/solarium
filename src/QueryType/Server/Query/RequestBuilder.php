<?php

namespace Solarium\QueryType\Server\Query;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Server\AbstractServerQuery;
use Solarium\QueryType\Server\Query\Action\ActionInterface;

/**
 * Build an API request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for an API query.
     *
     * @param QueryInterface|AbstractServerQuery $query
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
     * @param Request         $request
     *
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
