<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Exception;

/**
 * Solarium client HTTP exception.
 *
 * This exception class exists to make it easy to catch HTTP errors.
 * HTTP errors usually mean your Solr settings or Solr input (e.g. query)
 * contain an error.
 *
 * The getMessage method returns an error description that includes the status
 * message and code.
 *
 * The getCode method will return the HTTP response code returned by the server
 * (if available).
 *
 * The getStatusMessage method will return the HTTP status message.
 */
class HttpException extends \RuntimeException implements RuntimeExceptionInterface
{
    /**
     * HTTP status message.
     *
     * @var string
     */
    protected $statusMessage;

    /**
     * HTTP response body.
     *
     * Usually contains a description of the error (if Solr returned one)
     *
     * @var string
     */
    protected $body;

    /**
     * Exception constructor.
     *
     * The input message is a HTTP status message. Because an exception with the
     * message 'Not Found' is not very clear this message is transformed to a
     * more descriptive text. The original message is available using the
     * {@link getStatusMessage} method.
     *
     * @param string      $statusMessage
     * @param int|null    $code
     * @param string|null $body
     */
    public function __construct(string $statusMessage, int $code = null, string $body = null)
    {
        $this->statusMessage = $statusMessage;
        $this->body = $body;

        $message = 'Solr HTTP error: '.$statusMessage;
        if (null !== $code) {
            $message .= ' ('.$code.')';
        }
        if ($body) {
            $message .= "\n".$body;
        }

        parent::__construct($message, $code ?? 0);
    }

    /**
     * Get the HTTP status message.
     *
     * @return string
     */
    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }
}
