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
 * @package Solarium
 */

/**
 * Base class for creating a class that is configurable using the constructor or
 * setOption calls. Used by many solarium classes.
 */
class Solarium_Configurable
{

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
    );

    /**
     * Constructor
     *
     * @throws Solarium_Exception
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            // first convert to array if needed
            if (!is_array($options)) {
                if (is_object($options)) {
                    $options = $options->toArray();
                } else {
                    throw new Solarium_Exception('Options must be an '
                        . 'array or a Zend_Config object');
                }
            }

            // merge with default values, new values overwrite defaults
            $this->_options = array_merge($this->_options, $options);
        }

        // Hook for extending classes (for end user classes)
        $this->_init();
    }

    /**
     * Initialization hook
     *
     * Can be used by classes for special behaviour. For instance some options
     * have extra setup work in their 'set' method that also need to be called
     * when the option is passed as a constructor argument.
     *
     * This hook is called by the constructor after saving the constructor
     * arguments in {@link $_options}
     *
     * @return void
     */
    protected function _init()
    {

    }

    /**
     * Set an option
     *
     * @param string $name
     * @param mixed $value
     * @return Solarium_Configurable
     */
    protected function _setOption($name, $value)
    {
        $this->_options[$name] = $value;

        return $this;
    }

    /**
     * Get an option value
     *
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        if (isset($this->_options[$name])) {
            return $this->_options[$name];
        } else {
            return null;
        }
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

}