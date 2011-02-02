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
 */

/**
 *
 */
class Solarium_Client_Request
{

    protected $_postData = null;

    protected $_params = array();

    protected $_options;

    public function __construct($options, $query)
    {
        $this->_options = $options;
        $this->_query = $query;

        $this->_init();
    }

    protected function _init()
    {
        
    }

    /**
     * Build a URL for this request
     *
     * @return string
     */
    public function getUrl()
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

    public function getPostData()
    {
        return $this->_postData;
    }

    /**
     * Render a boolean attribute
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

}