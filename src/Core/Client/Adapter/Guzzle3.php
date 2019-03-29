<?php

namespace Solarium\Core\Client\Adapter;

use Guzzle\Http\Client as GuzzleClient;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Configurable;
use Solarium\Exception\HttpException;

/**
 * Guzzle3 HTTP adapter.
 */
class Guzzle3 extends Configurable implements AdapterInterface
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
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return Response
     */
    public function execute(Request $request, Endpoint $endpoint): Response
    {
        // @codeCoverageIgnoreStart
        $uri = AdapterHelper::buildUri($request, $endpoint);
        $guzzleRequest = $this->getGuzzleClient()->createRequest(
            $request->getMethod(),
            $uri,
            $this->getRequestHeaders($request),
            $this->getRequestBody($request),
            [
                'timeout' => $endpoint->getTimeout(),
                'connect_timeout' => $endpoint->getTimeout(),
            ]
        );

        // Try endpoint authentication first, fallback to request for backwards compatibility
        $authData = $endpoint->getAuthentication();
        if (empty($authData['username'])) {
            $authData = $request->getAuthentication();
        }

        if (!empty($authData['username']) && !empty($authData['password'])) {
            $guzzleRequest->setAuth($authData['username'], $authData['password']);
        }

        try {
            $this->getGuzzleClient()->send($guzzleRequest);

            $guzzleResponse = $guzzleRequest->getResponse();

            $responseHeaders = array_merge(
                ["HTTP/1.1 {$guzzleResponse->getStatusCode()} {$guzzleResponse->getReasonPhrase()}"],
                $guzzleResponse->getHeaderLines()
            );

            return new Response($guzzleResponse->getBody(true), $responseHeaders);
        } catch (\Guzzle\Http\Exception\RequestException $e) {
            $error = $e->getMessage();
            if ($e instanceof \Guzzle\Http\Exception\CurlException) {
                $error = $e->getError();
            }

            throw new HttpException("HTTP request failed, {$error}");
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Gets the Guzzle HTTP client instance.
     *
     * @return GuzzleClient
     */
    public function getGuzzleClient()
    {
        // @codeCoverageIgnoreStart
        if (null === $this->guzzleClient) {
            $this->guzzleClient = new GuzzleClient(null, $this->options);
        }

        return $this->guzzleClient;
        // @codeCoverageIgnoreEnd
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
        // @codeCoverageIgnoreStart
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
        // @codeCoverageIgnoreEnd
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
        // @codeCoverageIgnoreStart
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
        // @codeCoverageIgnoreEnd
    }
}
