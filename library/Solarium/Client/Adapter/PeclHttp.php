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
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Client
 */

/**
 * Pecl HTTP adapter
 *
 * @author Gasol Wu <gasol.wu@gmail.com>
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_Adapter_PeclHttp extends Solarium_Client_Adapter
{

    /**
     * Initialization hook
     *
     * Checks the availability of pecl_http
     */
    protected function _init()
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('HttpRequest', false)) {
           throw new Solarium_Exception('Pecl_http is not available, install it to use the PeclHttp adapter');
        }

        parent::_init();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Execute a Solr request using the Pecl Http
     *
     * @param Solarium_Client_Request $request
     * @return Solarium_Client_Response
     */
    public function execute($request)
    {
        $httpRequest = $this->toHttpRequest($request);

        try {
            $httpMessage = $httpRequest->send();
        } catch (Exception $e) {
            throw new Solarium_Client_HttpException($e->getMessage());
        }

        return new Solarium_Client_Response(
            $httpMessage->getBody(),
            $this->_toRawHeaders($httpMessage)
        );
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
     * @param $message HttpMessage
     * @return array
     */
    protected function _toRawHeaders($message)
    {
        $headers[] = 'HTTP/' . $message->getHttpVersion()
                   . ' ' . $message->getResponseCode()
                   . ' ' . $message->getResponseStatus();

        foreach ($message->getHeaders() as $header => $value) {
            $headers[] = "$header: $value";
        }

        return $headers;
    }

    /**
     *
     * adapt Solarium_Client_Request to HttpRequest
     *
     * {@link http://us.php.net/manual/en/http.constants.php
     *  HTTP Predefined Constant}
     *
     * @param Solarium_Client_Request $request
     * @param HttpRequest
     */
    public function toHttpRequest($request)
    {
        $url = $this->getBaseUri() . $request->getUri();
        $httpRequest = new HttpRequest($url);

        $headers = array();
        foreach ($request->getHeaders() as $headerLine) {
            list($header, $value) = explode(':', $headerLine);
            if ($header = trim($header)) {
                $headers[$header] = trim($value);
            }
        }

        switch($request->getMethod()) {
        case Solarium_Client_Request::METHOD_GET:
            $method = HTTP_METH_GET;
            break;
        case Solarium_Client_Request::METHOD_POST:
            $method = HTTP_METH_POST;
            $httpRequest->setBody($request->getRawData());
            if (!isset($headers['Content-Type'])) {
                $headers['Content-Type'] = 'text/xml; charset=utf-8';
            }
            break;
        case Solarium_Client_Request::METHOD_HEAD:
            $method = HTTP_METH_HEAD;
            break;
        default:
            throw new Solarium_Exception(
                'Unsupported method: ' . $request->getMethod()
            );
        }

        $httpRequest->setMethod($method);
        $httpRequest->setOptions(array('timeout' => $this->getTimeout()));
        $httpRequest->setHeaders($headers);

        return $httpRequest;
    }
}
