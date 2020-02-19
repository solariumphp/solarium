<?php

namespace Solarium\Core\Client\Adapter;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Configurable;
use Solarium\Exception\HttpException;

class Psr18Adapter extends Configurable implements AdapterInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        parent::__construct();
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    public function execute(Request $request, Endpoint $endpoint): Response
    {
        try {
            return $this->createResponse($this->httpClient->sendRequest($this->createPsr7Request($request, $endpoint)));
        } catch (ClientExceptionInterface $clientException) {
            throw new HttpException("HTTP request failed, {$clientException}");
        }
    }

    private function createPsr7Request(Request $request, Endpoint $endpoint): RequestInterface
    {
        $uri = AdapterHelper::buildUri($request, $endpoint);

        $psr7Request = $this->requestFactory->createRequest(
            $request->getMethod(),
            $uri
        );

        if (null !== $body = $this->getRequestBody($request)) {
            $psr7Request = $psr7Request->withBody($this->streamFactory->createStream($body));
        }

        foreach ($request->getHeaders() as $headerLine) {
            [$header, $value] = explode(':', $headerLine);
            if ($header = trim($header)) {
                $psr7Request = $psr7Request->withAddedHeader($header, $value);
            }
        }

        return $psr7Request;
    }

    private function createResponse(ResponseInterface $psr7Response): Response
    {
        $headerLines = [
            sprintf(
                'HTTP/%s %s %s',
                $psr7Response->getProtocolVersion(),
                $psr7Response->getStatusCode(),
                $psr7Response->getReasonPhrase()
            ),
        ];

        foreach ($psr7Response->getHeaders() as $name => $values) {
            $headerLines[] = sprintf('%s: %s', $name, implode(', ', $values));
        }

        return new Response((string) $psr7Response->getBody(), $headerLines);
    }

    private function getRequestBody(Request $request): ?string
    {
        if (Request::METHOD_PUT == $request->getMethod()) {
            return $request->getRawData();
        }

        if (Request::METHOD_POST !== $request->getMethod()) {
            return null;
        }

        if ($request->getFileUpload()) {
            return AdapterHelper::buildUploadBodyFromRequest($request);
        }

        return $request->getRawData();
    }
}
