<?php
/**
 * Copyright 2011 Gasol Wu. PIXNET Digital Media Corporation.
 * All rights reserved.
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
 * @copyright Copyright 2011 Gasol Wu <gasol.wu@gmail.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Configurable;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Client\Endpoint;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\HttpException;
use Solarium\Exception\InvalidArgumentException;

/**
 * Pecl HTTP adapter.
 *
 * @author Gasol Wu <gasol.wu@gmail.com>
 */
class PeclHttp extends Configurable implements AdapterInterface
{
    /**
     * Execute a Solr request using the Pecl Http.
     *
     * @throws HttpException
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return Response
     */
    public function execute($request, $endpoint)
    {
        $httpRequest = $this->toHttpRequest($request, $endpoint);

        try {
            $httpMessage = $httpRequest->send();
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage());
        }

        return new Response(
            $httpMessage->getBody(),
            $this->toRawHeaders($httpMessage)
        );
    }

    /**
     * adapt Request to HttpRequest.
     *
     * {@link http://us.php.net/manual/en/http.constants.php
     *  HTTP Predefined Constant}
     *
     * {@link http://us.php.net/manual/en/http.request.options.php
     *  HttpRequest options}
     *
     * @throws InvalidArgumentException
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return \HttpRequest
     */
    public function toHttpRequest($request, $endpoint)
    {
        $url = $endpoint->getBaseUri().$request->getUri();
        $httpRequest = new \HttpRequest($url);

        $headers = array();
        foreach ($request->getHeaders() as $headerLine) {
            list($header, $value) = explode(':', $headerLine);
            if ($header = trim($header)) {
                $headers[$header] = trim($value);
            }
        }

        // Try endpoint authentication first, fallback to request for backwards compatibility
        $authData = $endpoint->getAuthentication();
        if (empty($authData['username'])) {
            $authData = $request->getAuthentication();
        }

        if (!empty($authData['username']) && !empty($authData['password'])) {
            $headers['Authorization'] = 'Basic '.base64_encode($authData['username'].':'.$authData['password']);
        }

        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                $method = HTTP_METH_GET;
                break;
            case Request::METHOD_POST:
                $method = HTTP_METH_POST;
                if ($request->getFileUpload()) {
                    $httpRequest->addPostFile(
                        'content',
                        $request->getFileUpload(),
                        'application/octet-stream; charset=binary'
                    );
                } else {
                    $httpRequest->setBody($request->getRawData());
                    if (!isset($headers['Content-Type'])) {
                        $headers['Content-Type'] = 'text/xml; charset=utf-8';
                    }
                }
                break;
            case Request::METHOD_HEAD:
                $method = HTTP_METH_HEAD;
                break;
            default:
                throw new InvalidArgumentException(
                    'Unsupported method: '.$request->getMethod()
                );
        }

        $httpRequest->setMethod($method);
        $httpRequest->setOptions(
            array(
                'timeout' => $endpoint->getTimeout(),
                'connecttimeout' => $endpoint->getTimeout(),
                'dns_cache_timeout' => $endpoint->getTimeout(),
            )
        );
        $httpRequest->setHeaders($headers);

        return $httpRequest;
    }

    /**
     * Initialization hook.
     *
     * Checks the availability of pecl_http
     *
     * @throws RuntimeException
     */
    protected function init()
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('HttpRequest', false)) {
            throw new RuntimeException('Pecl_http is not available, install it to use the PeclHttp adapter');
        }

        parent::init();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Convert key/value pair header to raw header.
     *
     * <code>
     * //before
     * $headers['Content-Type'] = 'text/plain';
     *
     * ...
     *
     * //after
     * $headers[0] = 'Content-Type: text/plain';
     * </code>
     *
     * @param $message \HttpMessage
     *
     * @return array
     */
    protected function toRawHeaders($message)
    {
        $headers[] = 'HTTP/'.$message->getHttpVersion().' '.$message->getResponseCode().' '.$message->getResponseStatus();

        foreach ($message->getHeaders() as $header => $value) {
            $headers[] = "$header: $value";
        }

        return $headers;
    }
}
