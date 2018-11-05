<?php

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Configurable;
use Solarium\Exception\HttpException;
use Solarium\Exception\OutOfBoundsException;

/**
 * Adapter that uses a Zend_Http_Client.
 *
 * The Zend Framework HTTP client has many great features and has lots of
 * configuration options. For more info see the manual at
 * {@link http://framework.zend.com/manual/en/zend.http.html}
 *
 * To use this adapter you need to have the Zend Framework available (autoloading)
 */
class ZendHttp extends Configurable implements AdapterInterface
{
    /**
     * Zend Http instance for communication with Solr.
     *
     * @var \Zend_Http_Client
     */
    protected $zendHttp;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * Set options.
     *
     * Overrides any existing values.
     *
     * If the options array has an 'options' entry it is forwarded to the
     * Zend_Http_Client. See the Zend_Http_Clientdocs for the many config
     * options available.
     *
     * The $options param should be an array or an object that has a toArray
     * method, like Zend_Config
     *
     * @param array|object $options
     * @param bool         $overwrite
     *
     * @return self Provides fluent interface
     */
    public function setOptions($options, $overwrite = false)
    {
        parent::setOptions($options, $overwrite);

        // forward options to zendHttp instance
        if (null !== $this->zendHttp) {
            // forward timeout setting
            $adapterOptions = [];

            // forward adapter options if available
            if (isset($this->options['options'])) {
                $adapterOptions = array_merge($adapterOptions, $this->options['options']);
            }

            $this->zendHttp->setConfig($adapterOptions);
        }

        return $this;
    }

    /**
     * Set the Zend_Http_Client instance.
     *
     * This method is optional, if you don't set a client it will be created
     * upon first use, using default and/or custom options (the most common use
     * case)
     *
     * @param \Zend_Http_Client $zendHttp
     *
     * @return self Provides fluent interface
     */
    public function setZendHttp($zendHttp)
    {
        $this->zendHttp = $zendHttp;

        return $this;
    }

    /**
     * Get the Zend_Http_Client instance.
     *
     * If no instance is available yet it will be created automatically based on
     * options.
     *
     * You can use this method to get a reference to the client instance to set
     * options, get the last response and use many other features offered by the
     * Zend_Http_Client API.
     *
     * @return \Zend_Http_Client
     */
    public function getZendHttp()
    {
        if (null === $this->zendHttp) {
            $options = [];

            // forward zendhttp options
            if (isset($this->options['options'])) {
                $options = array_merge(
                    $options,
                    $this->options['options']
                );
            }

            $this->zendHttp = new \Zend_Http_Client(null, $options);
        }

        return $this->zendHttp;
    }

    /**
     * Execute a Solr request using the Zend_Http_Client instance.
     *
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @throws HttpException
     * @throws OutOfBoundsException
     *
     * @return Response
     */
    public function execute($request, $endpoint)
    {
        $client = $this->getZendHttp();
        $client->resetParameters();

        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                $client->setMethod(\Zend_Http_Client::GET);
                $client->setParameterGet($request->getParams());
                break;
            case Request::METHOD_POST:
                $client->setMethod(\Zend_Http_Client::POST);
                if ($request->getFileUpload()) {
                    $this->prepareFileUpload($client, $request);
                } else {
                    $client->setParameterGet($request->getParams());
                    $client->setRawData($request->getRawData());
                    $request->addHeader('Content-Type: text/xml; charset=UTF-8');
                }
                break;
            case Request::METHOD_HEAD:
                $client->setMethod(\Zend_Http_Client::HEAD);
                $client->setParameterGet($request->getParams());
                break;
            case Request::METHOD_DELETE:
                $client->setMethod(\Zend_Http_Client::DELETE);
                $client->setParameterGet($request->getParams());
                break;
            case Request::METHOD_PUT:
                $client->setMethod(\Zend_Http_Client::PUT);
                $client->setParameterGet($request->getParams());
                if ($request->getFileUpload()) {
                    $this->prepareFileUpload($client, $request);
                } else {
                    $client->setParameterGet($request->getParams());
                    $client->setRawData($request->getRawData());
                    $request->addHeader('Content-Type: application/json; charset=UTF-8');
                }
                break;
            default:
                throw new OutOfBoundsException('Unsupported method: '.$request->getMethod());
                break;
        }

        $baseUri = $request->getIsServerRequest() ? $endpoint->getServerUri() : $endpoint->getCoreBaseUri();
        $uri = $baseUri.$request->getUri();
        $client->setUri($uri);
        $client->setHeaders($request->getHeaders());
        $this->timeout = $endpoint->getTimeout();

        $response = $client->request();

        return $this->prepareResponse(
            $request,
            $response
        );
    }

    /**
     * Prepare a solarium response from the given request and client
     * response.
     *
     *
     * @param Request             $request
     * @param \Zend_Http_Response $response
     *
     * @throws HttpException
     *
     * @return Response
     */
    protected function prepareResponse($request, $response)
    {
        if ($response->isError()) {
            throw new HttpException(
                $response->getMessage(),
                $response->getStatus()
            );
        }

        if (Request::METHOD_HEAD == $request->getMethod()) {
            $data = '';
        } else {
            $data = $response->getBody();
        }

        // this is used because getHeaders doesn't return the HTTP header...
        $headers = explode("\n", $response->getHeadersAsString());

        return new Response($data, $headers);
    }

    /**
     * Prepare the client to send the file and params in request.
     *
     * @param \Zend_Http_Client $client
     * @param Request           $request
     */
    protected function prepareFileUpload($client, $request)
    {
        $filename = $request->getFileUpload();
        $client->setFileUpload(
            'content',
            'content',
            file_get_contents($filename),
            'application/octet-stream; charset=binary'
        );
    }
}
