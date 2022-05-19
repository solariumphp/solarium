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
 * Spellcheck Component Interface.
 */
interface SpellcheckInterface extends ConfigurableInterface
{
    /**
     * Get query option.
     *
     * @return string|null
     */
    public function getQuery(): ?string;

    /**
     * Set build option.
     *
     * Build the spellcheck?
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
     * Reload the dictionary?
     *
     * @param bool $reload
     *
     * @return self Provides fluent interface
     */
    public function setReload(bool $reload): self;

    /**
     * Get fragsize option.
     *
     * @return bool|null
     */
    public function getReload(): ?bool;

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
     * Set onlyMorePopular option.
     *
     * Only return suggestions that result in more hits for the query than the existing query
     *
     * @param bool $onlyMorePopular
     *
     * @return self Provides fluent interface
     */
    public function setOnlyMorePopular(bool $onlyMorePopular): self;

    /**
     * Get onlyMorePopular option.
     *
     * @return bool|null
     */
    public function getOnlyMorePopular(): ?bool;

    /**
     * Set alternativetermcount option.
     *
     * The the number of suggestions to return for each query term existing in the index and/or dictionary.
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setAlternativeTermCount(int $count): self;

    /**
     * Get alternativetermcount option.
     *
     * @return int|null
     */
    public function getAlternativeTermCount(): ?int;

    /**
     * Set extendedResults option.
     *
     * @param bool $extendedResults
     *
     * @return self Provides fluent interface
     */
    public function setExtendedResults(bool $extendedResults): self;

    /**
     * Get extendedResults option.
     *
     * @return bool|null
     */
    public function getExtendedResults(): ?bool;

    /**
     * Set collate option.
     *
     * @param bool $collate
     *
     * @return self Provides fluent interface
     */
    public function setCollate(bool $collate): self;

    /**
     * Get collate option.
     *
     * @return bool|null
     */
    public function getCollate(): ?bool;

    /**
     * Set maxCollations option.
     *
     * @param int $maxCollations
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollations(int $maxCollations): self;

    /**
     * Get maxCollations option.
     *
     * @return int|null
     */
    public function getMaxCollations(): ?int;

    /**
     * Set maxCollationTries option.
     *
     * @param int $maxCollationTries
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollationTries(int $maxCollationTries): self;

    /**
     * Get maxCollationTries option.
     *
     * @return int|null
     */
    public function getMaxCollationTries(): ?int;

    /**
     * Set maxCollationEvaluations option.
     *
     * @param int $maxCollationEvaluations
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollationEvaluations(int $maxCollationEvaluations): self;

    /**
     * Get maxCollationEvaluations option.
     *
     * @return int|null
     */
    public function getMaxCollationEvaluations(): ?int;

    /**
     * Set collateExtendedResults option.
     *
     * @param bool $collateExtendedResults
     *
     * @return self Provides fluent interface
     */
    public function setCollateExtendedResults(bool $collateExtendedResults): self;

    /**
     * Get collateExtendedResults option.
     *
     * @return bool|null
     */
    public function getCollateExtendedResults(): ?bool;

    /**
     * Set accuracy option.
     *
     * @param float $accuracy
     *
     * @return self Provides fluent interface
     */
    public function setAccuracy(float $accuracy): self;

    /**
     * Get accuracy option.
     *
     * @return float|null
     */
    public function getAccuracy(): ?float;

    /**
     * Set a collation param.
     *
     * @param string $param
     * @param mixed  $value
     *
     * @return self Provides fluent interface
     */
    public function setCollateParam(string $param, $value): self;

    /**
     * Returns the array of collate params.
     *
     * @return array
     */
    public function getCollateParams(): array;
}
