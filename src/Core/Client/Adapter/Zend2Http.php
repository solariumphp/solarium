<?php

namespace Solarium\Core\Client\Adapter;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Configurable;
use Solarium\Core\ConfigurableInterface;
use Solarium\Exception\HttpException;
use Solarium\Exception\OutOfBoundsException;

/**
 * Adapter that uses a ZF2 Zend\Http\Client.
 *
 * The Zend Framework HTTP client has many great features and has lots of
 * configuration options. For more info see the manual at
 * {@link http://framework.zend.com/manual/en/zend.http.html}
 *
 * To use this adapter you need to have the Zend Framework available (autoloading)
 */
class Zend2Http extends Configurable implements AdapterInterface
{
    /**
     * Zend Http instance for communication with Solr.
     *
     * @var \Zend\Http\Client
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
     * Zend\Http\Client. See the Zend\Http\Client docs for the many config
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
    public function setOptions($options, bool $overwrite = false): ConfigurableInterface
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

            $this->zendHttp->setOptions($adapterOptions);
        }

        return $this;
    }

    /**
     * Set the Zend\Http\Client instance.
     *
     * This method is optional, if you don't set a client it will be created
     * upon first use, using default and/or custom options (the most common use
     * case)
     *
     * @param \Zend\Http\Client $zendHttp
     *
     * @return self Provides fluent interface
     */
    public function setZendHttp($zendHttp)
    {
        $this->zendHttp = $zendHttp;

        return $this;
    }

    /**
     * Get the Zend\Http\Client instance.
     *
     * If no instance is available yet it will be created automatically based on
     * options.
     *
     * You can use this method to get a reference to the client instance to set
     * options, get the last response and use many other features offered by the
     * Zend\Http\Client API.
     *
     * @return \Zend\Http\Client
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

            $this->zendHttp = new \Zend\Http\Client(null, $options);
        }

        return $this->zendHttp;
    }

    /**
     * Execute a Solr request using the Zend\Http\Client instance.
     *
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @throws HttpException
     * @throws OutOfBoundsException
     *
     * @return Response
     */
    public function execute(Request $request, Endpoint $endpoint): Response
    {
        $client = $this->getZendHttp();
        $client->resetParameters();

        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                $client->setMethod('GET');
                break;
            case Request::METHOD_POST:
                $client->setMethod('POST');
                if ($request->getFileUpload()) {
                    $this->prepareFileUpload($client, $request);
                } else {
                    $client->setRawBody($request->getRawData());
                    $request->addHeader('Content-Type: text/xml; charset=UTF-8');
                }
                break;
            case Request::METHOD_HEAD:
                $client->setMethod('HEAD');
                break;
            case Request::METHOD_DELETE:
                $client->setMethod('DELETE');
                break;
            case Request::METHOD_PUT:
                $client->setMethod('PUT');
                if ($request->getFileUpload()) {
                    $this->prepareFileUpload($client, $request);
                } else {
                    $client->setRawBody($request->getRawData());
                    $request->addHeader('Content-Type: application/json; charset=UTF-8');
                }
                break;
            default:
                throw new OutOfBoundsException('Unsupported method: '.$request->getMethod());
                break;
        }

        $uri = AdapterHelper::buildUri($request, $endpoint);

        $client->setUri($uri);
        $client->setHeaders($request->getHeaders());
        $this->timeout = $endpoint->getTimeout();

        $response = $client->send();

        return $this->prepareResponse(
            $request,
            $response
        );
    }

    /**
     * Prepare a solarium response from the given request and client
     * response.
     *
     * @param Request             $request
     * @param \Zend\Http\Response $response
     *
     * @throws HttpException
     *
     * @return Response
     */
    protected function prepareResponse(Request $request, \Zend\Http\Response $response)
    {
        if ($response->isClientError()) {
            throw new HttpException(
                $response->getReasonPhrase(),
                $response->getStatusCode()
            );
        }

        if (Request::METHOD_HEAD == $request->getMethod()) {
            $data = '';
        } else {
            $data = $response->getBody();
        }

        // this is used because in ZF2 status line isn't in the headers anymore
        $headers = [$response->renderStatusLine()];

        return new Response($data, $headers);
    }

    /**
     * Prepare the client to send the file and params in request.
     *
     * @param \Zend\Http\Client $client
     * @param Request           $request
     */
    protected function prepareFileUpload($client, $request)
    {
        $data = AdapterHelper::buildUploadBodyFromRequest($request);
        $client->setRawBody($data);
    }
}
