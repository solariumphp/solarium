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
 * @subpackage Client
 */

/**
 * Solarium client HTTP exception
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
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_HttpException extends Solarium_Exception
{

    /**
     * HTTP status message
     *
     * @var string
     */
    protected $_statusMessage;

    /**
     * Exception constructor
     *
     * The input message is a HTTP status message. Because an exception with the
     * message 'Not Found' is not very clear it this message is tranformed to a
     * more descriptive text. The original message is available using the
     * {@link getStatusMessage} method.
     *
     * @param string $statusMessage
     * @param int|null $code
     */
    public function __construct($statusMessage, $code = null)
    {
        $this->_statusMessage = $statusMessage;

        $message = 'Solr HTTP error: ' . $statusMessage;
        if (null !== $code) {
             $message .= ' (' . $code . ')';
        }

        parent::__construct($message, $code);
    }

    /**
     * Get the HTTP status message
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->_statusMessage;
    }

}