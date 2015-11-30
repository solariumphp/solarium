<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

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
    protected $params = array();

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
     * @param boolean $value
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
     * @return boolean
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
        if ($responseWriter === null) {
            $responseWriter = self::WT_JSON;
        }

        return $responseWriter;
    }
}
