<?php

namespace Solarium\Component;

/**
 * Trait query types supporting components.
 */
interface ComponentAwareQueryInterface
{
    /**
     * Query component morelikethis.
     */
    const COMPONENT_MORELIKETHIS = 'morelikethis';

    /**
     * Query component spellcheck.
     */
    const COMPONENT_SPELLCHECK = 'spellcheck';

    /**
     * Query component spellcheck.
     */
    const COMPONENT_SUGGESTER = 'suggest';

    /**
     * Query component debug.
     */
    const COMPONENT_DEBUG = 'debug';

    /**
     * Query component spatial.
     */
    const COMPONENT_SPATIAL = 'spatial';

    /**
     * Get all registered component types.
     *
     * @return array
     */
    public function getComponentTypes();

    /**
     * Register a component type.
     *
     * @param string $key
     * @param string $component
     *
     * @return self Provides fluent interface
     */
    public function registerComponentType($key, $component);

    /**
     * Get all registered components.
     *
     * @return AbstractComponent[]
     */
    public function getComponents();

    /**
     * Get a component instance by key.
     *
     * You can optionally supply an autoload class to create a new component
     * instance if there is no registered component for the given key yet.
     *
     * @throws \Solarium\Exception\OutOfBoundsException
     *
     * @param string         $key      Use one of the constants
     * @param string|boolean $autoload Class to autoload if component needs to be created
     * @param array|null     $config   Configuration to use for autoload
     *
     * @return object|null
     */
    public function getComponent($key, $autoload = false, $config = null);

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
    public function setComponent($key, $component);

    /**
     * Remove a component instance.
     *
     * You can remove a component by passing its key or the component instance.
     *
     * @param string|AbstractComponent $component
     *
     * @return self Provides fluent interface
     */
    public function removeComponent($component);

    /**
     * Get a MoreLikeThis component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\MoreLikeThis
     */
    public function getMoreLikeThis();

    /**
     * Get a spellcheck component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Spellcheck
     */
    public function getSpellcheck();

    /**
     * Get a suggest component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Spellcheck
     */
    public function getSuggester();

    /**
     * Get a Debug component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Debug
     */
    public function getDebug();

    /**
     * Get a Spatial component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Spatial
     */
    public function getSpatial();

}
