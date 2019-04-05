<?php

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Configurable;
use Solarium\Exception\HttpException;

/**
 * Basic HTTP adapter using a stream.
 */
class Http extends Configurable implements AdapterInterface
{
    /**
     * Handle Solr communication.
     *
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @throws HttpException
     *
     * @return Response
     */
    public function execute(Request $request, Endpoint $endpoint): Response
    {
        $context = $this->createContext($request, $endpoint);
        $uri = AdapterHelper::buildUri($request, $endpoint);

        list($data, $headers) = $this->getData($uri, $context);

        $this->check($data, $headers);

        return new Response($data, $headers);
    }

    /**
     * Check result of a request.
     *
     *
     * @param string $data
     * @param array  $headers
     *
     * @throws HttpException
     */
    public function check($data, $headers)
    {
        // if there is no data and there are no headers it's a total failure,
        // a connection to the host was impossible.
        if (false === $data && 0 == count($headers)) {
            throw new HttpException('HTTP request failed');
        }
    }

    /**
     * Create a stream context for a request.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return resource
     */
    public function createContext($request, $endpoint)
    {
        $method = $request->getMethod();
        $context = stream_context_create(
            ['http' => [
                    'method' => $method,
                    'timeout' => $endpoint->getTimeout(),
                ],
            ]
        );

        // Try endpoint authentication first, fallback to request for backwards compatibility
        $authData = $endpoint->getAuthentication();
        if (empty($authData['username'])) {
            $authData = $request->getAuthentication();
        }

        if (!empty($authData['username']) && !empty($authData['password'])) {
            $request->addHeader(
                'Authorization: Basic '.base64_encode($authData['username'].':'.$authData['password'])
            );
        }

        if (Request::METHOD_POST == $method) {
            if ($request->getFileUpload()) {
                $data = AdapterHelper::buildUploadBodyFromRequest($request);

                $content_length = strlen($data);
                $request->addHeader("Content-Length: $content_length\r\n");
                stream_context_set_option(
                    $context,
                    'http',
                    'content',
                    $data
                );
            } else {
                $data = $request->getRawData();
                if (null !== $data) {
                    stream_context_set_option(
                        $context,
                        'http',
                        'content',
                        $data
                    );

                    $request->addHeader('Content-Type: text/xml; charset=UTF-8');
                }
            }
        } elseif (Request::METHOD_PUT == $method) {
            $data = $request->getRawData();
            if (null !== $data) {
                stream_context_set_option(
                    $context,
                    'http',
                    'content',
                    $data
                );
                $request->addHeader('Content-Type: application/json; charset=UTF-8');
            }
        }

        $headers = $request->getHeaders();
        if (count($headers) > 0) {
            stream_context_set_option(
                $context,
                'http',
                'header',
                implode("\r\n", $headers)
            );
        }

        return $context;
    }

    /**
     * Execute request.
     *
     * @param string   $uri
     * @param resource $context
     *
     * @return array
     */
    protected function getData($uri, $context)
    {
        $data = @file_get_contents($uri, false, $context);

        // @ see https://www.php.net/manual/en/reserved.variables.httpresponseheader.php
        return [$data, $http_response_header];
    }
}
