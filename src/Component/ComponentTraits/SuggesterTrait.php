<?php

namespace Solarium\Component\ComponentTraits;

/**
 * Suggester Query Trait.
 */
trait SuggesterTrait
{
    /**
     * Set dictionary option.
     *
     * The name of the dictionary or dictionaries to use
     *
     * @param string|array $dictionary
     *
     * @return self Provides fluent interface
     */
    public function setDictionary($dictionary)
    {
        return $this->setOption('dictionary', $dictionary);
    }

    /**
     * Get dictionary option.
     *
     * @return string|null
     */
    public function getDictionary()
    {
        return $this->getOption('dictionary');
    }

    /**
     * Set count option.
     *
     * The maximum number of suggestions to return
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setCount($count)
    {
        return $this->setOption('count', $count);
    }

    /**
     * Get count option.
     *
     * @return int|null
     */
    public function getCount()
    {
        return $this->getOption('count');
    }

    /**
     * Set cfq option.
     *
     * A Context Filter Query used to filter suggestions based on the context field, if supported by the suggester.
     *
     * @param string $cfq
     *
     * @return self Provides fluent interface
     */
    public function setContextFilterQuery($cfq)
    {
        return $this->setOption('cfq', $cfq);
    }

    /**
     * Get cfq option.
     *
     * @return string|null
     */
    public function getContextFilterQuery()
    {
        return $this->getOption('cfq');
    }

    /**
     * Set build option.
     *
     * @param bool $build
     *
     * @return self Provides fluent interface
     */
    public function setBuild($build)
    {
        return $this->setOption('build', $build);
    }

    /**
     * Get build option.
     *
     * @return bool|null
     */
    public function getBuild()
    {
        return $this->getOption('build');
    }

    /**
     * Set reload option.
     *
     * @param bool  $build
     * @param mixed $reload
     *
     * @return self Provides fluent interface
     */
    public function setReload($reload)
    {
        return $this->setOption('reload', $reload);
    }

    /**
     * Get reload option.
     *
     * @return bool|null
     */
    public function getReload()
    {
        return $this->getOption('reload');
    }
}
