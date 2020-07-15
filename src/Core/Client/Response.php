<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
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
    public function __construct(string $body, array $headers = [])
    {
        $this->body = $body;
        if ($headers) {
            $this->setHeaders($headers);
        }
    }

    /**
     * Get body data.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Get response headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get status message.
     *
     * @return string
     */
    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    /**
     * Set headers.
     *
     * @param array $headers
     *
     * @throws HttpException
     *
     * @return self Provides fluent interface
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        // get the status header
        $statusHeader = null;
        foreach ($headers as $header) {
            if (0 === strpos($header, 'HTTP')) {
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

        return $this;
    }
}
