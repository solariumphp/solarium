<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
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
 * @see http://www.solarium-project.org/
 */

/**
 * @namespace
 */

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
    public function execute($request, $endpoint)
    {
        $context = $this->createContext($request, $endpoint);
        $uri = $endpoint->getBaseUri().$request->getUri();

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

        if (Request::METHOD_POST == $method) {
            if ($request->getFileUpload()) {
                $boundary = '----------'.md5(time());
                $CRLF = "\r\n";
                $file = $request->getFileUpload();
                // Add the proper boundary to the Content-Type header
                $headers = $request->getHeaders();
                // Remove the Content-Type header, because we will replace it with something else.
                if (false !== ($key = array_search('Content-Type: multipart/form-data', $headers))) {
                    unset($headers[$key]);
                }
                $request->setHeaders($headers);
                $request->addHeader("Content-Type: multipart/form-data; boundary={$boundary}");
                $data = "--{$boundary}".$CRLF;
                $data .= 'Content-Disposition: form-data; name="upload"; filename='.$file.$CRLF;
                $data .= 'Content-Type: application/octet-stream'.$CRLF.$CRLF;
                $data .= file_get_contents($file).$CRLF;
                $data .= '--'.$boundary.'--';
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
        }

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
        // @codeCoverageIgnoreStart
        $data = @file_get_contents($uri, false, $context);

        $headers = [];

        if (isset($http_response_header)) {
            $headers = $http_response_header;
        }

        return [$data, $headers];
        // @codeCoverageIgnoreEnd
    }
}
