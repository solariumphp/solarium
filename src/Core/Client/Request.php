<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
     * V1 API.
     */
    const API_V1 = 'v1';

    /**
     * V2 API.
     */
    const API_V2 = 'v2';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'method' => self::METHOD_GET,
        'api' => self::API_V1,
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
     * @param string|null $handler
     *
     * @return self Provides fluent interface
     */
    public function setHandler(?string $handler): self
    {
        $this->setOption('handler', $handler);

        return $this;
    }

    /**
     * Get request handler.
     *
     * @return string|null
     */
    public function getHandler(): ?string
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
    public function setMethod(string $method): self
    {
        $this->setOption('method', $method);

        return $this;
    }

    /**
     * Get request method.
     *
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->getOption('method');
    }

    /**
     * Get raw POST data.
     *
     * @return string|null
     */
    public function getRawData(): ?string
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
    public function setRawData(string $data): self
    {
        $this->rawData = $data;

        return $this;
    }

    /**
     * Get the file to upload via "multipart/form-data" POST request.
     *
     * @return string|null
     */
    public function getFileUpload(): ?string
    {
        return $this->getOption('file');
    }

    /**
     * Set the file to upload via "multipart/form-data" POST request.
     *
     * @param string $filename Name of file to upload
     *
     * @throws RuntimeException
     *
     * @return self Provides fluent interface
     */
    public function setFileUpload($filename): self
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
    public function getHeaders(): array
    {
        return array_unique($this->headers);
    }

    /**
     * @param string $headerName
     *
     * @return string|null
     */
    public function getHeader(string $headerName): ?string
    {
        foreach ($this->headers as $header) {
            list($name) = explode(':', $header);

            if ($name === $headerName) {
                return $header;
            }
        }

        return null;
    }

    /**
     * Set request headers.
     *
     * @param array $headers
     *
     * @return self Provides fluent interface
     */
    public function setHeaders(array $headers): self
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
    public function addHeader($value): self
    {
        $this->headers[] = $value;

        return $this;
    }

    /**
     * Replace header if previously set else add it.
     *
     * @param string $header
     *
     * @return $this
     */
    public function replaceOrAddHeader(string $header): self
    {
        list($name) = explode(':', $header);

        if ((null !== $current = $this->getHeader($name)) &&
            false !== $key = array_search($current, $this->headers, true)
        ) {
            $this->headers[$key] = $header;
        } else {
            $this->headers[] = $header;
        }

        return $this;
    }

    /**
     * Add multiple headers to the request.
     *
     * @param array $headers
     *
     * @return self Provides fluent interface
     */
    public function addHeaders(array $headers): self
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
    public function clearHeaders(): self
    {
        $this->headers = [];

        return $this;
    }

    /**
     * Get an URI for this request.
     *
     * @return string|null
     */
    public function getUri(): ?string
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
    public function setAuthentication(string $username, string $password): self
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
    public function getAuthentication(): array
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
     *
     * @return self Provides fluent interface
     */
    public function setIsServerRequest(bool $isServerRequest = false): self
    {
        $this->setOption('isserverrequest', $isServerRequest);

        return $this;
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
     * Set Solr API version.
     *
     * @param string $api
     *
     * @return self Provides fluent interface
     */
    public function setApi($api): self
    {
        $this->setOption('api', $api);

        return $this;
    }

    /**
     * Returns Solr API version.
     *
     * @return string|null
     */
    public function getApi(): ?string
    {
        return $this->getOption('api');
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return spl_object_hash($this);
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
                    if (isset($value['username'], $value['password'])) {
                        $this->setAuthentication($value['username'], $value['password']);
                    }
            }
        }
    }
}
