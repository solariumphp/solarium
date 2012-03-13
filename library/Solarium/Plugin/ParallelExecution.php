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
 * @subpackage Plugin
 */

/**
 * @namespace
 */
namespace Solarium\Plugin;
use Solarium\Core\Plugin;
use Solarium\Core\Client\HttpException;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\Query;

/**
 * ParallelExecution plugin
 *
 * You can use this plugin to run multiple queries parallel. This functionality depends on the curl adapter so you
 * do need to have curl available in your PHP environment.
 *
 * While query execution is parallel, the results only become available as soon as all requests have finished. So the
 * time of the slowest query will be the effective execution time for all queries.
 *
 * @package Solarium
 * @subpackage Plugin
 */

// @codeCoverageIgnoreStart
class ParallelExecution extends Plugin
{

    /**
     * Default options
     *
     * @var array
     */
    protected $options = array(
        'curlmultiselecttimeout' => 0.1,
    );

    /**
     * Queries (and optionally clients) to execute
     *
     * @var array
     */
    protected $queries = array();

    /**
     * Set curl adapter (the only type that supports parallelexecution)
     */
    protected function initPluginType()
    {
        $this->client->setAdapter('Solarium\Client\Adapter\Curl');
    }

    /**
     * Add a query to execute
     *
     * @param string $key
     * @param Query $query
     * @param null|string|Endpoint $endpoint
     * @return self Provides fluent interface
     */
    public function addQuery($key, $query, $endpoint = null)
    {
        if (is_object($endpoint)) $endpoint = $endpoint->getKey();

        if($endpoint == null) $endpoint = $this->client->getEndpoint()->getKey();

        $this->queries[$key] = array(
            'query' => $query,
            'endpoint' => $endpoint,
        );

        return $this;
    }

    /**
     * Get queries (and coupled client instances)
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * Clear all queries
     *
     * @return self Provides fluent interface
     */
    public function clearQueries()
    {
        $this->queries = array();
        return $this;
    }

    // @codeCoverageIgnoreStart

    /**
     * Execute queries parallel
     *
     * @return array
     */
    public function execute()
    {
        // create handles and add all handles to the multihandle
        $adapter = $this->client->getAdapter();
        $multiHandle = curl_multi_init();
        $handles = array();
        foreach ($this->queries as $key => $data) {
            $request = $this->client->createRequest($data['query']);
            $endpoint = $this->client->getEndpoint($data['endpoint']);
            $handle = $adapter->createHandle($request, $endpoint);
            curl_multi_add_handle($multiHandle, $handle);
            $handles[$key] = $handle;
        }

        // executing multihandle (all requests)
        $this->client->triggerEvent('ParallelExecutionStart');

        do {
            $mrc = curl_multi_exec($multiHandle, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        $timeout = $this->getOption('curlmultiselecttimeout');
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multiHandle, $timeout) != -1) {
                do {
                    $mrc = curl_multi_exec($multiHandle, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        $this->client->triggerEvent('ParallelExecutionEnd');

        // get the results
        $results = array();
        foreach ($handles as $key => $handle) {
            try {
                curl_multi_remove_handle($multiHandle, $handle);
                $response = $adapter->getResponse($handle, curl_multi_getcontent($handle));
                $results[$key] = $this->client->createResult($this->queries[$key]['query'], $response);
            } catch(HttpException $e) {

                $results[$key] = $e;
            }
        }

        curl_multi_close($multiHandle);

        return $results;
    }

    // @codeCoverageIgnoreEnd
}
