<?php

namespace Solarium\Client;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;

final class SolrClientBuilder
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct(string $endpoint)
    {
        // $this->httpClient = $httpClient; // ?: HttpClientDiscovery::find();
        // $this->messageFactory = $requestFactory; // ?: MessageFactoryDiscovery::find();
    }

    public static function create(): self
    {
        return new self('');
    }

    public function addEndpoint(string $url)
    {
    }

    public function addHttpEndpoint(HttpClient $endpointHttpClient)
    {
    }

    public function build(): SolrClient
    {
        if (!$this->httpClient) {
            $this->httpClient = HttpClientDiscovery::find();
        }

        if (!$this->messageFactory) {
            $this->messageFactory = MessageFactoryDiscovery::find();
        }

        return new SolrClient($this->httpClient, $this->messageFactory);
    }
}
