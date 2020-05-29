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
use Solarium\Exception\HttpException;

final class Psr18Adapter implements AdapterInterface
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

        foreach ($this->getRequestHeaders($request, $endpoint) as $name => $values) {
            foreach ($values as $value) {
                $psr7Request = $psr7Request->withAddedHeader($name, $value);
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

    private function getRequestHeaders(Request $request, Endpoint $endpoint): array
    {
        $headers = [];

        foreach ($request->getHeaders() as $headerLine) {
            [$header, $value] = explode(':', $headerLine);
            if ($header = trim($header)) {
                $headers[$header][] = $value;
            }
        }

        if (!isset($headers['Content-Type'])) {
            $charset = $request->getParam('ie') ?? 'utf-8';

            if (Request::METHOD_GET == $request->getMethod()) {
                $headers['Content-Type'] = ['application/x-www-form-urlencoded; charset='.$charset];
            } else {
                $headers['Content-Type'] = ['application/xml; charset='.$charset];
            }
        }

        if (!isset($headers['Authorization'])) {
            // Try endpoint authentication first, fallback to request for backwards compatibility
            $authData = $endpoint->getAuthentication();
            if (empty($authData['username'])) {
                $authData = $request->getAuthentication();
            }

            if (!empty($authData['username']) && !empty($authData['password'])) {
                $headers['Authorization'] = ['Basic '.base64_encode($authData['username'].':'.$authData['password'])];
            }
        }

        return $headers;
    }
}
