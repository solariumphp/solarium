<?php

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Configurable;
use Solarium\Exception\HttpException;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;

/**
 * Pecl HTTP adapter.
 *
 * @author Gasol Wu <gasol.wu@gmail.com>
 */
class PeclHttp extends Configurable implements AdapterInterface
{
    /**
     * Execute a Solr request using the Pecl Http.
     *
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @throws HttpException
     *
     * @return Response
     */
    public function execute($request, $endpoint)
    {
        $httpRequest = $this->toHttpRequest($request, $endpoint);

        try {
            $httpMessage = $httpRequest->send();
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage());
        }

        return new Response(
            $httpMessage->getBody(),
            $this->toRawHeaders($httpMessage)
        );
    }

    /**
     * adapt Request to HttpRequest.
     *
     * {@link http://us.php.net/manual/en/http.constants.php
     *  HTTP Predefined Constant}
     *
     * {@link http://us.php.net/manual/en/http.request.options.php
     *  HttpRequest options}
     *
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @throws InvalidArgumentException
     *
     * @return \HttpRequest
     */
    public function toHttpRequest($request, $endpoint)
    {
        $baseUri = $request->getIsServerRequest() ? $endpoint->getServerUri() : $endpoint->getCoreBaseUri();
        $url = $baseUri.$request->getUri();
        $httpRequest = new \HttpRequest($url);

        $headers = [];
        foreach ($request->getHeaders() as $headerLine) {
            list($header, $value) = explode(':', $headerLine);
            if ($header = trim($header)) {
                $headers[$header] = trim($value);
            }
        }

        // Try endpoint authentication first, fallback to request for backwards compatibility
        $authData = $endpoint->getAuthentication();
        if (empty($authData['username'])) {
            $authData = $request->getAuthentication();
        }

        if (!empty($authData['username']) && !empty($authData['password'])) {
            $headers['Authorization'] = 'Basic '.base64_encode($authData['username'].':'.$authData['password']);
        }

        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                $method = HTTP_METH_GET;
                break;
            case Request::METHOD_POST:
                $method = HTTP_METH_POST;
                if ($request->getFileUpload()) {
                    $httpRequest->addPostFile(
                        'content',
                        $request->getFileUpload(),
                        'application/octet-stream; charset=binary'
                    );
                } else {
                    $httpRequest->setBody($request->getRawData());
                    if (!isset($headers['Content-Type'])) {
                        $headers['Content-Type'] = 'text/xml; charset=utf-8';
                    }
                }
                break;
            case Request::METHOD_HEAD:
                $method = HTTP_METH_HEAD;
                break;
            case Request::METHOD_DELETE:
                $method = HTTP_METH_DELETE;
                break;
            case Request::METHOD_PUT:
                $method = HTTP_METH_PUT;
                if ($request->getFileUpload()) {
                    $httpRequest->addPostFile(
                        'content',
                        $request->getFileUpload(),
                        'application/octet-stream; charset=binary'
                    );
                } else {
                    $httpRequest->setBody($request->getRawData());
                    if (!isset($headers['Content-Type'])) {
                        $headers['Content-Type'] = 'application/json; charset=utf-8';
                    }
                }
                break;
            default:
                throw new InvalidArgumentException(
                    'Unsupported method: '.$request->getMethod()
                );
        }

        $httpRequest->setMethod($method);
        $httpRequest->setOptions(
            [
                'timeout' => $endpoint->getTimeout(),
                'connecttimeout' => $endpoint->getTimeout(),
                'dns_cache_timeout' => $endpoint->getTimeout(),
            ]
        );
        $httpRequest->setHeaders($headers);

        return $httpRequest;
    }

    /**
     * Initialization hook.
     *
     * Checks the availability of pecl_http
     *
     * @throws RuntimeException
     */
    protected function init()
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('HttpRequest', false)) {
            throw new RuntimeException('Pecl_http is not available, install it to use the PeclHttp adapter');
        }

        parent::init();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Convert key/value pair header to raw header.
     *
     * <code>
     * //before
     * $headers['Content-Type'] = 'text/plain';
     *
     * ...
     *
     * //after
     * $headers[0] = 'Content-Type: text/plain';
     * </code>
     *
     * @param $message \HttpMessage
     *
     * @return array
     */
    protected function toRawHeaders($message)
    {
        $headers[] = 'HTTP/'.$message->getHttpVersion().' '.$message->getResponseCode().' '.$message->getResponseStatus();

        foreach ($message->getHeaders() as $header => $value) {
            $headers[] = "$header: $value";
        }

        return $headers;
    }
}
