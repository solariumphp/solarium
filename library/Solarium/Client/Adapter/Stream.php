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
 * A very basic adapter using file_get_contents for retrieving data from Solr
 */
class Solarium_Client_Adapter_Stream extends Solarium_Client_Adapter
{

    /**
     * Execute a select query and return a result object
     *
     * @param Solarium_Query_Select $query
     * @return Solarium_Result_Select
     */
    public function select($query)
    {
        $data = $this->_getSolrData(
            new Solarium_Client_Request_Select($this->_options, $query)
        );

        $documentClass = $query->getOption('documentclass');
        $documents = array();
        if (isset($data['response']['docs'])) {
            foreach ($data['response']['docs'] AS $doc) {
                $fields = (array)$doc;
                $documents[] = new $documentClass($fields);
            }
        }

        $numFound = $data['response']['numFound'];
        
        $resultClass = $query->getOption('resultclass');
        return new $resultClass($numFound, $documents);

    }

    /**
     * Execute a ping query and return a result object
     *
     * @param Solarium_Query_Ping $query
     * @return Solarium_Result_Ping
     */
    public function ping($query)
    {
        $this->_getSolrData(
            new Solarium_Client_Request($this->_options, $query)
        );

        $resultClass = $query->getOption('resultclass');

        return new $resultClass;
    }

    /**
     * Execute an update query and return a result object
     *
     * @param Solarium_Query_Update $query
     * @return Solarium_Result_Update
     */
    public function update($query)
    {
        $data = $this->_getSolrData(
            new Solarium_Client_Request_Update($this->_options, $query)
        );
        
        $resultClass = $query->getOption('resultclass');

        return new $resultClass(
            $data['responseHeader']['status'],
            $data['responseHeader']['QTime']
        );
    }

    /**
     * Handle Solr communication and JSON decode
     *
     * @throws Solarium_Exception
     * @param Solarium_Client_Request
     * @return array
     */
    protected function _getSolrData($request)
    {
        if (null !== $request && null !== $request->getPostData()) {
            $context = stream_context_create(
                array(
                    'http' => array(
                        'method' => 'POST',
                        'content' => $request->getPostData(),
                        'header' => 'Content-Type: text/xml; charset=UTF-8',
                    ),
                )
            );
        } else {
            $context = null;
        }
        
        $data = @file_get_contents($request->getUrl(), false, $context);
        if (false === $data) {
            $error = error_get_last();
            throw new Solarium_Exception($error['message']);
        }

        $data = json_decode($data, true);
        if (null === $data) {
            throw new Solarium_Exception(
                'Solr JSON response could not be decoded');
        }

        return $data;
    }

}