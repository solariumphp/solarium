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
 * @namespace
 */
namespace Solarium\Client\Adapter;
use Solarium;
use Solarium\Client;

/**
 * cURL HTTP adapter
 *
 * @author Intervals <info@myintervals.com>
 * @package Solarium
 * @subpackage Client
 */
class Curl extends Adapter
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
           throw new \Solarium\Exception('cURL is not available, install it to use the CurlHttp adapter');
        }

        parent::_init();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Execute a Solr request using the cURL Http
     *
     * @param Solarium\Client\Request $request
     * @return Solarium\Client\Response
     */
    public function execute($request)
    {
        list($data, $headers) = $this->_getData($request);
        $this->check($data, $headers);
        return new Client\Response($data, $headers);
    }

    /**
     * Execute request
     *
     * @param Solarium\Client\Request $request
     * @return array
     */
    protected function _getData($request)
    {
        // @codeCoverageIgnoreStart
        $uri = $this->getBaseUri() . $request->getUri();
        $method = $request->getMethod();
        $options = $this->_createOptions($request);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);

        if (!isset($options['headers']['Content-Type'])) {
            $options['headers']['Content-Type'] = 'text/xml; charset=utf-8';
        }
        if (!isset($options['headers']['Content-Type'])) {
            $options['headers']['Content-Type'] = 'text/xml; charset=utf-8';
        }

        if (count($options['headers'])) {
            $arr = array();
            foreach ($options['headers'] as $k => $v) {
                $arr[] = $k . ": " . $v;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);
        }

        if ($method == Client\Request::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getRawData());
            $httpResponse  = curl_exec($ch);
        } else if ($method == Client\Request::METHOD_GET) {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            $httpResponse  = curl_exec($ch);
        } else if ($method == Client\Request::METHOD_HEAD) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
            $httpResponse  = curl_exec($ch);
        } else {
            throw new \Solarium\Exception("unsupported method: $method");
        }

        $headers = array(); $data = '';

        if ($httpResponse !== false) {
            $data = $httpResponse;
            $info = curl_getinfo($ch);
            $headers = array();
            $headers[] = 'HTTP/1.1 ' . $info['http_code']. ' OK';
        }

        return array($data, $headers);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Create http request options from request.
     *
     * @param Solarium\Client\Request $request
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
     * @throws Solarium\Client\HttpException
     * @param string $data
     * @param array $headers
     * @return void
     */
    public function check($data, $headers)
    {
        // if there is no data and there are no headers it's a total failure,
        // a connection to the host was impossible.
        if (empty($data) && count($headers) == 0) {
            throw new Client\HttpException("HTTP request failed");
        }
    }
}
