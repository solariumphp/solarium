<?php

namespace Solarium\Component;

use Solarium\Exception\OutOfBoundsException;

/**
 * Trait query types supporting components.
 */
trait ComponentAwareQueryTrait
{
    /**
     * Search components.
     *
     * @var AbstractComponent[]
     */
    protected $components = [];

    /**
     * Default select query component types.
     *
     * @var array
     */
    protected $componentTypes = [];

    /**
     * Get all registered component types.
     *
     * @return array
     */
    public function getComponentTypes()
    {
        return $this->componentTypes;
    }

    /**
     * Register a component type.
     *
     * @param string $key
     * @param string $component
     *
     * @return self Provides fluent interface
     */
    public function registerComponentType(string $key, string $component): ComponentAwareQueryInterface
    {
        $this->componentTypes[$key] = $component;

        return $this;
    }

    /**
     * Get all registered components.
     *
     * @return AbstractComponent[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * Get a component instance by key.
     *
     * You can optionally supply an autoload class to create a new component
     * instance if there is no registered component for the given key yet.
     *
     *
     * @param string      $key      Use one of the constants
     * @param string|bool $autoload Class to autoload if component needs to be created
     * @param array|null  $config   Configuration to use for autoload
     *
     * @throws OutOfBoundsException
     *
     * @return object|null
     */
    public function getComponent(string $key, $autoload = false, array $config = null)
    {
        if (isset($this->components[$key])) {
            return $this->components[$key];
        }

        if (true === $autoload) {
            if (!isset($this->componentTypes[$key])) {
                throw new OutOfBoundsException('Cannot autoload unknown component: '.$key);
            }

            $className = $this->componentTypes[$key];
            $className = class_exists($className) ? $className : $className.strrchr($className, '\\');
            $component = new $className($config);
            $this->setComponent($key, $component);

            return $component;
        }

        return null;
    }

    /**
     * Set a component instance.
     *
     * This overwrites any existing component registered with the same key.
     *
     * @param string            $key
     * @param AbstractComponent $component
     *
     * @return self Provides fluent interface
     */
    public function setComponent(string $key, AbstractComponent $component): ComponentAwareQueryInterface
    {
        $component->setQueryInstance($this);
        $this->components[$key] = $component;

        return $this;
    }

    /**
     * Remove a component instance.
     *
     * You can remove a component by passing its key or the component instance.
     *
     * @param string|AbstractComponent $component
     *
     * @return self Provides fluent interface
     */
    public function removeComponent($component): ComponentAwareQueryInterface
    {
        if (is_object($component)) {
            foreach ($this->components as $key => $instance) {
                if ($instance === $component) {
                    unset($this->components[$key]);
                    break;
                }
            }
        } else {
            if (isset($this->components[$component])) {
                unset($this->components[$component]);
            }
        }

        return $this;
    }

    /**
     * Build component instances based on config.
     *
     * @param array $configs
     *
     * @return self Provides fluent interface
     */
    protected function createComponents(array $configs): ComponentAwareQueryInterface
    {
        foreach ($configs as $type => $config) {
            $this->getComponent($type, true, $config);
        }

        return $this;
    }
}
