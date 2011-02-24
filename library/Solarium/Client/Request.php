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
 * Base class for building Solr HTTP requests
 *
 * Most {@link Solarium_Client_Adapter} implementations will use HTTP for
 * communicating with Solr. While the HTTP part is adapter-specific, generating
 * the HTTP request setting (url, postdata, etc.) is not.
 * This abstract class is the base for several requestbuilders that generate the
 * settings for the various querytypes.
 *
 * @package Solarium
 * @subpackage Client
 */
abstract class Solarium_Client_Request
{
    /**
     * Http request methods
     */
    const GET     = 'GET';
    const POST    = 'POST';
    const HEAD    = 'HEAD';

    /**
     * Query instance
     *
     * The query that has to be used for building the request.
     *
     * @var Solarium_Query
     */
    protected $_query;

    /**
     * Adapter options
     *
     * When the adapter class the {@link __construct()} method it forwards it's
     * options. These options are needed for building the right uri.
     *
     * @var array
     */
    protected $_options;

    /**
     * HTTP GET params
     *
     * Used for building the uri in {@link buildUri()}
     * 
     * @var array
     */
    protected $_params;

    /**
     * Constructor
     *
     * @param array|object $options Passed on by the adapter
     * @param Solarium_Query $query
     */
    public function __construct($options, $query)
    {
        $this->_options = $options;
        $this->_query = $query;
    }

    /**
     * Get HTTP request method
     *
     * @return string
     */
    public function getMethod()
    {
        return self::GET;
    }

    /**
     * Get request uri
     *
     * To be implemented by query specific request builders.
     *
     * @abstract
     * @return string
     */
    abstract public function getUri();

    /**
     * Get raw POST data
     *
     * Returns null by default, disabling raw POST.
     * If raw POST data is needed for a request the builder must override this
     * method and return a data string. This string must be safely encoded.
     *
     * @return null
     */
    public function getRawData()
    {
        return null;
    }

    /**
     * Get request GET params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Build a URL for this request
     *
     * Based on {@link $_options} and {@link $_params} as input.
     *
     * @internal Solr expects multiple GET params of the same name instead of
     *  the PHP array type notation. Therefore the result of http_build_query
     *  has to be altered.
     *
     * @return string
     */
    public function buildUri()
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

        if (null !== $this->_options['core']) {
            $core = '/' . $this->_options['core'];
        } else {
            $core = '';
        }

        return 'http://' . $this->_options['host'] . ':'
               . $this->_options['port'] . $this->_options['path']
               . $core . $this->_query->getOption('path') . '?'
               . $queryString;
    }

    /**
     * Render a boolean attribute
     *
     * For use in building XML messages
     *
     * @param string $name
     * @param boolean $value
     * @return string
     */
    public function boolAttrib($name, $value)
    {
        if (null !== $value) {
            $value = (true == $value) ? 'true' : 'false';
            return $this->attrib($name, $value);
        } else {
            return '';
        }
    }

    /**
     * Render an attribute
     *
     * For use in building XML messages
     *
     * @param string $name
     * @param striung $value
     * @return string
     */
    public function attrib($name, $value)
    {
        if (null !== $value) {
            return ' ' . $name . '="' . $value . '"';
        } else {
            return '';
        }
    }
    
    /**
     * Render a param with localParams
     *
     * LocalParams can be use in various Solr GET params.
     * @link http://wiki.apache.org/solr/LocalParams
     *
     * @param string $value
     * @param array $localParams in key => value format
     * @return string with Solr localparams syntax
     */
    public function renderLocalParams($value, $localParams = array())
    {
        $params = '';
        foreach ($localParams AS $paramName => $paramValue) {
            if (empty($paramValue)) continue;

            if (is_array($paramValue)) {
                $paramValue = implode($paramValue, ',');
            }

            $params .= $paramName . '=' . $paramValue . ' ';
        }

        if ($params !== '') {
            $value = '{!' . trim($params) . '}' . $value;
        }

        return $value;
    }

    /**
     * Add a param to the request
     *
     * This method makes adding params easy in two ways:
     * - empty params are filtered out, so you don't to manually check each
     * param
     * - if you add the same param twice it will be converted into a multivalue
     * param automatically. This way you don't need to check for single or
     * multiple values beforehand.
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addParam($name, $value)
    {
        if (0 === strlen($value)) return;

        if (!isset($this->_params[$name])) {
            $this->_params[$name] = $value;
        } else {
            if (!is_array($this->_params[$name])) {
                $this->_params[$name] = array($this->_params[$name]);
            }
            $this->_params[$name][] = $value;
        }
    }

}