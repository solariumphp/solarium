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
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\Core\Client;

use Solarium\Exception\HttpException;

/**
 * Class for describing a response.
 */
class Response
{
    /**
     * Headers.
     *
     * @var array
     */
    protected $headers;

    /**
     * Body.
     *
     * @var string
     */
    protected $body;

    /**
     * HTTP response code.
     *
     * @var int
     */
    protected $statusCode;

    /**
     * HTTP response message.
     *
     * @var string
     */
    protected $statusMessage;

    /**
     * Constructor.
     *
     * @param string $body
     * @param array  $headers
     */
    public function __construct($body, $headers = array())
    {
        $this->body = $body;
        $this->headers = $headers;

        $this->setHeaders($headers);
    }

    /**
     * Get body data.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get response headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get status message.
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * Set headers.
     *
     * @throws HttpException
     *
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        // get the status header
        $statusHeader = null;
        foreach ($headers as $header) {
            if (substr($header, 0, 4) == 'HTTP') {
                $statusHeader = $header;
                break;
            }
        }

        if (null === $statusHeader) {
            throw new HttpException("No HTTP status found");
        }

        // parse header like "$statusInfo[1]" into code and message
        // $statusInfo[1] = the HTTP response code
        // $statusInfo[2] = the response message
        $statusInfo = explode(' ', $statusHeader, 3);
        $this->statusCode = $statusInfo[1];
        $this->statusMessage = $statusInfo[2];
    }
}
