<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as BaseQuery;

/**
 * Resource.
 */
class Resource extends AbstractRequestBuilder
{
    /**
     * Build request for a resource query.
     *
     * @param \Solarium\Core\Query\AbstractQuery $query
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return \Solarium\Core\Client\Request
     */
    public function build(AbstractQuery $query): Request
    {
        if (empty($query->getName())) {
            throw new RuntimeException('Name of the resource is not set in the query.');
        }

        $request = parent::build($query);
        $request->setHandler($query->getHandler().$query->getName());
        if (null !== $query->getCommand()) {
            $request->addHeader('Content-Type: application/json; charset=utf-8');
            $this->buildCommand($request, $query->getCommand());
        } else {
            // Lists all items.
            $request->setMethod(Request::METHOD_GET);
        }

        return $request;
    }

    /**
     * @param \Solarium\Core\Client\Request                              $request
     * @param \Solarium\QueryType\ManagedResources\Query\AbstractCommand $command
     *
     * @return self
     */
    protected function buildCommand(Request $request, AbstractCommand $command): self
    {
        $request->setMethod($command->getRequestMethod());

        switch ($command->getType()) {
            case BaseQuery::COMMAND_ADD:
                $request->setRawData($command->getRawData());
                break;
            case BaseQuery::COMMAND_CONFIG:
                $request->setRawData($command->getRawData());
                break;
            case BaseQuery::COMMAND_CREATE:
                $request->setRawData($command->getRawData());
                break;
            case BaseQuery::COMMAND_DELETE:
                $request->setHandler($request->getHandler().'/'.$command->getTerm());
                break;
            case BaseQuery::COMMAND_EXISTS:
                $request->setHandler($request->getHandler().'/'.$command->getTerm());
                break;
            case BaseQuery::COMMAND_REMOVE:
                break;
            default:
                throw new RuntimeException(sprintf('Unsupported command type: %s', $command->getType()));
        }

        return $this;
    }
}
