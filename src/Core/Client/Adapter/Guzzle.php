<?php

namespace Solarium\Core\Client\Adapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Configurable;
use Solarium\Exception\HttpException;

/**
 * Guzzle HTTP adapter.
 */
class Guzzle extends Configurable implements AdapterInterface
{
    /**
     * The Guzzle HTTP client instance.
     *
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     * Execute a Solr request using the cURL Http.
     *
     * @param Request  $request  the incoming Solr request
     * @param Endpoint $endpoint the configured Solr endpoint
     *
     * @throws HttpException thrown if solr request connot be made
     * @throws
     *
     * @return Response
     *
     * @codingStandardsIgnoreStart AdapterInterface does not declare type-hints
     */
    public function execute(Request $request, Endpoint $endpoint): Response
    {
        //@codingStandardsIgnoreEnd
        $requestOptions = [
            RequestOptions::HEADERS => $this->getRequestHeaders($request),
            RequestOptions::BODY => $this->getRequestBody($request),
            RequestOptions::TIMEOUT => $endpoint->getTimeout(),
            RequestOptions::CONNECT_TIMEOUT => $endpoint->getTimeout(),
        ];

        // Try endpoint authentication first, fallback to request for backwards compatibility
        $authData = $endpoint->getAuthentication();
        if (empty($authData['username'])) {
            $authData = $request->getAuthentication();
        }

        if (!empty($authData['username']) && !empty($authData['password'])) {
            $requestOptions[RequestOptions::AUTH] = [$authData['username'], $authData['password']];
        }

        try {
            $uri = AdapterHelper::buildUri($request, $endpoint);

            $guzzleResponse = $this->getGuzzleClient()->request(
                $request->getMethod(),
                $uri,
                $requestOptions
            );

            $responseHeaders = [
                "HTTP/{$guzzleResponse->getProtocolVersion()} {$guzzleResponse->getStatusCode()} "
                .$guzzleResponse->getReasonPhrase(),
            ];

            foreach ($guzzleResponse->getHeaders() as $key => $value) {
                $responseHeaders[] = "{$key}: ".implode(', ', $value);
            }

            return new Response((string) $guzzleResponse->getBody(), $responseHeaders);
        } catch (GuzzleException $e) {
            $error = $e->getMessage();
            throw new HttpException("HTTP request failed, {$error}");
        }
    }

    /**
     * Gets the Guzzle HTTP client instance.
     *
     * @return GuzzleClient
     */
    public function getGuzzleClient()
    {
        if (null === $this->guzzleClient) {
            $this->guzzleClient = new GuzzleClient($this->options);
        }

        return $this->guzzleClient;
    }

    /**
     * Helper method to create a request body suitable for a guzzle 3 request.
     *
     * @param Request $request the incoming solarium request
     *
     * @return null|resource|string
     */
    private function getRequestBody(Request $request)
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

    /**
     * Helper method to extract headers from the incoming solarium request and put them in a format
     * suitable for a guzzle 3 request.
     *
     * @param Request $request the incoming solarium request
     *
     * @return array
     */
    private function getRequestHeaders(Request $request)
    {
        $headers = [];
        foreach ($request->getHeaders() as $headerLine) {
            list($header, $value) = explode(':', $headerLine);
            if ($header = trim($header)) {
                $headers[$header] = trim($value);
            }
        }

        if (!isset($headers['Content-Type'])) {
            if (Request::METHOD_GET == $request->getMethod()) {
                $headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
            } else {
                $headers['Content-Type'] = 'application/xml; charset=utf-8';
            }
        }

        return $headers;
    }
}
