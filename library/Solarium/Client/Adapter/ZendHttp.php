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
 * Adapter that uses a Zend_Http_Client
 *
 * The Zend Framework HTTP client has many great features and has lots of
 * configuration options. For more info see the manual at
 * {@link http://framework.zend.com/manual/en/zend.http.html}
 *
 * To use this adapter you need to have the Zend Framework available (autoloading)
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_Adapter_ZendHttp extends Solarium_Client_Adapter
{

    /**
     * Zend Http instance for communication with Solr
     *
     * @var Zend_Http_Client
     */
    protected $_zendHttp;

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
     * @param array|object $options
     * @param boolean $overwrite
     * @return Solarium_Client_Adapter_ZendHttp Provides fluent interface
     */
    public function setOptions($options, $overwrite = false)
    {
        parent::setOptions($options, $overwrite);

        // forward options to zendHttp instance
        if (null !== $this->_zendHttp) {

            // forward timeout setting
            $adapterOptions = array('timeout' => $this->getTimeout());

            // forward adapter options if available
            if (isset($this->_options['options'])) {
                $adapterOptions = array_merge($adapterOptions, $this->_options['options']);
            }
            
            $this->_zendHttp->setConfig($adapterOptions);
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
     * @param Zend_Http_Client $zendHttp
     * @return Solarium_Client_Adapter_ZendHttp Provides fluent interface
     */
    public function setZendHttp($zendHttp)
    {
        $this->_zendHttp = $zendHttp;
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
     * @return Zend_Http_Client
     */
    public function getZendHttp()
    {
        if (null == $this->_zendHttp) {
            $options = array('timeout' => $this->getOption('timeout'));

            // forward zendhttp options
            if (isset($this->_options['options'])) {
                $options = array_merge(
                    $options,
                    $this->_options['options']
                );
            }

            $this->_zendHttp = new Zend_Http_Client(null, $options);
        }

        return $this->_zendHttp;
    }

    /**
     * Execute a Solr request using the Zend_Http_Client instance
     *
     * @param Solarium_Client_Request $request
     * @return Solarium_Client_Response
     */
    public function execute($request)
    {
        $client = $this->getZendHttp();

        $client->setMethod($request->getMethod());
        $client->setUri($this->getBaseUri() . $request->getUri());
        $client->setHeaders($request->getHeaders());
        $client->setRawData($request->getRawData());

        $response = $client->request();

        // throw an exception in case of a HTTP error
        if ($response->isError()) {
            throw new Solarium_Client_HttpException(
                $response->getMessage(),
                $response->getStatus()
            );
        }

        if ($request->getMethod() == Solarium_Client_Request::METHOD_HEAD) {
            $data = '';
        } else {
            $data = $response->getBody();
        }

        // this is used because getHeaders doesn't return the HTTP header...
        $headers = explode("\n", $response->getHeadersAsString());

        return new Solarium_Client_Response($data, $headers);
    }

}