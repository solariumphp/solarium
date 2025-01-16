<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Configsets;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Server\Configsets\Query\Action\Upload;
use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Query\RequestBuilder as ServerRequestBuilder;

/**
 * Build a Configsets API request.
 */
class RequestBuilder extends ServerRequestBuilder
{
    /**
     * Build request for an API query.
     *
     * @param QueryInterface|ConfigsetsQuery $query
     *
     * @return Request
     */
    public function build(QueryInterface|ConfigsetsQuery $query): Request
    {
        $request = parent::build($query);

        $action = $query->getAction();
        if ($action instanceof Upload) {
            $request->setMethod(Request::METHOD_POST);
            $request->setFileUpload($action->getFile());
            $request->setContentType(Request::CONTENT_TYPE_MULTIPART_FORM_DATA, ['boundary' => $request->getHash()]);
        }

        return $request;
    }
}
