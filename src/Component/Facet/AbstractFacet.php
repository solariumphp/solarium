<?php

namespace Solarium\Component\Facet;

use Solarium\Core\Configurable;

/**
 * Facet base class.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters
 */
abstract class AbstractFacet extends Configurable
{
    /**
     * Exclude tags for this facet.
     *
     * @var array
     */
    protected $excludes = [];

    /**
     * Must be implemented by the facet types and return one of the constants.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getType();

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
     * Set key value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setKey($value)
    {
        return $this->setOption('key', $value);
    }

    /**
     * Add an exclude tag.
     *
     * @param string $tag
     *
     * @return self Provides fluent interface
     */
    public function addExclude($tag)
    {
        $this->excludes[$tag] = true;

        return $this;
    }

    /**
     * Add multiple exclude tags.
     *
     * @param array $excludes
     *
     * @return self Provides fluent interface
     */
    public function addExcludes(array $excludes)
    {
        foreach ($excludes as $exclude) {
            $this->addExclude($exclude);
        }

        return $this;
    }

    /**
     * Get all excludes.
     *
     * @return array
     */
    public function getExcludes()
    {
        return array_keys($this->excludes);
    }

    /**
     * Remove a single exclude tag.
     *
     * @param string $exclude
     *
     * @return self Provides fluent interface
     */
    public function removeExclude($exclude)
    {
        if (isset($this->excludes[$exclude])) {
            unset($this->excludes[$exclude]);
        }

        return $this;
    }

    /**
     * Remove all excludes.
     *
     * @return self Provides fluent interface
     */
    public function clearExcludes()
    {
        $this->excludes = [];

        return $this;
    }

    /**
     * Set multiple excludes.
     *
     * This overwrites any existing excludes
     *
     * @param array $excludes
     */
    public function setExcludes($excludes)
    {
        $this->clearExcludes();
        $this->addExcludes($excludes);
    }

    /**
     * Initialize options.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'key':
                    $this->setKey($value);
                    break;
                case 'exclude':
                    if (!is_array($value)) {
                        $value = explode(',', $value);
                    }
                    $this->setExcludes($value);
                    unset($this->options['exclude']);
                    break;
            }
        }
    }
}
