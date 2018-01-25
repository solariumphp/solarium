<?php

namespace Solarium\Core\Query;

use Solarium\Core\ConfigurableInterface;

/**
 * Query interface.
 */
interface QueryInterface extends ConfigurableInterface
{
    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType();

    /**
     * Get the requestbuilder class for this query.
     *
     * @return RequestBuilderInterface
     */
    public function getRequestBuilder();

    /**
     * Get the response parser class for this query.
     *
     * @return ResponseParserInterface
     */
    public function getResponseParser();

    /**
     * Set handler option.
     *
     * @param string $handler
     *
     * @return self Provides fluent interface
     */
    public function setHandler($handler);

    /**
     * Get handler option.
     *
     * @return string
     */
    public function getHandler();

    /**
     * Set resultclass option.
     *
     * If you set a custom result class it must be available through autoloading
     * or a manual require before calling this method. This is your
     * responsibility.
     *
     * Also you need to make sure this class implements the ResultInterface
     *
     * @param string $classname
     *
     * @return self Provides fluent interface
     */
    public function setResultClass($classname);

    /**
     * Get resultclass option.
     *
     * @return string
     */
    public function getResultClass();

    /**
     * Get a helper instance.
     *
     * @return Helper
     */
    public function getHelper();

    /**
     * Add extra params to the request.
     *
     * Only intended for internal use, for instance with dereferenced params.
     * Therefore the params are limited in functionality. Only add and get
     *
     * @param string $name
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function addParam($name, $value);

    /**
     * Get extra params.
     *
     * @return array
     */
    public function getParams();
}
