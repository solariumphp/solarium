<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 * * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\Core\Client\Adapter;

use Guzzle\Http\Client as GuzzleClient;
use Solarium\Core\Configurable;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Client\Endpoint;
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
    public function execute($request, $endpoint)
    {
        $guzzleRequest = $this->getGuzzleClient()->createRequest(
            $request->getMethod(),
            $endpoint->getBaseUri() . $request->getUri(),
            $this->getRequestHeaders($request),
            $this->getRequestBody($request),
            array(
                'timeout' => $endpoint->getTimeout(),
                'connecttimeout' => $endpoint->getTimeout(),
            )
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
                array("HTTP/1.1 {$guzzleResponse->getStatusCode()} {$guzzleResponse->getReasonPhrase()}"),
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
    }

    /**
     * Gets the Guzzle HTTP client instance.
     *
     * @return GuzzleClient
     */
    public function getGuzzleClient()
    {
        if ($this->guzzleClient === null) {
            $this->guzzleClient = new GuzzleClient(null, $this->options);
        }

        return $this->guzzleClient;
    }

    /**
     * Helper method to create a request body suitable for a guzzle 3 request.
     *
     * @param Request $request The incoming solarium request.
     *
     * @return null|resource|string
     */
    private function getRequestBody(Request $request)
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            return null;
        }

        if ($request->getFileUpload()) {
            return fopen($request->getFileUpload(), 'r');
        }

        return $request->getRawData();
    }

    /**
     * Helper method to extract headers from the incoming solarium request and put them in a format
     * suitable for a guzzle 3 request.
     *
     * @param Request $request The incoming solarium request.
     *
     * @return array
     */
    private function getRequestHeaders(Request $request)
    {
        $headers = array();
        foreach ($request->getHeaders() as $headerLine) {
            list($header, $value) = explode(':', $headerLine);
            if ($header = trim($header)) {
                $headers[$header] = trim($value);
            }
        }

        if (!isset($headers['Content-Type'])) {
            if ($request->getMethod() == Request::METHOD_GET) {
                $headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
            } else {
                $headers['Content-Type'] = 'application/xml; charset=utf-8';
            }
        }

        return $headers;
    }
}
