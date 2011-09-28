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
        if (!function_exists('http_get')) {
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
        list($data, $headers) = $this->_getData($request);
        $this->check($data, $headers);
        return new Solarium_Client_Response($data, $headers);
    }

    /**
     * Execute request
     *
     * @param Solarium_Client_Request $request
     * @return array
     */
    protected function _getData($request)
    {
        // @codeCoverageIgnoreStart
        $uri = $this->getBaseUri() . $request->getUri();
        $method = $request->getMethod();
        $options = $this->_createOptions($request);

        if ($method == Solarium_Client_Request::METHOD_POST) {
            if (!isset($options['headers']['Content-Type'])) {
                $options['headers']['Content-Type'] = 'text/xml; charset=utf-8';
            }
            $httpResponse = http_post_data(
                $uri, $request->getRawData(), $options
            );
        } else if ($method == Solarium_Client_Request::METHOD_GET) {
            $httpResponse = http_get($uri, $options);
        } else if ($method == Solarium_Client_Request::METHOD_HEAD) {
            $httpResponse = http_head($uri, $options);
        } else {
            throw new Solarium_Exception("unsupported method: $method");
        }

        $headers = array();
        $data = '';
        if ($message = http_parse_message($httpResponse)) {
            $data = $message->body;
            if ($firstPositionOfCRLF = strpos($httpResponse, "\r\n\r\n")) {
                $headersAsString = substr(
                    $httpResponse, 0, $firstPositionOfCRLF
                );
                $headers = explode("\n", $headersAsString);
            }
        }

        return array($data, $headers);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Create http request options from request.
     *
     * @link http://php.net/manual/en/http.request.options.php
     *
     * @param Solarium_Client_Request $request
     * @return array
     */
    protected function _createOptions($request)
    {
        // @codeCoverageIgnoreStart
        $options = array(
            'timeout' => $this->getTimeout()
        );
        foreach ($request->getHeaders() as $headerLine) {
            list($header, $value) = explode(':', $headerLine);
            if ($header = trim($header)) {
                $options['headers'][$header] = trim($value);
            }
        }
        return $options;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Check result of a request
     *
     * @throws Solarium_Client_HttpException
     * @param string $data
     * @param array $headers
     * @return void
     */
    public function check($data, $headers)
    {
        // if there is no data and there are no headers it's a total failure,
        // a connection to the host was impossible.
        if (empty($data) && count($headers) == 0) {
            throw new Solarium_Client_HttpException("HTTP request failed");
        }
    }
}
