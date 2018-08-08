<?php

namespace Solarium\Core\Query;

use Solarium\Core\Configurable;

/**
 * Base class for all query types, not intended for direct usage.
 */
abstract class AbstractQuery extends Configurable implements QueryInterface
{
    const WT_JSON = 'json';

    const WT_PHPS = 'phps';

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
    public function setHandler($handler)
    {
        return $this->setOption('handler', $handler);
    }

    /**
     * Get handler option.
     *
     * @return string
     */
    public function getHandler()
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
    public function setResultClass($classname)
    {
        return $this->setOption('resultclass', $classname);
    }

    /**
     * Get resultclass option.
     *
     * @return string
     */
    public function getResultClass()
    {
        return $this->getOption('resultclass');
    }

    /**
     * Set timeAllowed option.
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setTimeAllowed($value)
    {
        return $this->setOption('timeallowed', $value);
    }

    /**
     * Get timeAllowed option.
     *
     * @return int|null
     */
    public function getTimeAllowed()
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
    public function setOmitHeader($value)
    {
        return $this->setOption('omitheader', $value);
    }

    /**
     * Get omitHeader option.
     *
     * @return bool
     */
    public function getOmitHeader()
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
    public function getHelper()
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
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function addParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Removes a param that was previously added by addParam.
     *
     * Note: This can not be used to remove known default parameters of the solarium api.
     *
     * @param string $name
     *
     * @return self Provides fluent interface
     */
    public function removeParam($name)
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
    public function getParams()
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
    public function setResponseWriter($value)
    {
        return $this->setOption('responsewriter', $value);
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
    public function getResponseWriter()
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
     * @param string $timestamp Milliseconds since epoch
     *
     * @return self Provides fluent interface
     */
    public function setNow($timestamp)
    {
        return $this->setOption('now', $timestamp);
    }

    /**
     * Get now option.
     *
     * @return string Milliseconds since epoch
     */
    public function getNow()
    {
        return $this->getOption('now');
    }

    /**
     * Set timezone option.
     *
     * Forces all date based addition and rounding to be relative to the specified time zone instead of UTC.
     *
     * @param string $timezone Java TimeZone ID
     *
     * @return self Provides fluent interface
     */
    public function setTimeZone($timezone)
    {
        return $this->setOption('timezone', $timezone);
    }

    /**
     * Get timezone option.
     *
     * @return string Java TimeZone ID
     */
    public function getTimeZone()
    {
        return $this->getOption('timezone');
    }
}
