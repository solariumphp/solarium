<?php

namespace Solarium\QueryType\ManagedResources\RequestBuilder;

use RuntimeException;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder as BaseRequestBuilder;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\ManagedResources\Query\Stopwords as StopwordsQuery;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\AbstractCommand;

class Stopwords extends BaseRequestBuilder {
    
    /**
     * Build request for a stopwords query.
     *
     * @param QueryInterface|StopwordsQuery $query
     *
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        if (empty($query->getName())) {
            throw new \Solarium\Exception\RuntimeException("Name of the resource is not set in the query.");
        }

        $request = parent::build($query);
        if ($query->getCommand() !== null) {
            $request->setHandler($query->getHandler().$query->getName());
            $this->buildCommand($request, $query->getCommand());
        }
        else {
            // Lists all stopwords.
            $request->setMethod(Request::METHOD_GET);
            $request->setHandler($query->getHandler().$query->getName());
        }

        return $request;
    }

    protected function buildCommand(Request $request, AbstractCommand $command)
    {
        $request->setMethod($command->getRequestMethod());

        switch ($command->getType()) {
            case StopwordsQuery::COMMAND_ADD:
                $request->setRawData($command->getRawData());
                break;
            case StopwordsQuery::COMMAND_DELETE:
                $request->setHandler($request->getHandler().'/'.$command->getTerm());
                break;
            case StopwordsQuery::COMMAND_EXISTS:
                $request->setHandler($request->getHandler().'/'.$command->getTerm());
                break;
            default:
                throw new RuntimeException('Unsupported command type');
                break;
        }

        $request->setMethod($command->getRequestMethod());
    }
}