<?php

namespace Solarium\Core;

use Solarium\Exception\InvalidArgumentException;

/**
 * Interface for configurable classes.
 *
 * All classes implementing this interface are  configurable using the constructor or
 * setOption calls. This is the base for many Solarium classes, providing a
 * uniform interface for various models.
 */
interface ConfigurableInterface
{
    /**
     * Set options.
     *
     * If $options is an object it will be converted into an array by called
     * it's toArray method. This is compatible with the Zend_Config classes in
     * Zend Framework, but can also easily be implemented in any other object.
     *
     * @param array|\Zend_Config $options
     * @param bool               $overwrite True for overwriting existing options, false
     *                                      for merging (new values overwrite old ones if needed)
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function setOptions($options, bool $overwrite = false): self;

    /**
     * Get an option value by name.
     *
     * If the option is empty or not set a NULL value will be returned.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getOption(string $name);

    /**
     * Get all options.
     *
     * @return array
     */
    public function getOptions(): array;
}
