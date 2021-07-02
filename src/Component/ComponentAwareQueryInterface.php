<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Exception\OutOfBoundsException;

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
     * Query component facetset.
     */
    const COMPONENT_FACETSET = 'facetset';

    /**
     * Query component dismax.
     */
    const COMPONENT_DISMAX = 'dismax';

    /**
     * Query component dismax.
     */
    const COMPONENT_EDISMAX = 'edismax';

    /**
     * Query component highlighting.
     */
    const COMPONENT_HIGHLIGHTING = 'highlighting';

    /**
     * Query component grouping.
     */
    const COMPONENT_GROUPING = 'grouping';

    /**
     * Query component distributed search.
     */
    const COMPONENT_DISTRIBUTEDSEARCH = 'distributedsearch';

    /**
     * Query component stats.
     */
    const COMPONENT_STATS = 'stats';

    /**
     * Query component terms.
     */
    const COMPONENT_TERMS = 'terms';

    /**
     * Query component queryelevation.
     */
    const COMPONENT_QUERYELEVATION = 'queryelevation';

    /**
     * Query component rerank query.
     */
    const COMPONENT_RERANKQUERY = 'rerankquery';

    /**
     * Query component analytics.
     */
    const COMPONENT_ANALYTICS = 'analytics';

    /**
     * Get all registered component types.
     *
     * @return array
     */
    public function getComponentTypes(): array;

    /**
     * Register a component type.
     *
     * @param string $key
     * @param string $component
     *
     * @return self Provides fluent interface
     */
    public function registerComponentType(string $key, string $component);

    /**
     * Get all registered components.
     *
     * @return AbstractComponent[]
     */
    public function getComponents(): array;

    /**
     * Get a component instance by key.
     *
     * You can optionally supply an autoload class to create a new component
     * instance if there is no registered component for the given key yet.
     *
     * @param string      $key      Use one of the constants
     * @param string|bool $autoload Class to autoload if component needs to be created
     * @param array|null  $config   Configuration to use for autoload
     *
     * @throws OutOfBoundsException
     *
     * @return object|null
     */
    public function getComponent(string $key, $autoload = false, array $config = null);

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
    public function setComponent(string $key, AbstractComponent $component): self;

    /**
     * Remove a component instance.
     *
     * You can remove a component by passing its key or the component instance.
     *
     * @param string|AbstractComponent $component
     *
     * @return self Provides fluent interface
     */
    public function removeComponent($component): self;
}
