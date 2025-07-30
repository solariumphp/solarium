<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Core\ConfigurableInterface;

/**
 * Suggester Interface.
 */
interface SuggesterInterface extends ConfigurableInterface
{
    /**
     * Get query option.
     *
     * @return string|null
     */
    public function getQuery(): ?string;

    /**
     * Set dictionary option.
     *
     * The name of the dictionary to use
     *
     * @param string|array $dictionary
     *
     * @return self Provides fluent interface
     */
    public function setDictionary($dictionary): self;

    /**
     * Get dictionary option.
     *
     * @return array|null
     */
    public function getDictionary(): ?array;

    /**
     * Set count option.
     *
     * The maximum number of suggestions to return
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setCount(int $count): self;

    /**
     * Get count option.
     *
     * @return int|null
     */
    public function getCount(): ?int;

    /**
     * Set cfq option.
     *
     * A Context Filter Query used to filter suggestions based on the context field, if supported by the suggester.
     *
     * @param string $cfq
     *
     * @return self Provides fluent interface
     */
    public function setContextFilterQuery(string $cfq): self;

    /**
     * Get cfq option.
     *
     * @return string|null
     */
    public function getContextFilterQuery(): ?string;

    /**
     * Set build option.
     *
     * @param bool $build
     *
     * @return self Provides fluent interface
     */
    public function setBuild(bool $build): self;

    /**
     * Get build option.
     *
     * @return bool|null
     */
    public function getBuild(): ?bool;

    /**
     * Set reload option.
     *
     * @param bool $reload
     *
     * @return self Provides fluent interface
     */
    public function setReload(bool $reload): self;

    /**
     * Get reload option.
     *
     * @return bool|null
     */
    public function getReload(): ?bool;

    /**
     * Set buildAll option.
     *
     * @param bool $buildAll
     *
     * @return self Provides fluent interface
     */
    public function setBuildAll(bool $buildAll): self;

    /**
     * Get buildAll option.
     *
     * @return bool|null
     */
    public function getBuildAll(): ?bool;
}
