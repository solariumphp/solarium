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
namespace Solarium\QueryType\Analysis\Query;

use Solarium\Core\Client\Client;
use Solarium\QueryType\Analysis\ResponseParser\Field as ResponseParser;
use Solarium\QueryType\Analysis\RequestBuilder\Field as RequestBuilder;

/**
 * Analysis document query
 */
class Field extends Query
{
    /**
     * Default options
     *
     * @var array
     */
    protected $options = array(
        'handler'       => 'analysis/field',
        'resultclass'   => 'Solarium\QueryType\Analysis\Result\Field',
        'omitheader'    => true,
    );

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_ANALYSIS_FIELD;
    }

    /**
     * Get a requestbuilder for this query
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder;
    }

    /**
     * Get a response parser for this query
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser;
    }

    /**
     * Set the field value option
     *
     * The text that will be analyzed. The analysis will mimic the index-time analysis.
     *
     * @param  string $value
     * @return self   Provides fluent interface
     */
    public function setFieldValue($value)
    {
        return $this->setOption('fieldvalue', $value);
    }

    /**
     * Get the field value option
     *
     * @return string
     */
    public function getFieldValue()
    {
        return $this->getOption('fieldvalue');
    }

    /**
     * Set the field type option
     *
     * When present, the text will be analyzed based on the specified type
     *
     * @param  string $type
     * @return self   Provides fluent interface
     */
    public function setFieldType($type)
    {
        return $this->setOption('fieldtype', $type);
    }

    /**
     * Get the fieldtype option
     *
     * @return string
     */
    public function getFieldType()
    {
        return $this->getOption('fieldtype');
    }

    /**
     * Set the field name option
     *
     * When present, the text will be analyzed based on the type of this field name
     *
     * @param  string $name
     * @return self   Provides fluent interface
     */
    public function setFieldName($name)
    {
        return $this->setOption('fieldname', $name);
    }

    /**
     * Get the fieldname option
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->getOption('fieldname');
    }
}
