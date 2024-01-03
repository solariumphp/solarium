<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\ComponentTraits;

use Solarium\Component\SuggesterInterface;

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
     * @return SuggesterInterface Provides fluent interface
     */
    public function setDictionary($dictionary): SuggesterInterface
    {
        if (\is_string($dictionary)) {
            $dictionary = [$dictionary];
        }

        return $this->setOption('dictionary', $dictionary);
    }

    /**
     * Get dictionary option.
     *
     * @return array|null
     */
    public function getDictionary(): ?array
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
     * @return SuggesterInterface Provides fluent interface
     */
    public function setCount(int $count): SuggesterInterface
    {
        return $this->setOption('count', $count);
    }

    /**
     * Get count option.
     *
     * @return int|null
     */
    public function getCount(): ?int
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
     * @return SuggesterInterface Provides fluent interface
     */
    public function setContextFilterQuery(string $cfq): SuggesterInterface
    {
        return $this->setOption('cfq', $cfq);
    }

    /**
     * Get cfq option.
     *
     * @return string|null
     */
    public function getContextFilterQuery(): ?string
    {
        return $this->getOption('cfq');
    }

    /**
     * Set build option.
     *
     * @param bool $build
     *
     * @return SuggesterInterface Provides fluent interface
     */
    public function setBuild(bool $build): SuggesterInterface
    {
        return $this->setOption('build', $build);
    }

    /**
     * Get build option.
     *
     * @return bool|null
     */
    public function getBuild(): ?bool
    {
        return $this->getOption('build');
    }

    /**
     * Set reload option.
     *
     * @param bool $reload
     *
     * @return SuggesterInterface Provides fluent interface
     */
    public function setReload(bool $reload): SuggesterInterface
    {
        return $this->setOption('reload', $reload);
    }

    /**
     * Get reload option.
     *
     * @return bool|null
     */
    public function getReload(): ?bool
    {
        return $this->getOption('reload');
    }

    /**
     * Set buildAll option.
     *
     * @param bool $buildAll
     *
     * @return SuggesterInterface Provides fluent interface
     */
    public function setBuildAll(bool $buildAll): SuggesterInterface
    {
        return $this->setOption('buildAll', $buildAll);
    }

    /**
     * Get buildAll option.
     *
     * @return bool|null
     */
    public function getBuildAll(): ?bool
    {
        return $this->getOption('buildAll');
    }
}
