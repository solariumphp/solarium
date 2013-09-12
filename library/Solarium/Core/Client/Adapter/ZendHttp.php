<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 * Copyright 2012 Alexander Brausewetter. All rights reserved.
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
 * @copyright Copyright 2012 Alexander Brausewetter <alex@helpdeskhq.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Configurable;
use Solarium\Core\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Client\Endpoint;
use Solarium\Exception\HttpException;
use Solarium\Exception\OutOfBoundsException;

/**
 * Adapter that uses a Zend_Http_Client
 *
 * The Zend Framework HTTP client has many great features and has lots of
 * configuration options. For more info see the manual at
 * {@link http://framework.zend.com/manual/en/zend.http.html}
 *
 * To use this adapter you need to have the Zend Framework available (autoloading)
 */
class ZendHttp extends Configurable implements AdapterInterface
{
    /**
     * Zend Http instance for communication with Solr
     *
     * @var \Zend_Http_Client
     */
    protected $zendHttp;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * Set options
     *
     * Overrides any existing values.
     *
     * If the options array has an 'options' entry it is forwarded to the
     * Zend_Http_Client. See the Zend_Http_Clientdocs for the many config
     * options available.
     *
     * The $options param should be an array or an object that has a toArray
     * method, like Zend_Config
     *
     * @param  array|object $options
     * @param  boolean      $overwrite
     * @return self         Provides fluent interface
     */
    public function setOptions($options, $overwrite = false)
    {
        parent::setOptions($options, $overwrite);

        // forward options to zendHttp instance
        if (null !== $this->zendHttp) {

            // forward timeout setting
            $adapterOptions = array();

            // forward adapter options if available
            if (isset($this->options['options'])) {
                $adapterOptions = array_merge($adapterOptions, $this->options['options']);
            }

            $this->zendHttp->setConfig($adapterOptions);
        }

        return $this;
    }

    /**
     * Set the Zend_Http_Client instance
     *
     * This method is optional, if you don't set a client it will be created
     * upon first use, using default and/or custom options (the most common use
     * case)
     *
     * @param  \Zend_Http_Client $zendHttp
     * @return self              Provides fluent interface
     */
    public function setZendHttp($zendHttp)
    {
        $this->zendHttp = $zendHttp;

        return $this;
    }

    /**
     * Get the Zend_Http_Client instance
     *
     * If no instance is available yet it will be created automatically based on
     * options.
     *
     * You can use this method to get a reference to the client instance to set
     * options, get the last response and use many other features offered by the
     * Zend_Http_Client API.
     *
     * @return \Zend_Http_Client
     */
    public function getZendHttp()
    {
        if (null == $this->zendHttp) {
            $options = array();

            // forward zendhttp options
            if (isset($this->options['options'])) {
                $options = array_merge(
                    $options,
                    $this->options['options']
                );
            }

            $this->zendHttp = new \Zend_Http_Client(null, $options);
        }

        return $this->zendHttp;
    }

    /**
     * Execute a Solr request using the Zend_Http_Client instance
     *
     * @throws HttpException
     * @throws OutOfBoundsException
     * @param  Request       $request
     * @param  Endpoint      $endpoint
     * @return Response
     */
    public function execute($request, $endpoint)
    {
        $client = $this->getZendHttp();
        $client->resetParameters();

        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                $client->setMethod(\Zend_Http_Client::GET);
                $client->setParameterGet($request->getParams());
                break;
            case Request::METHOD_POST:
                $client->setMethod(\Zend_Http_Client::POST);
                if ($request->getFileUpload()) {
                    $this->prepareFileUpload($client, $request);
                } else {
                    $client->setParameterGet($request->getParams());
                    $client->setRawData($request->getRawData());
                    $request->addHeader('Content-Type: text/xml; charset=UTF-8');
                }
                break;
            case Request::METHOD_HEAD:
                $client->setMethod(\Zend_Http_Client::HEAD);
                $client->setParameterGet($request->getParams());
                break;
            default:
                throw new OutOfBoundsException('Unsupported method: ' . $request->getMethod());
                break;
        }

        $client->setUri($endpoint->getBaseUri() . $request->getHandler());
        $client->setHeaders($request->getHeaders());
        $this->timeout = $endpoint->getTimeout();

        $response = $client->request();

        return $this->prepareResponse(
            $request,
            $response
        );
    }

    /**
     * Prepare a solarium response from the given request and client
     * response
     *
     * @throws HttpException
     * @param  Request             $request
     * @param  \Zend_Http_Response $response
     * @return Response
     */
    protected function prepareResponse($request, $response)
    {
        if ($response->isError()) {
            throw new HttpException(
                $response->getMessage(),
                $response->getStatus()
            );
        }

        if ($request->getMethod() == Request::METHOD_HEAD) {
            $data = '';
        } else {
            $data = $response->getBody();
        }

        // this is used because getHeaders doesn't return the HTTP header...
        $headers = explode("\n", $response->getHeadersAsString());

        return new Response($data, $headers);
    }

    /**
     * Prepare the client to send the file and params in request
     *
     * @param  \Zend_Http_Client $client
     * @param  Request           $request
     * @return void
     */
    protected function prepareFileUpload($client, $request)
    {
        $filename = $request->getFileUpload();
        $client->setFileUpload(
            'content',
            'content',
            file_get_contents($filename),
            'application/octet-stream; charset=binary'
        );
    }
}
