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
 */

/**
 * @namespace
 */
namespace Solarium\Core\Query;

use Solarium\Core\Query\Query;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\QueryInterface;

/**
 * Class for building Solarium client requests
 */
abstract class RequestBuilder implements RequestBuilderInterface
{
    /**
     * Build request for a select query
     *
     * @param  QueryInterface|Query $query
     * @return Request
     */
    public function build(QueryInterface $query)
    {
        $request = new Request;
        $request->setHandler($query->getHandler());
        $request->addParam('omitHeader', $query->getOmitHeader());
        $request->addParam('timeAllowed', $query->getTimeAllowed());
        $request->addParams($query->getParams());

        $request->addParam('wt', $query->getResponseWriter());
        if ($query->getResponseWriter() == $query::WT_JSON) {
            // only one JSON format is supported
            $request->addParam('json.nl', 'flat');
        }

        return $request;
    }

    /**
     * Render a param with localParams
     *
     * LocalParams can be use in various Solr GET params.
     * @link http://wiki.apache.org/solr/LocalParams
     *
     * @param  string $value
     * @param  array  $localParams in key => value format
     * @return string with Solr localparams syntax
     */
    public function renderLocalParams($value, $localParams = array())
    {
        $params = '';
        foreach ($localParams as $paramName => $paramValue) {
            if (empty($paramValue)) {
                continue;
            }

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
    * @param string $value
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
