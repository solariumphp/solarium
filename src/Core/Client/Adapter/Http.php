<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Exception\HttpException;

/**
 * Basic HTTP adapter using a stream.
 */
class Http implements AdapterInterface, TimeoutAwareInterface, ProxyAwareInterface
{
    use TimeoutAwareTrait;
    use ProxyAwareTrait;

    /**
     * Handle Solr communication.
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

        // if false instead of response data it's a total failure
        if (false === $data) {
            throw new HttpException('HTTP request failed');
        }

        return new Response($data, $headers);
    }

    /**
     * Create a stream context for a request.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return resource
     */
    public function createContext(Request $request, Endpoint $endpoint)
    {
        $method = $request->getMethod();

        $httpOptions = [
            'method' => $method,
            'timeout' => $this->timeout,
            'protocol_version' => 1.0,
            'user_agent' => 'Solarium Http Adapter',
            'ignore_errors' => true,
        ];

        if (null !== $this->proxy) {
            $httpOptions['proxy'] = $this->proxy;
            $httpOptions['request_fulluri'] = true;
        }

        $context = stream_context_create(
            [
                'http' => $httpOptions,
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
        } else {
            // According to the specification, only one Authorization header is allowed.
            // @see https://stackoverflow.com/questions/29282578/multiple-http-authorization-headers
            $tokenData = $endpoint->getAuthorizationToken();
            if (!empty($tokenData['tokenname']) && !empty($tokenData['token'])) {
                $request->addHeader(
                    'Authorization: '.$tokenData['tokenname'].' '.$tokenData['token']
                );
            }
        }

        if (Request::METHOD_POST === $method) {
            if ($request->getFileUpload()) {
                $data = AdapterHelper::buildUploadBodyFromRequest($request);

                $contentLength = \strlen($data);
                $request->addHeader("Content-Length: $contentLength");
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
                }
            }
        } elseif (Request::METHOD_PUT === $method) {
            $data = $request->getRawData();
            if (null !== $data) {
                stream_context_set_option(
                    $context,
                    'http',
                    'content',
                    $data
                );
                // The stream context automatically adds a "Connection: close" header which fails on Solr 8.5.0
                $request->addHeader('Connection: Keep-Alive');
            }
        }

        $headers = $request->getHeaders();
        if (\count($headers) > 0) {
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
    protected function getData(string $uri, $context): array
    {
        $data = @file_get_contents($uri, false, $context);

        // @see https://www.php.net/manual/en/reserved.variables.httpresponseheader.php
        // @phpstan-ignore-next-line https://github.com/phpstan/phpstan/issues/3213
        return [$data, $http_response_header ?? []];
    }
}
