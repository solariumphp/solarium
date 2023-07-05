<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

/**
 * Trait for handling request params.
 */
trait RequestParamsTrait
{
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
     * Get a param value.
     *
     * @param string $key
     *
     * @return string|array|null
     */
    public function getParam(string $key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        return null;
    }

    /**
     * Get all params.
     *
     * @return array
     */
    public function getParams(): array
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
    public function setParams(array $params): self
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
     * @param string                  $key
     * @param string|array|SubRequest $value
     * @param bool                    $overwrite
     *
     * @return self Provides fluent interface
     */
    public function addParam(string $key, $value, bool $overwrite = false): self
    {
        if (null !== $value && [] !== $value) {
            if (!$overwrite && isset($this->params[$key])) {
                if (!\is_array($this->params[$key])) {
                    $this->params[$key] = [$this->params[$key]];
                }
                $this->params[$key][] = $value;
            } else {
                // not all Solr handlers support 0/1 as boolean values...
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
    public function addParams(array $params, bool $overwrite = false): self
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
    public function removeParam(string $key): self
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
    public function clearParams(): self
    {
        $this->params = [];

        return $this;
    }

    /**
     * Get the query string for this request.
     *
     * @param string $separator
     *
     * @return string
     */
    public function getQueryString(string $separator = '&'): string
    {
        $queryString = '';
        if (\count($this->params) > 0) {
            $queryString = http_build_query($this->params, '', $separator);
            $queryString = preg_replace(
                '/%5B(?:\d|[1-9]\d+)%5D=/',
                '=',
                $queryString
            );
        }

        return $queryString;
    }
}
