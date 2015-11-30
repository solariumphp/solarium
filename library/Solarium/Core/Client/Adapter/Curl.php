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
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\HttpException;

/**
 * cURL HTTP adapter.
 *
 * @author Intervals <info@myintervals.com>
 */
class Curl extends Configurable implements AdapterInterface
{
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
        return $this->getData($request, $endpoint);
    }

    /**
     * Get the response for a curl handle.
     *
     * @param resource $handle
     * @param string   $httpResponse
     *
     * @return Response
     */
    public function getResponse($handle, $httpResponse)
    {
        // @codeCoverageIgnoreStart
        if ($httpResponse !== false && $httpResponse !== null) {
            $data = $httpResponse;
            $info = curl_getinfo($handle);
            $headers = array();
            $headers[] = 'HTTP/1.1 '.$info['http_code'].' OK';
        } else {
            $headers = array();
            $data = '';
        }

        $this->check($data, $headers, $handle);
        curl_close($handle);

        return new Response($data, $headers);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Create curl handle for a request.
     *
     * @throws InvalidArgumentException
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return resource
     */
    public function createHandle($request, $endpoint)
    {
        // @codeCoverageIgnoreStart
        $uri = $endpoint->getBaseUri().$request->getUri();
        $method = $request->getMethod();
        $options = $this->createOptions($request, $endpoint);

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $uri);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        if (!ini_get('open_basedir')) {
            curl_setopt($handler, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($handler, CURLOPT_TIMEOUT, $options['timeout']);
        curl_setopt($handler, CURLOPT_CONNECTTIMEOUT, $options['timeout']);

        if (null !== ($proxy = $this->getOption('proxy'))) {
            curl_setopt($handler, CURLOPT_PROXY, $proxy);
        }

        if (!isset($options['headers']['Content-Type'])) {
            if($method == Request::METHOD_GET){
                $options['headers']['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
            } else {
                $options['headers']['Content-Type'] = 'application/xml; charset=utf-8';
            }
        }

        // Try endpoint authentication first, fallback to request for backwards compatibility
        $authData = $endpoint->getAuthentication();
        if (empty($authData['username'])) {
            $authData = $request->getAuthentication();
        }

        if (!empty($authData['username']) && !empty($authData['password'])) {
            curl_setopt($handler, CURLOPT_USERPWD, $authData['username'].':'.$authData['password']);
            curl_setopt($handler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        if (count($options['headers'])) {
            $headers = array();
            foreach ($options['headers'] as $key => $value) {
                $headers[] = $key.": ".$value;
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
        }

        if ($method == Request::METHOD_POST) {
            curl_setopt($handler, CURLOPT_POST, true);

            if ($request->getFileUpload()) {
                if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
                    $curlFile = curl_file_create($request->getFileUpload());
                    curl_setopt($handler, CURLOPT_POSTFIELDS, array('content' => $curlFile));
                } else {
                    curl_setopt($handler, CURLOPT_POSTFIELDS, array('content' => '@'.$request->getFileUpload()));
                }
            } else {
                curl_setopt($handler, CURLOPT_POSTFIELDS, $request->getRawData());
            }
        } elseif ($method == Request::METHOD_GET) {
            curl_setopt($handler, CURLOPT_HTTPGET, true);
        } elseif ($method == Request::METHOD_HEAD) {
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'HEAD');
        } else {
            throw new InvalidArgumentException("unsupported method: $method");
        }

        return $handler;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Check result of a request.
     *
     * @throws HttpException
     *
     * @param string   $data
     * @param array    $headers
     * @param resource $handle
     */
    public function check($data, $headers, $handle)
    {
        // if there is no data and there are no headers it's a total failure,
        // a connection to the host was impossible.
        if (empty($data) && count($headers) == 0) {
            throw new HttpException('HTTP request failed, '.curl_error($handle));
        }
    }

    /**
     * Execute request.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return Response
     */
    protected function getData($request, $endpoint)
    {
        // @codeCoverageIgnoreStart
        $handle = $this->createHandle($request, $endpoint);
        $httpResponse = curl_exec($handle);

        return $this->getResponse($handle, $httpResponse);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Initialization hook.
     *
     * Checks the availability of Curl_http
     *
     * @throws RuntimeException
     */
    protected function init()
    {
        // @codeCoverageIgnoreStart
        if (!function_exists('curl_init')) {
            throw new RuntimeException('cURL is not available, install it to use the CurlHttp adapter');
        }

        parent::init();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Create http request options from request.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return array
     */
    protected function createOptions($request, $endpoint)
    {
        // @codeCoverageIgnoreStart
        $options = array(
            'timeout' => $endpoint->getTimeout(),
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
}
