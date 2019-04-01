<?php

namespace Solarium\QueryType\ManagedResources\RequestBuilder;

use RuntimeException;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;

class Synonyms extends BaseRequestBuilder
{
    /**
     * Build request for a synonyms query.
     *
     * @param QueryInterface|SynonymsQuery $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        if (empty($query->getName())) {
            throw new \Solarium\Exception\RuntimeException('Name of the synonym resource is not set in the query.');
        }

        $request = parent::build($query);
        $request->setHandler($query->getHandler().$query->getName());
        if (null !== $query->getCommand()) {
            $request->addHeader('Content-Type: application/json; charset=utf-8');
            $this->buildCommand($request, $query->getCommand());
        } else {
            // Lists all synonyms.
            $request->setMethod(Request::METHOD_GET);
        }

        return $request;
    }

    /**
     * @param Request         $request
     * @param AbstractCommand $command
     *
     * @return self
     */
    protected function buildCommand(Request $request, AbstractCommand $command): self
    {
        $request->setMethod($command->getRequestMethod());

        switch ($command->getType()) {
            case SynonymsQuery::COMMAND_ADD:
                $request->setRawData($command->getRawData());
                break;
            case SynonymsQuery::COMMAND_DELETE:
                $request->setHandler($request->getHandler().'/'.$command->getTerm());
                break;
            case SynonymsQuery::COMMAND_EXISTS:
                $request->setHandler($request->getHandler().'/'.$command->getTerm());
                break;
            default:
                throw new RuntimeException('Unsupported command type');
                break;
        }

        $request->setMethod($command->getRequestMethod());

        return $this;
    }
}
