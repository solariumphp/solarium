<?php

namespace Solarium\Core\Client;

use Solarium\Component\RequestBuilder\RequestParamsInterface;
use Solarium\Component\RequestBuilder\RequestParamsTrait;
use Solarium\Core\Configurable;
use Solarium\Exception\RuntimeException;

/**
 * Class for describing a request.
 */
class Request extends Configurable implements RequestParamsInterface
{
    use RequestParamsTrait;

    /**
     * Request GET method.
     */
    const METHOD_GET = 'GET';

    /**
     * Request POST method.
     */
    const METHOD_POST = 'POST';

    /**
     * Request HEAD method.
     */
    const METHOD_HEAD = 'HEAD';

    /**
     * Request DELETE method.
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * Request PUT method.
     */
    const METHOD_PUT = 'PUT';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'method' => self::METHOD_GET,
    ];

    /**
     * Request headers.
     */
    protected $headers = [];

    /**
     * Raw POST data.
     *
     * @var string
     */
    protected $rawData;

    /**
     * Magic method enables a object to be transformed to a string.
     *
     * Get a summary showing significant variables in the object
     * note: uri resource is decoded for readability
     *
     * @return string
     */
    public function __toString()
    {
        $output = __CLASS__.'::__toString'."\n".'method: '.$this->getMethod()."\n".'header: '.print_r($this->getHeaders(), 1).'authentication: '.print_r($this->getAuthentication(), 1).'resource: '.$this->getUri()."\n".'resource urldecoded: '.urldecode($this->getUri())."\n".'raw data: '.$this->getRawData()."\n".'file upload: '.$this->getFileUpload()."\n";

        return $output;
    }

    /**
     * Set request handler.
     *
     * @param string $handler
     *
     * @return self Provides fluent interface
     */
    public function setHandler($handler)
    {
        $this->setOption('handler', $handler);

        return $this;
    }

    /**
     * Get request handler.
     *
     * @return string
     */
    public function getHandler()
    {
        return $this->getOption('handler');
    }

    /**
     * Set request method.
     *
     * Use one of the constants as value
     *
     * @param string $method
     *
     * @return self Provides fluent interface
     */
    public function setMethod($method)
    {
        $this->setOption('method', $method);

        return $this;
    }

    /**
     * Get request method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getOption('method');
    }

    /**
     * Get raw POST data.
     *
     * @return string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * Set raw POST data.
     *
     * This string must be safely encoded.
     *
     * @param string $data
     *
     * @return self Provides fluent interface
     */
    public function setRawData($data)
    {
        $this->rawData = $data;

        return $this;
    }

    /**
     * Get the file to upload via "multipart/form-data" POST request.
     *
     * @return string|null
     */
    public function getFileUpload()
    {
        return $this->getOption('file');
    }

    /**
     * Set the file to upload via "multipart/form-data" POST request.
     *
     *
     * @param string $filename Name of file to upload
     *
     * @throws RuntimeException
     *
     * @return self
     */
    public function setFileUpload($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new RuntimeException("Unable to read file '{$filename}' for upload");
        }

        $this->setOption('file', $filename);

        return $this;
    }

    /**
     * Get all request headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set request headers.
     *
     * @param array $headers
     *
     * @return self Provides fluent interface
     */
    public function setHeaders($headers)
    {
        $this->clearHeaders();
        $this->addHeaders($headers);

        return $this;
    }

    /**
     * Add a request header.
     *
     * @param string|array $value
     *
     * @return self Provides fluent interface
     */
    public function addHeader($value)
    {
        $this->headers[] = $value;

        return $this;
    }

    /**
     * Add multiple headers to the request.
     *
     * @param array $headers
     *
     * @return self Provides fluent interface
     */
    public function addHeaders($headers)
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }

        return $this;
    }

    /**
     * Clear all request headers.
     *
     * @return self Provides fluent interface
     */
    public function clearHeaders()
    {
        $this->headers = [];

        return $this;
    }

    /**
     * Get an URI for this request.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->getHandler().'?'.$this->getQueryString();
    }

    /**
     * Set HTTP basic auth settings.
     *
     * If one or both values are NULL authentication will be disabled
     *
     * @param string $username
     * @param string $password
     *
     * @return self Provides fluent interface
     */
    public function setAuthentication($username, $password)
    {
        $this->setOption('username', $username);
        $this->setOption('password', $password);

        return $this;
    }

    /**
     * Get HTTP basic auth settings.
     *
     * @return array
     */
    public function getAuthentication()
    {
        return [
            'username' => $this->getOption('username'),
            'password' => $this->getOption('password'),
        ];
    }

    /**
     * Execute a request outside of the core context in the global solr context.
     *
     * @param bool $isServerRequest
     */
    public function setIsServerRequest($isServerRequest = false)
    {
        $this->setOption('isserverrequest', $isServerRequest);
    }

    /**
     * Indicates if a request is core independent and could be executed outside a core context.
     * By default a Request is not core independent and must be executed in the context of a core.
     *
     * @return bool
     */
    public function getIsServerRequest(): bool
    {
        return $this->getOption('isserverrequest') ?? false;
    }

    /**
     * Initialization hook.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'rawdata':
                    $this->setRawData($value);
                    break;
                case 'file':
                    $this->setFileUpload($value);
                    break;
                case 'param':
                    $this->setParams($value);
                    break;
                case 'header':
                    $this->setHeaders($value);
                    break;
                case 'authentication':
                    if (isset($value['username']) && isset($value['password'])) {
                        $this->setAuthentication($value['username'], $value['password']);
                    }
            }
        }
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return spl_object_hash($this);
    }
}
