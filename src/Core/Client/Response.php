<?php

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
    public function __construct($body, $headers = [])
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
     *
     * @param array $headers
     *
     * @throws HttpException
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        // get the status header
        $statusHeader = null;
        foreach ($headers as $header) {
            if ('HTTP' == substr($header, 0, 4)) {
                $statusHeader = $header;
                break;
            }
        }

        if (null === $statusHeader) {
            throw new HttpException('No HTTP status found');
        }

        // parse header like "$statusInfo[1]" into code and message
        // $statusInfo[1] = the HTTP response code
        // $statusInfo[2] = the response message
        $statusInfo = explode(' ', $statusHeader, 3);
        $this->statusCode = (int) $statusInfo[1];
        $this->statusMessage = $statusInfo[2];
    }
}
