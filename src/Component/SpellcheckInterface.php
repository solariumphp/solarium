<?php

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
    public function setBuild(bool $build): SpellcheckInterface;

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
    public function setReload(bool $reload): SpellcheckInterface;

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
     * @param string $dictionary
     *
     * @return self Provides fluent interface
     */
    public function setDictionary(string $dictionary): SpellcheckInterface;

    /**
     * Get dictionary option.
     *
     * @return string|null
     */
    public function getDictionary(): ?string;

    /**
     * Set count option.
     *
     * The maximum number of suggestions to return
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setCount(int $count): SpellcheckInterface;

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
    public function setOnlyMorePopular(bool $onlyMorePopular): SpellcheckInterface;

    /**
     * Get onlyMorePopular option.
     *
     * @return bool|null
     */
    public function getOnlyMorePopular(): ?bool;

    /**
     * Set extendedResults option.
     *
     * @param bool $extendedResults
     *
     * @return self Provides fluent interface
     */
    public function setExtendedResults(bool $extendedResults): SpellcheckInterface;

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
    public function setCollate(bool $collate): SpellcheckInterface;

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
    public function setMaxCollations(int $maxCollations): SpellcheckInterface;

    /**
     * Get maxCollations option.
     *
     * @return int|null
     */
    public function getMaxCollations(): ?int;

    /**
     * Set maxCollationTries option.
     *
     * @param string $maxCollationTries
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollationTries(string $maxCollationTries): SpellcheckInterface;

    /**
     * Get maxCollationTries option.
     *
     * @return string|null
     */
    public function getMaxCollationTries(): ?string;

    /**
     * Set maxCollationEvaluations option.
     *
     * @param int $maxCollationEvaluations
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollationEvaluations(int $maxCollationEvaluations): SpellcheckInterface;

    /**
     * Get maxCollationEvaluations option.
     *
     * @return int|null
     */
    public function getMaxCollationEvaluations(): ?int;

    /**
     * Set collateExtendedResults option.
     *
     * @param string $collateExtendedResults
     *
     * @return self Provides fluent interface
     */
    public function setCollateExtendedResults(string $collateExtendedResults): SpellcheckInterface;

    /**
     * Get collateExtendedResults option.
     *
     * @return string|null
     */
    public function getCollateExtendedResults(): ?string;

    /**
     * Set accuracy option.
     *
     * @param float $accuracy
     *
     * @return self Provides fluent interface
     */
    public function setAccuracy(float $accuracy): SpellcheckInterface;

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
    public function setCollateParam(string $param, $value): SpellcheckInterface;

    /**
     * Returns the array of collate params.
     *
     * @return array
     */
    public function getCollateParams(): array;
}
