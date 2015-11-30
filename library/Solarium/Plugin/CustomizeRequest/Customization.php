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

namespace Solarium\Plugin\CustomizeRequest;

use Solarium\Core\Configurable;

/**
 * Customization value object.
 */
class Customization extends Configurable
{
    /**
     * Type definition for params.
     */
    const TYPE_PARAM = 'param';

    /**
     * Type definition for headers.
     */
    const TYPE_HEADER = 'header';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = array(
        'key' => null,
        'type' => null,
        'name' => null,
        'value' => null,
        'persistent' => false,
        'overwrite' => true,
    );

    /**
     * Set key value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setKey($value)
    {
        $this->setOption('key', $value);

        return $this;
    }

    /**
     * Get key value.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getOption('key');
    }

    /**
     * Set type value.
     *
     * @param string $value
     *
     * @return Customization
     */
    public function setType($value)
    {
        $this->setOption('type', $value);

        return $this;
    }

    /**
     * Get type value.
     *
     * @return string
     */
    public function getType()
    {
        return $this->getOption('type');
    }

    /**
     * Set name value.
     *
     * @param string $value
     *
     * @return Customization
     */
    public function setName($value)
    {
        $this->setOption('name', $value);

        return $this;
    }

    /**
     * Get name value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getOption('name');
    }

    /**
     * Set value.
     *
     * @param string $value
     *
     * @return Customization
     */
    public function setValue($value)
    {
        $this->setOption('value', $value);

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->getOption('value');
    }

    /**
     * Set persistent on/off.
     *
     * @param boolean $value
     *
     * @return Customization
     */
    public function setPersistent($value)
    {
        $this->setOption('persistent', $value);

        return $this;
    }

    /**
     * Get persistent setting.
     *
     * @return boolean
     */
    public function getPersistent()
    {
        return $this->getOption('persistent');
    }

    /**
     * Set overwrite option on/off.
     *
     * @param boolean $value
     *
     * @return Customization
     */
    public function setOverwrite($value)
    {
        $this->setOption('overwrite', $value);

        return $this;
    }

    /**
     * Get overwrite option value.
     *
     * @return boolean
     */
    public function getOverwrite()
    {
        return $this->getOption('overwrite');
    }

    /**
     * Check for all mandatory settings.
     *
     * @return bool
     */
    public function isValid()
    {
        $type = $this->getType();
        if ($type !== self::TYPE_PARAM && $type !== self::TYPE_HEADER) {
            return false;
        }

        if (null === $this->getKey() || null === $this->getName() || null === $this->getValue()) {
            return false;
        }

        return true;
    }
}
