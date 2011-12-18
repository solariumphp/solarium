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
 * @subpackage Result
 */

/**
 * @namespace
 */
namespace Solarium\Result;

/**
 * Query result
 *
 * This base class provides access to the response and decoded data. If you need more functionality
 * like resultset parsing use one of the subclasses
 *
 * @package Solarium
 * @subpackage Result
 */
class Result
{

    /**
     * Response object
     *
     * @var Solarium\Client\Response
     */
    protected $_response;

    /**
     * Decode response data
     *
     * This is lazy loaded, {@link getData()}
     *
     * @var array
     */
    protected $_data;

    /**
     * Query used for this request
     *
     * @var Solarium\Query
     */
    protected $_query;

    /**
     * @var Solarium\Client
     */
    protected $_client;

    /**
     * Constructor
     *
     * @param Solarium\Client $client
     * @param Solarium\Query $query
     * @param Solarium\Client\Response $response
     * @return void
     */
    public function __construct($client, $query, $response)
    {
        $this->_client = $client;
        $this->_query = $query;
        $this->_response = $response;

        // check status for error (range of 400 and 500)
        $statusNum = floor($response->getStatusCode() / 100);
        if ($statusNum == 4 || $statusNum == 5) {
            throw new \Solarium\Client\HttpException(
                $response->getStatusMessage(),
                $response->getStatusCode()
            );
        }
    }

    /**
     * Get response object
     *
     * This is the raw HTTP response object, not the parsed data!
     *
     * @return Solarium\Client\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Get query instance
     *
     * @return Solarium\Query
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Get Solr response data
     *
     * Includes a lazy loading mechanism: JSON body data is decoded on first use and then saved for reuse.
     *
     * @return array
     */
    public function getData()
    {
        if (null == $this->_data) {
            $this->_data = json_decode($this->_response->getBody(), true);
            if (null === $this->_data) {
                throw new \Solarium\Exception(
                    'Solr JSON response could not be decoded'
                );
            }
        }

        return $this->_data;
    }
}