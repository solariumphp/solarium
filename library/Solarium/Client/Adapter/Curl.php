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
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Client
 */

/**
 * cURL HTTP adapter
 *
 * @author Intervals <info@myintervals.com>
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_Adapter_Curl extends Solarium_Client_Adapter
{

    /**
     * Initialization hook
     *
     * Checks the availability of Curl_http
     */
    protected function _init()
    {
        // @codeCoverageIgnoreStart
        if (!function_exists('curl_init')) {
           throw new Solarium_Exception('cURL is not available, install it to use the CurlHttp adapter');
        }

        parent::_init();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Execute a Solr request using the cURL Http
     *
     * @param Solarium_Client_Request $request
     * @return Solarium_Client_Response
     */
    public function execute($request)
    {
        return $this->_getData($request);
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
        $handle = $this->createHandle($request);
        $httpResponse = curl_exec($handle);

        return $this->getResponse($handle, $httpResponse);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get the response for a curl handle
     *
     * @param resource $handle
     * @param string $httpResponse
     * @return Solarium_Client_Response
     */
    public function getResponse($handle, $httpResponse)
    {
        // @codeCoverageIgnoreStart
        if ($httpResponse !== false) {
            $data = $httpResponse;
            $info = curl_getinfo($handle);
            $headers = array();
            $headers[] = 'HTTP/1.1 ' . $info['http_code']. ' OK';
        } else {
            $headers = array();
            $data = '';
        }

        curl_close($handle);
        $this->check($data, $headers);
        return new Solarium_Client_Response($data, $headers);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Create curl handle for a request
     *
     * @param Solarium_Client_Request $request
     * @return resource
     */
    public function createHandle($request)
    {
        // @codeCoverageIgnoreStart
        $uri = $this->getBaseUri() . $request->getUri();
        $method = $request->getMethod();
        $options = $this->_createOptions($request);

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $uri);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($handler, CURLOPT_TIMEOUT, $options['timeout']);

        if (!isset($options['headers']['Content-Type'])) {
            $options['headers']['Content-Type'] = 'text/xml; charset=utf-8';
        }

        if (count($options['headers'])) {
            $headers = array();
            foreach ($options['headers'] as $key => $value) {
                $headers[] = $key . ": " . $value;
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
        }

        if ($method == Solarium_Client_Request::METHOD_POST) {
            curl_setopt($handler, CURLOPT_POST, true);
            curl_setopt($handler, CURLOPT_POSTFIELDS, $request->getRawData());
        } else if ($method == Solarium_Client_Request::METHOD_GET) {
            curl_setopt($handler, CURLOPT_HTTPGET, true);
        } else if ($method == Solarium_Client_Request::METHOD_HEAD) {
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'HEAD');
        } else {
            throw new Solarium_Exception("unsupported method: $method");
        }

        return $handler;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Create http request options from request.
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
