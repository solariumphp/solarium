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
 * Class for describing a request
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_Request extends Solarium_Configurable
{

    /**
     * Request GET method
     */
    const METHOD_GET     = 'GET';

    /**
     * Request POST method
     */
    const METHOD_POST    = 'POST';

    /**
     * Request HEAD method
     */
    const METHOD_HEAD    = 'HEAD';

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'method' => self::METHOD_GET,
    );

    /**
     * Request headers
     */
    protected $_headers = array();

    /**
     * Request params
     *
     * Multivalue params are supported using a multidimensional array:
     * 'fq' => array('cat:1','published:1')
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Raw POST data
     *
     * @var string
     */
    protected $_rawData;

    /**
     * Initialization hook
     */
    protected function _init()
    {
        foreach ($this->_options AS $name => $value) {
            switch ($name) {
                case 'rawdata':
                    $this->setRawData($value);
                    break;
                case 'param':
                    $this->setParams($value);
                    break;
                case 'header':
                    $this->setHeaders($value);
                    break;
            }
        }
    }

    /**
     * Set request handler
     *
     * @param string $handler
     * @return Solarium_Client_Request
     */
    public function setHandler($handler)
    {
        $this->_setOption('handler', $handler);
        return $this;
    }

    /**
     * Get request handler
     *
     * @return string
     */
    public function getHandler()
    {
        return $this->getOption('handler');
    }

    /**
     * Set request method
     *
     * Use one of the constants as value
     *
     * @param string $method
     * @return Solarium_Client_Request
     */
    public function setMethod($method)
    {
        $this->_setOption('method', $method);
        return $this;
    }

    /**
     * Get request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getOption('method');
    }

    /**
     * Get a param value
     *
     * @param string $key
     * @return string|array
     */
    public function getParam($key)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        } else {
            return null;
        }
    }

    /**
     * Get all params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Set request params
     *
     * @param array $params
     * @return Solarium_Client_Request
     */
    public function setParams($params)
    {
        $this->clearParams();
        $this->addParams($params);
        return $this;
    }

    /**
     * Add a request param
     *
     * If you add a request param that already exists the param will be converted into a multivalue param,
     * unless you set the overwrite param to true.
     *
     * Empty params are not added to the request. If you want to empty a param disable it you should use
     * remove param instead.
     *
     * @param string $key
     * @param string|array $value
     * @param boolean $overwrite
     * @return Solarium_Client_Request
     */
    public function addParam($key, $value, $overwrite = false)
    {
        if ($value !== null) {
            if (!$overwrite && isset($this->_params[$key])) {
                if (!is_array($this->_params[$key])) {
                    $this->_params[$key] = array($this->_params[$key]);
                }
                $this->_params[$key][] = $value;
            } else {
                // not all solr handlers support 0/1 as boolean values...
                if($value === true) $value = 'true';
                if($value === false) $value = 'false';

                $this->_params[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Add multiple params to the request
     *
     * @param array $params
     * @param boolean $overwrite
     * @return Solarium_Client_Request
     */
    public function addParams($params, $overwrite = false)
    {
        foreach ($params as $key => $value) {
            $this->addParam($key, $value, $overwrite);
        }

        return $this;
    }

    /**
     * Remove a param by key
     *
     * @param string $key
     * @return Solarium_Client_Request
     */
    public function removeParam($key)
    {
        if (isset($this->_params[$key])) {
            unset($this->_params[$key]);
        }
        return $this;
    }

    /**
     * Clear all request params
     *
     * @return Solarium_Client_Request
     */
    public function clearParams()
    {
        $this->_params = array();
        return $this;
    }

    /**
     * Get raw POST data
     *
     * @return null
     */
    public function getRawData()
    {
        return $this->_rawData;
    }

    /**
     * Set raw POST data
     *
     * This string must be safely encoded.
     *
     * @param string $data
     * @return Solarium_Client_Request
     */
    public function setRawData($data)
    {
        $this->_rawData = $data;
        return $this;
    }

    /**
     * Get all request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Set request headers
     *
     * @param array $headers
     * @return Solarium_Client_Request
     */
    public function setHeaders($headers)
    {
        $this->clearHeaders();
        $this->addHeaders($headers);
        return $this;
    }

    /**
     * Add a request header
     *
     * @param string|array $value
     * @return Solarium_Client_Request
     */
    public function addHeader($value)
    {
        $this->_headers[] = $value;

        return $this;
    }

    /**
     * Add multiple headers to the request
     *
     * @param array $headers
     * @return Solarium_Client_Request
     */
    public function addHeaders($headers)
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }

        return $this;
    }

    /**
     * Clear all request headers
     *
     * @return Solarium_Client_Request
     */
    public function clearHeaders()
    {
        $this->_headers = array();
        return $this;
    }

    /**
     * Get an URI for this request
     *
     * @return string
     */
    public function getUri()
    {
        return $this->getHandler() . '?' . $this->getQueryString();
    }

    /**
     * Get the query string for this request
     *
     * @return string
     */
    public function getQueryString()
    {
        $queryString = '';
        if (count($this->_params) > 0) {
            $queryString = http_build_query($this->_params, null, '&');
            $queryString = preg_replace(
                '/%5B(?:[0-9]|[1-9][0-9]+)%5D=/',
                '=',
                $queryString
            );
        }

        return $queryString;
    }

    /**
     * Magic method enables a object to be transformed to a string
     *
     * Get a summary showing significant variables in the object
     * note: uri resource is decoded for readability
     *
     * @return string
     */
    public function __toString()
    {
        $output = __CLASS__ . '::toString' . "\n"
                . 'method: ' . $this->getMethod() . "\n"
                . 'header: ' . print_r($this->getHeaders(), 1) //don't add newline when using print_r
                . 'resource: ' . $this->getUri() . "\n"
                . 'resource urldecoded: ' . urldecode($this->getUri()) . "\n"
                . 'raw data: ' . $this->getRawData() . "\n";

        return $output;
    }

}