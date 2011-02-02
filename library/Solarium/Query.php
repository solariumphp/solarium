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
 * Base class for all queries
 */
class Solarium_Query extends Solarium_Configurable
{
    
    /**
     * Set path option
     *
     * @param string $path
     * @return Solarium_Query Provides fluent interface
     */
    public function setPath($path)
    {
        return $this->_setOption('path', $path);
    }

    /**
     * Get path option
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getOption('path');
    }

    /**
     * Set resultclass option
     *
     * @param string $classname
     * @return Solarium_Query Provides fluent interface
     */
    public function setResultClass($classname)
    {
        return $this->_setOption('resultclass', $classname);
    }

    /**
     * Get resultclass option
     *
     * @return string
     */
    public function getResultClass()
    {
        return $this->getOption('resultclass');
    }
    
    /**
     * Escape special Solr characters in a value
     * @param string $string
     * @return string
     */
    public function escapeValue($string)
    {
        $match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[',
                        ']', '^', '~', '*', '?', ':', '"', ';', ' ');
        $replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(',
                        '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*',
                        '\\?', '\\:', '\\"', '\\;', '\\ ');
        $string = str_replace($match, $replace, $string);

        return $string;
    }

    /**
     * Render a param with localParams
     *
     * @param string $value
     * @param array $localParams in key => value format
     * @return string with Solr localparams syntax
     */
    public function renderLocalParams($value, $localParams = array())
    {
        $prefix = '';

        if (count($localParams) > 0) {
            $prefix .= '{!';

            foreach ($localParams AS $paramName => $paramValue) {
                $prefix .= $paramName . '=' . $paramValue . ' ';
            }

            $prefix = rtrim($prefix) . '}';
        }

        return $prefix . $value;
    }
}