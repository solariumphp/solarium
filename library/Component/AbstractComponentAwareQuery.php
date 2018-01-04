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

namespace Solarium\Component;

use Solarium\Core\Query\AbstractQuery;
use Solarium\Exception\OutOfBoundsException;

/**
 * Base class for all query types supporting components, not intended for direct usage.
 */
abstract class AbstractComponentAwareQuery extends AbstractQuery
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
     * Search components.
     *
     * @var AbstractComponent[]
     */
    protected $components = array();

    /**
     * Default select query component types.
     *
     * @var array
     */
    protected $componentTypes = array(
        self::COMPONENT_MORELIKETHIS => 'Solarium\Component\MoreLikeThis',
        self::COMPONENT_SPELLCHECK   => 'Solarium\Component\Spellcheck',
        self::COMPONENT_SUGGESTER    => 'Solarium\Component\Suggester',
        self::COMPONENT_DEBUG        => 'Solarium\Component\Debug',
        self::COMPONENT_SPATIAL      => 'Solarium\Component\Spatial',
    );

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
    public function registerComponentType($key, $component)
    {
        $this->componentTypes[$key] = $component;

        return $this;
    }

    /**
     * Get all registered components.
     *
     * @return AbstractComponent[]
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * Get a component instance by key.
     *
     * You can optionally supply an autoload class to create a new component
     * instance if there is no registered component for the given key yet.
     *
     * @throws OutOfBoundsException
     *
     * @param string         $key      Use one of the constants
     * @param string|boolean $autoload Class to autoload if component needs to be created
     * @param array|null     $config   Configuration to use for autoload
     *
     * @return object|null
     */
    public function getComponent($key, $autoload = false, $config = null)
    {
        if (isset($this->components[$key])) {
            return $this->components[$key];
        } else {
            if ($autoload === true) {
                if (!isset($this->componentTypes[$key])) {
                    throw new OutOfBoundsException('Cannot autoload unknown component: '.$key);
                }

                $className = $this->componentTypes[$key];
                $className = class_exists($className) ? $className : $className.strrchr($className, '\\');
                $component = new $className($config);
                $this->setComponent($key, $component);

                return $component;
            }

            return;
        }
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
    public function setComponent($key, $component)
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
    public function removeComponent($component)
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
     */
    protected function createComponents($configs)
    {
        foreach ($configs as $type => $config) {
            $this->getComponent($type, true, $config);
        }
    }

    /**
     * Get a MoreLikeThis component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\MoreLikeThis
     */
    public function getMoreLikeThis()
    {
        return $this->getComponent(self::COMPONENT_MORELIKETHIS, true);
    }

    /**
     * Get a spellcheck component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Spellcheck
     */
    public function getSpellcheck()
    {
        return $this->getComponent(self::COMPONENT_SPELLCHECK, true);
    }

    /**
     * Get a suggest component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Spellcheck
     */
    public function getSuggester()
    {
        return $this->getComponent(self::COMPONENT_SUGGESTER, true);
    }

    /**
     * Get a Debug component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Debug
     */
    public function getDebug()
    {
        return $this->getComponent(self::COMPONENT_DEBUG, true);
    }

    /**
     * Get a Spatial component instance.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Component\Spatial
     */
    public function getSpatial()
    {
        return $this->getComponent(self::COMPONENT_SPATIAL, true);
    }

}
