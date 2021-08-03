<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Configssets;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\Server\Configssets\Query\Action\Upload;
use Solarium\QueryType\Server\Query\RequestBuilder as ServerRequestBuilder;
use Solarium\QueryType\Server\Query\Action\ActionInterface;

/**
 * Build an Configsets API request.
 */
class RequestBuilder extends ServerRequestBuilder
{
    /**
     * Build request for an API query.
     *
     * @param \Solarium\Core\Query\AbstractQuery $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);

        $action = $query->getAction();
        if ($action instanceof Upload) {
            $request->setMethod(Request::METHOD_POST);
            $request->setFileUpload($action->getFile());
        }

        return $request;
    }
}
