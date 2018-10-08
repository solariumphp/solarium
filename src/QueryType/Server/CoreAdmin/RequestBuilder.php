<?php

namespace Solarium\QueryType\Server\CoreAdmin;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\AbstractAction;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

/**
 * Build an core admin request.
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * Build request for an update query.
     *
     * @param QueryInterface|CoreAdminQuery $query
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

    protected function addOptionsFromAction(AbstractAction $action, Request $request)
    {
        $options = ['action' => $action->getType()];
        $options += $action->getOptions();
        $request->addParams($options);
        return $request;
    }
}
