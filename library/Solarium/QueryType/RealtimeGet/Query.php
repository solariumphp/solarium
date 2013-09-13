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
namespace Solarium\QueryType\RealtimeGet;

use Solarium\Core\Query\Query as BaseQuery;
use Solarium\Core\Client\Client;
use Solarium\QueryType\RealtimeGet\RequestBuilder as RequestBuilder;
use Solarium\QueryType\Select\ResponseParser\ResponseParser;

/**
 * Get query
 *
 * Realtime Get query for one or multiple document IDs
 *
 * @see http://wiki.apache.org/solr/RealTimeGet
 */
class Query extends BaseQuery
{
    /**
     * Default options
     *
     * @var array
     */
    protected $options = array(
        'resultclass' => 'Solarium\QueryType\RealtimeGet\Result',
        'documentclass' => 'Solarium\QueryType\Select\Result\Document',
        'handler' => 'get',
        'omitheader'    => true,
    );

    /**
     * Document IDs
     *
     * @var array
     */
    protected $ids = array();

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_REALTIME_GET;
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
     * The ping query has no response parser so we return a null value
     *
     * @return null;
     */
    public function getResponseParser()
    {
        return new ResponseParser;
    }

    /**
     * Add an id
     *
     * @param  string $id
     * @return self   Provides fluent interface
     */
    public function addId($id)
    {
        $this->ids[$id] = true;

        return $this;
    }

    /**
     * Add multiple ids
     *
     * @param string|array $ids can be an array or string with comma separated ids
     *
     * @return self Provides fluent interface
     */
    public function addIds($ids)
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        foreach ($ids as $id) {
            $this->addId($id);
        }

        return $this;
    }

    /**
     * Remove an id
     *
     * @param  string $id
     * @return self   Provides fluent interface
     */
    public function removeId($id)
    {
        if (isset($this->ids[$id])) {
            unset($this->ids[$id]);
        }

        return $this;
    }

    /**
     * Remove all IDs
     *
     * @return self Provides fluent interface
     */
    public function clearIds()
    {
        $this->ids = array();

        return $this;
    }

    /**
     * Get the list of ids
     *
     * @return array
     */
    public function getIds()
    {
        return array_keys($this->ids);
    }

    /**
     * Set multiple ids
     *
     * This overwrites any existing ids
     *
     * @param  array $ids
     * @return self  Provides fluent interface
     */
    public function setIds($ids)
    {
        $this->clearIds();
        $this->addIds($ids);

        return $this;
    }

    /**
     * Set a custom document class
     *
     * This class should implement the document interface
     *
     * @param  string $value classname
     * @return self   Provides fluent interface
     */
    public function setDocumentClass($value)
    {
        return $this->setOption('documentclass', $value);
    }

    /**
     * Get the current documentclass option
     *
     * The value is a classname, not an instance
     *
     * @return string
     */
    public function getDocumentClass()
    {
        return $this->getOption('documentclass');
    }

    /**
     * No components for this querytype
     *
     * @return array
     */
    public function getComponents()
    {
        return array();
    }
}
