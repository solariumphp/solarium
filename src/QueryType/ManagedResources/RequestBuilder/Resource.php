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
        // reserved characters in a REST resource name need to be encoded twice to make it through the servlet (SOLR-6853)
        $request->setHandler($query->getHandler().rawurlencode(rawurlencode($query->getName())));
        if (null !== $query->getCommand()) {
            $request->setContentType(Request::CONTENT_TYPE_APPLICATION_JSON);
            $this->buildCommand($request, $query->getCommand());
        } else {
            // Lists one or all items.
            $request->setMethod(Request::METHOD_GET);

            if (null !== $term = $query->getTerm()) {
                // reserved characters in a REST resource name need to be encoded twice to make it through the servlet (SOLR-6853)
                $request->setHandler($request->getHandler().'/'.rawurlencode(rawurlencode($term)));
            }
        }

        return $request;
    }

    /**
     * @param \Solarium\Core\Client\Request                              $request
     * @param \Solarium\QueryType\ManagedResources\Query\AbstractCommand $command
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return self
     */
    protected function buildCommand(Request $request, AbstractCommand $command): self
    {
        $request->setMethod($command->getRequestMethod());

        switch ($command->getType()) {
            case BaseQuery::COMMAND_ADD:
                if (null === $rawData = $command->getRawData()) {
                    throw new RuntimeException('Missing data for ADD command.');
                }
                $request->setRawData($rawData);
                break;
            case BaseQuery::COMMAND_CONFIG:
                if (null === $rawData = $command->getRawData()) {
                    throw new RuntimeException('Missing initArgs for CONFIG command.');
                }
                $request->setRawData($rawData);
                break;
            case BaseQuery::COMMAND_CREATE:
                if (null === $rawData = $command->getRawData()) {
                    throw new RuntimeException('Missing class for CREATE command.');
                }
                $request->setRawData($rawData);
                break;
            case BaseQuery::COMMAND_DELETE:
                if (null === $term = $command->getTerm()) {
                    throw new RuntimeException('Missing term for DELETE command.');
                }
                // reserved characters in a REST resource name need to be encoded twice to make it through the servlet (SOLR-6853)
                $request->setHandler($request->getHandler().'/'.rawurlencode(rawurlencode($command->getTerm())));
                break;
            case BaseQuery::COMMAND_EXISTS:
                if (null !== $term = $command->getTerm()) {
                    // reserved characters in a REST resource name need to be encoded twice to make it through the servlet (SOLR-6853)
                    $request->setHandler($request->getHandler().'/'.rawurlencode(rawurlencode($command->getTerm())));
                }
                break;
            case BaseQuery::COMMAND_REMOVE:
                break;
            default:
                throw new RuntimeException(sprintf('Unsupported command type: %s', $command->getType()));
        }

        return $this;
    }
}
