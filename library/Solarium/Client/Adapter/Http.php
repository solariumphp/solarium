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
 * @package Solarium
 * @subpackage Client
 */

/**
 * Basic HTTP adapter using a stream
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_Adapter_Http extends Solarium_Client_Adapter
{

    /**
     * Executes a select query
     *
     * @param Solarium_Query_Select $query
     * @return Solarium_Result_Select
     */
    public function select($query)
    {
        $request = new Solarium_Client_Request_Select($this->_options, $query);
        $data = $this->_handleRequest($request);

        $response = new Solarium_Client_Response_Select($query, $data);
        return $response->getResult();

    }

    /**
     * Executes a ping query
     *
     * @param Solarium_Query_Ping $query
     * @return boolean
     */
    public function ping($query)
    {
        $request = new Solarium_Client_Request_Ping($this->_options, $query);
        return (boolean)$this->_handleRequest($request);
    }

    /**
     * Executes an update query
     *
     * @param Solarium_Query_Update $query
     * @return Solarium_Result_Update
     */
    public function update($query)
    {
        $request = new Solarium_Client_Request_Update($this->_options, $query);
        $data = $this->_handleRequest($request);

        $response = new Solarium_Client_Response_Update($query, $data);
        return $response->getResult();
    }

    /**
     * Handle Solr communication
     *
     * @throws Solarium_Exception
     * @param Solarium_Client_Request
     * @return array
     */
    protected function _handleRequest($request)
    {
        $method = $request->getMethod();
        $context = stream_context_create(
            array('http' => array(
                'method' => $method,
                'timeout' => $this->getOption('timeout')
            ))
        );

        if ($method == Solarium_Client_Request::POST) {
            $data = $request->getRawData();
            if (null !== $data) {
                stream_context_set_option(
                    $context,
                    'http',
                    'content',
                    $data
                );
                stream_context_set_option(
                    $context,
                    'http',
                    'header',
                    'Content-Type: text/xml; charset=UTF-8'
                );
            }
        }

        $data = @file_get_contents($request->getUri(), false, $context);

        // if there is no data and there are no headers it's a total failure,
        // a connection to the host was impossible. 
        if (false === $data && !isset($http_response_header)) {
            throw new Solarium_Client_HttpException("HTTP request failed");
        }

        $this->_checkHeaders($http_response_header);

        if ($method == Solarium_Client_Request::HEAD) {
            // HEAD request has no result data
            return true;
        } else {
            if (false === $data) {
                $error = error_get_last();
                throw new Solarium_Exception($error['message']);
            }

            return $this->_jsonDecode($data);
        }
    }

    /**
     * Check HTTP headers
     *
     * The status header is parsed, an exception will be thrown for an error
     * code.
     *
     * @throws Solarium_Client_HttpException
     * @param array $headers
     * @return void
     */
    protected function _checkHeaders($headers)
    {
        // get the status header
        $statusHeader = null;
        foreach( $headers AS $header) {
            if (substr($header, 0, 4) == 'HTTP') {
                $statusHeader = $header;
                break;
            }
        }

        if (null == $statusHeader) {
            throw new Solarium_Client_HttpException("No HTTP status found");
        }

        // parse header like "$statusInfo[1]" into code and message
        // $statusInfo[1] = the HTTP response code
        // $statusInfo[2] = the response message
        $statusInfo = explode(' ', $statusHeader, 3);

        // check status for error (range of 400 and 500)
        $statusNum = floor($statusInfo[1] / 100);
        if ($statusNum == 4 || $statusNum == 5) {
            throw new Solarium_Client_HttpException(
                $statusInfo[2],
                $statusInfo[1]
            );
        }
    }

    /**
     * Decode json response data
     * 
     * @throws Solarium_Exception
     * @param string $data
     * @return string
     */
    protected function _jsonDecode($data)
    {
        $data = json_decode($data, true);
        if (null === $data) {
            throw new Solarium_Exception(
                'Solr JSON response could not be decoded'
            );
        }

        return $data;
    }
}