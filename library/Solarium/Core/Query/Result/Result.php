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
namespace Solarium\Core\Query\Result;

use Solarium\Core\Client\Client;
use Solarium\Core\Client\Response;
use Solarium\Exception\HttpException;
use Solarium\Core\Query\Query;
use Solarium\Exception\UnexpectedValueException;
use Solarium\Exception\RuntimeException;

/**
 * Query result
 *
 * This base class provides access to the response and decoded data. If you need more functionality
 * like resultset parsing use one of the subclasses
 */
class Result implements ResultInterface
{
    /**
     * Response object
     *
     * @var Response
     */
    protected $response;

    /**
     * Decoded response data
     *
     * This is lazy loaded, {@link getData()}
     *
     * @var array
     */
    protected $data;

    /**
     * Query used for this request
     *
     * @var Query
     */
    protected $query;

    /**
     * Solarium client instance
     *
     * @var Client
     */
    protected $client;

    /**
     * Constructor
     *
     * @throws HttpException
     * @param  Client        $client
     * @param  Query         $query
     * @param  Response      $response
     */
    public function __construct($client, $query, $response)
    {
        $this->client = $client;
        $this->query = $query;
        $this->response = $response;

        // check status for error (range of 400 and 500)
        $statusNum = floor($response->getStatusCode() / 100);
        if ($statusNum == 4 || $statusNum == 5) {
            throw new HttpException(
                $response->getStatusMessage(),
                $response->getStatusCode(),
                $response->getBody()
            );
        }
    }

    /**
     * Get response object
     *
     * This is the raw HTTP response object, not the parsed data!
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get query instance
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get Solr response data
     *
     * Includes a lazy loading mechanism: JSON body data is decoded on first use and then saved for reuse.
     *
     * @throws UnexpectedValueException
     * @throws RuntimeException
     * @return array
     */
    public function getData()
    {
        if (null == $this->data) {

            switch ($this->query->getResponseWriter()) {
                case Query::WT_PHPS:
                    $this->data = unserialize($this->response->getBody());
                    break;
                case Query::WT_JSON:
                    $this->data = json_decode($this->response->getBody(), true);
                    break;
                default:
                    throw new RuntimeException('Responseparser cannot handle ' . $this->query->getResponseWriter());
            }

            if (null === $this->data) {
                throw new UnexpectedValueException(
                    'Solr JSON response could not be decoded'
                );
            }
        }

        return $this->data;
    }
}
