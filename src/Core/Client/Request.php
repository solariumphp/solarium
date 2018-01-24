<?php

namespace Solarium\Core\Client;

use Solarium\Core\Configurable;
use Solarium\Exception\RuntimeException;

/**
 * Class for describing a request.
 */
class Request extends Configurable
{
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
     * Request params.
     *
     * Multivalue params are supported using a multidimensional array:
     * 'fq' => array('cat:1','published:1')
     *
     * @var array
     */
    protected $params = [];

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
     * Get a param value.
     *
     * @param string $key
     *
     * @return string|array
     */
    public function getParam($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
    }

    /**
     * Get all params.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set request params.
     *
     * @param array $params
     *
     * @return self Provides fluent interface
     */
    public function setParams($params)
    {
        $this->clearParams();
        $this->addParams($params);

        return $this;
    }

    /**
     * Add a request param.
     *
     * If you add a request param that already exists the param will be converted into a multivalue param,
     * unless you set the overwrite param to true.
     *
     * Empty params are not added to the request. If you want to empty a param disable it you should use
     * remove param instead.
     *
     * @param string       $key
     * @param string|array $value
     * @param bool         $overwrite
     *
     * @return self Provides fluent interface
     */
    public function addParam($key, $value, $overwrite = false)
    {
        if (null !== $value) {
            if (!$overwrite && isset($this->params[$key])) {
                if (!is_array($this->params[$key])) {
                    $this->params[$key] = [$this->params[$key]];
                }
                $this->params[$key][] = $value;
            } else {
                // not all solr handlers support 0/1 as boolean values...
                if (true === $value) {
                    $value = 'true';
                } elseif (false === $value) {
                    $value = 'false';
                }

                $this->params[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Add multiple params to the request.
     *
     * @param array $params
     * @param bool  $overwrite
     *
     * @return self Provides fluent interface
     */
    public function addParams($params, $overwrite = false)
    {
        foreach ($params as $key => $value) {
            $this->addParam($key, $value, $overwrite);
        }

        return $this;
    }

    /**
     * Remove a param by key.
     *
     * @param string $key
     *
     * @return self Provides fluent interface
     */
    public function removeParam($key)
    {
        if (isset($this->params[$key])) {
            unset($this->params[$key]);
        }

        return $this;
    }

    /**
     * Clear all request params.
     *
     * @return self Provides fluent interface
     */
    public function clearParams()
    {
        $this->params = [];

        return $this;
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
     * Get the query string for this request.
     *
     * @return string
     */
    public function getQueryString()
    {
        $queryString = '';
        if (count($this->params) > 0) {
            $queryString = http_build_query($this->params, null, '&');
            $queryString = preg_replace(
                '/%5B(?:[0-9]|[1-9][0-9]+)%5D=/',
                '=',
                $queryString
            );
        }

        return $queryString;
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
}
