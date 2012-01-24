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

// @codeCoverageIgnoreStart
class Solarium_Plugin_ParallelExecution extends Solarium_Plugin_Abstract
{

    /**
     * Execute queries parallel
     *
     * Use an array of Solarium_Query objects as input. The keys of the array are important, as they are also used in
     * the result array. You can mix all querytypes in the input array.
     *
     * @param array $queries
     * @return array
     */
    public function execute($queries)
    {
        $adapter = $this->_client->setAdapter('Solarium_Client_Adapter_Curl')->getAdapter();

        // create handles and add all handles to the multihandle
        $multiHandle = curl_multi_init();
        $handles = array();
        foreach ($queries as $key => $query) {
            $request = $this->_client->createRequest($query);
            $handle = $adapter->createHandle($request);
            curl_multi_add_handle($multiHandle, $handle);
            $handles[$key] = $handle;
        }

        // executing multihandle (all requests)
        $this->_client->triggerEvent('ParallelExecutionStart');
        do {
            curl_multi_exec($multiHandle, $running);
        } while($running > 0);
        $this->_client->triggerEvent('ParallelExecutionEnd');

        // get the results
        $results = array();
        foreach ($handles as $key => $handle) {
            try {
                curl_multi_remove_handle($multiHandle, $handle);
                $response = $adapter->getResponse($handle, curl_multi_getcontent($handle));
                $results[$key] = $this->_client->createResult($queries[$key], $response);
            } catch(Solarium_Client_HttpException $e) {
                $results[$key] = $e;
            }
        }

        curl_multi_close($multiHandle);

        return $results;
    }

}
// @codeCoverageIgnoreEnd