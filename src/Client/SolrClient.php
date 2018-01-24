<?php

namespace Solarium\Client;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Psr\Http\Message\RequestInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SolrClient
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * SolrClient constructor.
     *
     * @param HttpClient     $httpClient
     * @param MessageFactory $messageFactory
     */
    public function __construct(HttpClient $httpClient, MessageFactory $messageFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->httpClient = $httpClient;
        $this->messageFactory = $messageFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    public function executeRequest(RequestInterface $request): ResponseParserInterface
    {
        $this->httpClient->sendRequest();
    }
}
