<?php

namespace Solarium\QueryType\RealtimeGet;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\QueryType\Select\ResponseParser;

/**
 * Get query.
 *
 * Realtime Get query for one or multiple document IDs
 *
 * @see http://wiki.apache.org/solr/RealTimeGet
 */
class Query extends BaseQuery
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'resultclass' => 'Solarium\QueryType\RealtimeGet\Result',
        'documentclass' => 'Solarium\QueryType\Select\Result\Document',
        'handler' => 'get',
        'omitheader' => true,
    ];

    /**
     * Document IDs.
     *
     * @var array
     */
    protected $ids = [];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_REALTIME_GET;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * The ping query has no response parser so we return a null value.
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser();
    }

    /**
     * Add an id.
     *
     * @param string $id
     *
     * @return self Provides fluent interface
     */
    public function addId($id)
    {
        $this->ids[$id] = true;

        return $this;
    }

    /**
     * Add multiple ids.
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
     * Remove an id.
     *
     * @param string $id
     *
     * @return self Provides fluent interface
     */
    public function removeId($id)
    {
        if (isset($this->ids[$id])) {
            unset($this->ids[$id]);
        }

        return $this;
    }

    /**
     * Remove all IDs.
     *
     * @return self Provides fluent interface
     */
    public function clearIds()
    {
        $this->ids = [];

        return $this;
    }

    /**
     * Get the list of ids.
     *
     * @return array
     */
    public function getIds()
    {
        return array_keys($this->ids);
    }

    /**
     * Set multiple ids.
     *
     * This overwrites any existing ids
     *
     * @param array $ids
     *
     * @return self Provides fluent interface
     */
    public function setIds($ids)
    {
        $this->clearIds();
        $this->addIds($ids);

        return $this;
    }

    /**
     * Set a custom document class.
     *
     * This class should implement the document interface
     *
     * @param string $value classname
     *
     * @return self Provides fluent interface
     */
    public function setDocumentClass($value)
    {
        return $this->setOption('documentclass', $value);
    }

    /**
     * Get the current documentclass option.
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
     * No components for this querytype.
     *
     * @return array
     */
    public function getComponents()
    {
        return [];
    }
}
