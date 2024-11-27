<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query;

use Solarium\Core\Configurable;
use Solarium\Core\Query\LocalParameters\LocalParametersTrait;

/**
 * Base class for all query types, not intended for direct usage.
 */
abstract class AbstractQuery extends Configurable implements QueryInterface
{
    use LocalParametersTrait;

    public const WT_JSON = 'json';

    public const WT_PHPS = 'phps';

    /**
     * Helper instance.
     *
     * @var Helper
     */
    protected $helper;

    /**
     * Extra query params (e.g. dereferenced params).
     *
     * @var array
     */
    protected $params = [];

    /**
     * Set handler option.
     *
     * @param string $handler
     *
     * @return self Provides fluent interface
     */
    public function setHandler(string $handler): self
    {
        $this->setOption('handler', $handler);

        return $this;
    }

    /**
     * Get handler option.
     *
     * @return string|null
     */
    public function getHandler(): ?string
    {
        return $this->getOption('handler');
    }

    /**
     * Set resultclass option.
     *
     * If you set a custom result class it must be available through autoloading
     * or a manual require before calling this method. This is your
     * responsibility.
     *
     * Also you need to make sure it extends the orginal result class of the
     * query or has an identical API.
     *
     * @param string $classname
     *
     * @return self Provides fluent interface
     */
    public function setResultClass(string $classname): self
    {
        $this->setOption('resultclass', $classname);

        return $this;
    }

    /**
     * Get resultclass option.
     *
     * @return string|null
     */
    public function getResultClass(): ?string
    {
        return $this->getOption('resultclass');
    }

    /**
     * Set timeAllowed option.
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     *
     * @deprecated Will be removed in Solarium 7. This parameter is only relevant for Select queries.
     */
    public function setTimeAllowed(int $value): self
    {
        $this->setOption('timeallowed', $value);

        return $this;
    }

    /**
     * Get timeAllowed option.
     *
     * @return int|null
     *
     * @deprecated Will be removed in Solarium 7. This parameter is only relevant for Select queries.
     */
    public function getTimeAllowed(): ?int
    {
        return $this->getOption('timeallowed');
    }

    /**
     * Set omitHeader option.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setOmitHeader(bool $value): self
    {
        $this->setOption('omitheader', $value);

        return $this;
    }

    /**
     * Get omitHeader option.
     *
     * @return bool|null
     */
    public function getOmitHeader(): ?bool
    {
        return $this->getOption('omitheader');
    }

    /**
     * Get a helper instance.
     *
     * Uses lazy loading: the helper is instantiated on first use
     *
     * @return Helper
     */
    public function getHelper(): Helper
    {
        if (null === $this->helper) {
            $this->helper = new Helper($this);
        }

        return $this->helper;
    }

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
    public function addParam(string $name, $value): self
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Removes a param that was previously added by addParam.
     *
     * Note: This can not be used to remove known default parameters of the Solarium API.
     *
     * @param string $name
     *
     * @return self Provides fluent interface
     */
    public function removeParam(string $name): self
    {
        if (isset($this->params[$name])) {
            unset($this->params[$name]);
        }

        return $this;
    }

    /**
     * Get extra params.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set responsewriter option.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setResponseWriter(string $value): self
    {
        $this->setOption('responsewriter', $value);

        return $this;
    }

    /**
     * Get responsewriter option.
     *
     * Defaults to json for backwards compatibility and security.
     *
     * If you can fully trust the Solr responses (phps has a security risk from untrusted sources) you might consider
     * setting the responsewriter to 'phps' (serialized php). This can give a performance advantage,
     * especially with big resultsets.
     *
     * @return string
     */
    public function getResponseWriter(): string
    {
        $responseWriter = $this->getOption('responsewriter');
        if (null === $responseWriter) {
            $responseWriter = self::WT_JSON;
        }

        return $responseWriter;
    }

    /**
     * Set now option.
     *
     * Instructs Solr to use an arbitrary moment in time (past or future) to override NOW for date math expressions.
     *
     * Make sure to pass a string instead of an int if the code has to run on a 32-bit PHP installation.
     *
     * @param int $timestamp Milliseconds since epoch
     *
     * @return self Provides fluent interface
     */
    public function setNow(int $timestamp): self
    {
        $this->setOption('now', $timestamp);

        return $this;
    }

    /**
     * Get now option.
     *
     * @return int|null Milliseconds since epoch
     */
    public function getNow(): ?int
    {
        return $this->getOption('now');
    }

    /**
     * Set timezone option.
     *
     * Forces all date based addition and rounding to be relative to the specified time zone instead of UTC.
     *
     * @param string|\DateTimeZone $timezone Java TimeZone ID as string or PHP \DateTimeZone object
     *
     * @return self Provides fluent interface
     */
    public function setTimeZone($timezone): self
    {
        if ($timezone instanceof \DateTimeZone) {
            $this->setOption('timezone', $timezone->getName());
        } else {
            $this->setOption('timezone', $timezone);
        }

        return $this;
    }

    /**
     * Get timezone option.
     *
     * @return string|null Java TimeZone ID as string or PHP DateTimeZone object
     */
    public function getTimeZone(): ?string
    {
        return $this->getOption('timezone');
    }

    /**
     * Set distrib option.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setDistrib(bool $value): self
    {
        $this->setOption('distrib', $value);

        return $this;
    }

    /**
     * Get distrib option.
     *
     * @return bool|null
     */
    public function getDistrib(): ?bool
    {
        return $this->getOption('distrib');
    }

    /**
     * Set ie (input encoding) option.
     *
     * @param string $encoding
     *
     * @return self Provides fluent interface
     */
    public function setInputEncoding(string $encoding): self
    {
        $this->setOption('ie', $encoding);

        return $this;
    }

    /**
     * Get ie (input encoding) option.
     *
     * @return string|null
     */
    public function getInputEncoding(): ?string
    {
        return $this->getOption('ie');
    }
}
