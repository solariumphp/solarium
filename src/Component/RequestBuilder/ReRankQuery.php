<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\ReRankQuery as ReRankQueryComponent;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

/**
 * Add select component spatial to the request.
 */
class ReRankQuery implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for ReRankQuery.
     *
     * @param ReRankQueryComponent $component
     * @param Request              $request
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        $subRequest = new SubRequest();
        $subRequest->addParam('reRankQuery', $component->getQuery());
        $subRequest->addParam('reRankDocs', $component->getDocs());
        $subRequest->addParam('reRankWeight', $component->getWeight());

        $request->addParam('rq', $subRequest->getSubQuery());

        return $request;
    }
}
