<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core;

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
     * @param array|null $options
     */
    public function __construct(?array $options = null)
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
     * @param array $options
     * @param bool  $overwrite True for overwriting existing options, false for
     *                         merging (new values overwrite old ones if needed)
     *
     * @return self Provides fluent interface
     */
    public function setOptions(array $options, bool $overwrite = false): self
    {
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

        return $this;
    }

    /**
     * Get an option value by name.
     *
     * If the option is empty or not set a NULL value will be returned.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getOption(string $name): mixed
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
    protected function setOption(string $name, mixed $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }
}
