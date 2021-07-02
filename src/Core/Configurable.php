<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core;

use Solarium\Exception\InvalidArgumentException;

/**
 * Base class for configurable classes.
 *
 * All classes extending this class are  configurable using the constructor or
 * setOption calls. This is the base for many Solarium classes, providing a
 * uniform interface for various models.
 */
class Configurable implements ConfigurableInterface
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Constructor.
     *
     * If options are passed they will be merged with {@link $options} using
     * the {@link setOptions()} method.
     *
     * After handling the options the {@link _init()} method is called.
     *
     * @param array|\Zend_Config $options
     *
     * @throws InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        } else {
            $this->init();
        }
    }

    /**
     * Set options.
     *
     * If $options is an object, it will be converted into an array by calling
     * its toArray method. This is compatible with the Zend_Config classes in
     * Zend Framework, but can also easily be implemented in any other object.
     * If $options does not have the toArray method, the internal method will
     * be used instead.
     *
     * @param array|\Zend_Config $options
     * @param bool               $overwrite True for overwriting existing options, false
     *                                      for merging (new values overwrite old ones if needed)
     *
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public function setOptions($options, bool $overwrite = false): ConfigurableInterface
    {
        if (null !== $options) {
            // first convert to array if needed
            if (!\is_array($options)) {
                if (\is_object($options)) {
                    $options = (!method_exists($options, 'toArray') ? $this->toArray($options) : $options->toArray());
                } else {
                    throw new InvalidArgumentException('Options value given to the setOptions() method must be an array or a Zend_Config object');
                }
            }

            if (true === $overwrite) {
                $this->options = $options;
            } else {
                $this->options = array_merge($this->options, $options);
            }

            if (true === \is_callable([$this, 'initLocalParameters'])) {
                $this->initLocalParameters();
            }
            // re-init for new options
            $this->init();
        }

        return $this;
    }

    /**
     * Get an option value by name.
     *
     * If the option is empty or not set a NULL value will be returned.
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getOption(string $name)
    {
        return $this->options[$name] ?? null;
    }

    /**
     * Get all options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Initialization hook.
     *
     * Can be used by classes for special behaviour. For instance some options
     * have extra setup work in their 'set' method that also need to be called
     * when the option is passed as a constructor argument.
     *
     * This hook is called by the constructor after saving the constructor
     * arguments in {@link $options}
     *
     * This empty implementation can optionally be implemented in
     * descending classes. It's not an abstract method on purpose, there are
     * many cases where no initialization is needed.
     */
    protected function init()
    {
    }

    /**
     * Set an option.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return self Provides fluent interface
     */
    protected function setOption(string $name, $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Turns an object array into an associative multidimensional array.
     *
     * @param $object object|object[]
     *
     * @return array
     */
    protected function toArray($object): array
    {
        if (\is_object($object)) {
            return (array) $object;
        }

        /*
        * Return array converted to object
        * Using __METHOD__ (Magic constant)
        * for recursive call
        */
        return array_map(__METHOD__, $object);
    }
}
