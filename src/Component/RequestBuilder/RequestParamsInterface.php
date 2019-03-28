<?php

namespace Solarium\Component\RequestBuilder;

/**
 * Interface for handling request params.
 */
interface RequestParamsInterface
{
    /**
     * Get a param value.
     *
     * @param string $key
     *
     * @return string|array
     */
    public function getParam(string $key);

    /**
     * Get all params.
     *
     * @return array
     */
    public function getParams(): array;

    /**
     * Set request params.
     *
     * @param array $params
     *
     * @return self Provides fluent interface
     */
    public function setParams(array $params): RequestParamsInterface;

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
    public function addParam(string $key, $value, bool $overwrite = false): RequestParamsInterface;

    /**
     * Add multiple params to the request.
     *
     * @param array $params
     * @param bool  $overwrite
     *
     * @return self Provides fluent interface
     */
    public function addParams(array $params, bool $overwrite = false): RequestParamsInterface;

    /**
     * Remove a param by key.
     *
     * @param string $key
     *
     * @return self Provides fluent interface
     */
    public function removeParam(string $key): RequestParamsInterface;

    /**
     * Clear all request params.
     *
     * @return self Provides fluent interface
     */
    public function clearParams(): RequestParamsInterface;

    /**
     * Get the query string for this request.
     *
     * @param string $separator
     *
     * @return string
     */
    public function getQueryString(string $separator = '&'): string;
}
