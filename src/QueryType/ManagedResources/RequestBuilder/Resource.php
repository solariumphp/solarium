<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as BaseQuery;
use Solarium\QueryType\ManagedResources\Query\Command\AbstractAdd as Add;
use Solarium\QueryType\ManagedResources\Query\Command\AbstractCreate as Create;
use Solarium\QueryType\ManagedResources\Query\Command\Config;
use Solarium\QueryType\ManagedResources\Query\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Command\Exists;
use Solarium\QueryType\ManagedResources\Query\Command\Remove;

/**
 * Resource.
 */
class Resource extends AbstractRequestBuilder
{
    /**
     * Build request for a resource query.
     *
     * @param QueryInterface&BaseQuery $query
     *
     * @throws RuntimeException
     *
     * @return Request
     */
    public function build(QueryInterface|BaseQuery $query): Request
    {
        if (empty($query->getName())) {
            throw new RuntimeException('Name of the resource is not set in the query.');
        }

        $name = rawurlencode($query->getName());
        if ($query->getUseDoubleEncoding()) {
            $name = rawurlencode($name);
        }

        $request = parent::build($query);
        $request->setHandler($query->getHandler().$name);

        if (null !== $query->getCommand()) {
            $request->setContentType(Request::CONTENT_TYPE_APPLICATION_JSON);
            $this->buildCommand($query, $request);
        } else {
            // Lists one or all items.
            $request->setMethod(Request::METHOD_GET);

            if (null !== $term = $query->getTerm()) {
                $term = rawurlencode($term);
                if ($query->getUseDoubleEncoding()) {
                    $term = rawurlencode($term);
                }

                $request->setHandler($request->getHandler().'/'.$term);
            }
        }

        return $request;
    }

    /**
     * @param QueryInterface&BaseQuery $query
     * @param Request                  $request
     *
     * @throws RuntimeException
     *
     * @return self Provides fluent interface
     */
    protected function buildCommand(QueryInterface|BaseQuery $query, Request $request): self
    {
        $command = $query->getCommand();

        $request->setMethod($command->getRequestMethod());

        switch ($command->getType()) {
            case BaseQuery::COMMAND_ADD:
                assert($command instanceof Add);

                if (null === $rawData = $command->getRawData()) {
                    throw new RuntimeException('Missing data for ADD command.');
                }

                $request->setRawData($rawData);
                break;
            case BaseQuery::COMMAND_CONFIG:
                assert($command instanceof Config);

                if (null === $rawData = $command->getRawData()) {
                    throw new RuntimeException('Missing initArgs for CONFIG command.');
                }

                $request->setRawData($rawData);
                break;
            case BaseQuery::COMMAND_CREATE:
                assert($command instanceof Create);

                if (null === $rawData = $command->getRawData()) {
                    throw new RuntimeException('Missing class for CREATE command.');
                }

                $request->setRawData($rawData);
                break;
            case BaseQuery::COMMAND_DELETE:
                assert($command instanceof Delete);

                if (null === $term = $command->getTerm()) {
                    throw new RuntimeException('Missing term for DELETE command.');
                }

                $term = rawurlencode($term);
                if ($query->getUseDoubleEncoding()) {
                    $term = rawurlencode($term);
                }

                $request->setHandler($request->getHandler().'/'.$term);
                break;
            case BaseQuery::COMMAND_EXISTS:
                assert($command instanceof Exists);

                if (null !== $term = $command->getTerm()) {
                    $term = rawurlencode($term);
                    if ($query->getUseDoubleEncoding()) {
                        $term = rawurlencode($term);
                    }

                    $request->setHandler($request->getHandler().'/'.$term);
                }

                break;
            case BaseQuery::COMMAND_REMOVE:
                assert($command instanceof Remove);

                break;
            default:
                throw new RuntimeException(sprintf('Unsupported command type: %s', $command->getType()));
        }

        return $this;
    }
}
