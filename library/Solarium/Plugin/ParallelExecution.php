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
 */

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

class Solarium_Plugin_ParallelExecution extends Solarium_Plugin_Abstract
{

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'curlmultiselecttimeout' => 0.1,
    );

    /**
     * Queries (and optionally clients) to execute
     *
     * @var array
     */
    protected $_queries = array();

    /**
     * Add a query to execute
     *
     * @param string $key
     * @param Solarium_Query $query
     * @param null|Solarium_Client $client
     * @return Solarium_Plugin_ParallelExecution
     */
    public function addQuery($key, $query, $client = null)
    {
        if($client == null) $client = $this->_client;

        $this->_queries[$key] = array(
            'query' => $query,
            'client' => $client,
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
        return $this->_queries;
    }

    /**
     * Clear all queries
     *
     * @return self Provides fluent interface
     */
    public function clearQueries()
    {
        $this->_queries = array();
        return $this;
    }

    // @codeCoverageIgnoreStart

    /**
     * Execute queries parallel
     *
     * Use an array of Solarium_Query objects as input. The keys of the array are important, as they are also used in
     * the result array. You can mix all querytypes in the input array.
     *
     * @param array $queries (deprecated, use addQuery instead)
     * @return array
     */
    public function execute($queries = null)
    {
        // this is for backwards compatibility
        if (is_array($queries)) {
            foreach ($queries as $key => $query) {
                $this->addQuery($key, $query);
            }
        }

        // create handles and add all handles to the multihandle
        $multiHandle = curl_multi_init();
        $handles = array();
        foreach ($this->_queries as $key => $data) {
            $request = $this->_client->createRequest($data['query']);
            $adapter = $data['client']->setAdapter('Solarium_Client_Adapter_Curl')->getAdapter();
            $handle = $adapter->createHandle($request);
            curl_multi_add_handle($multiHandle, $handle);
            $handles[$key] = $handle;
        }

        // executing multihandle (all requests)
        $this->_client->triggerEvent('ParallelExecutionStart');

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

        $this->_client->triggerEvent('ParallelExecutionEnd');

        // get the results
        $results = array();
        foreach ($handles as $key => $handle) {
            try {
                curl_multi_remove_handle($multiHandle, $handle);
                $response = $adapter->getResponse($handle, curl_multi_getcontent($handle));
                $results[$key] = $this->_client->createResult($this->_queries[$key]['query'], $response);
            } catch(Solarium_Client_HttpException $e) {
                $results[$key] = $e;
            }
        }

        curl_multi_close($multiHandle);

        return $results;
    }

    // @codeCoverageIgnoreEnd
}
