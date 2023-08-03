<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
        $subRequest->addParam('reRankQuery', '$rqq');
        $subRequest->addParam('reRankDocs', $component->getDocs());
        $subRequest->addParam('reRankWeight', $component->getWeight());
        $subRequest->addParam('reRankScale', $component->getScale());
        $subRequest->addParam('reRankMainScale', $component->getMainScale());
        $subRequest->addParam('reRankOperator', $component->getOperator());

        $request->addParam('rq', $subRequest->getSubQuery());
        $request->addParam('rqq', $component->getQuery());

        return $request;
    }
}
