<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
    public function getType(): string;

    /**
     * Get the requestbuilder class for this query.
     *
     * @return RequestBuilderInterface
     */
    public function getRequestBuilder(): RequestBuilderInterface;

    /**
     * Get the response parser class for this query.
     *
     * @return ResponseParserInterface|null
     */
    public function getResponseParser(): ?ResponseParserInterface;

    /**
     * Set handler option.
     *
     * @param string $handler
     *
     * @return self Provides fluent interface
     */
    public function setHandler(string $handler): self;

    /**
     * Get handler option.
     *
     * @return string|null
     */
    public function getHandler(): ?string;

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
    public function setResultClass(string $classname): self;

    /**
     * Get resultclass option.
     *
     * @return string|null
     */
    public function getResultClass(): ?string;

    /**
     * Get a helper instance.
     *
     * @return Helper
     */
    public function getHelper(): Helper;

    /**
     * Add extra params to the request.
     *
     * Only intended for internal use, for instance with dereferenced params.
     * Therefore the params are limited in functionality. Only add and get
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return self Provides fluent interface
     */
    public function addParam(string $name, $value): self;

    /**
     * Get extra params.
     *
     * @return array
     */
    public function getParams(): array;
}
