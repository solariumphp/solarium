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
    public function getQuery();

    /**
     * Set build option.
     *
     * Build the spellcheck?
     *
     * @param bool $build
     *
     * @return self Provides fluent interface
     */
    public function setBuild($build);

    /**
     * Get build option.
     *
     * @return bool|null
     */
    public function getBuild();

    /**
     * Set reload option.
     *
     * Reload the dictionary?
     *
     * @param bool $reload
     *
     * @return self Provides fluent interface
     */
    public function setReload($reload);

    /**
     * Get fragsize option.
     *
     * @return bool|null
     */
    public function getReload();

    /**
     * Set dictionary option.
     *
     * The name of the dictionary to use
     *
     * @param string $dictionary
     *
     * @return self Provides fluent interface
     */
    public function setDictionary($dictionary);

    /**
     * Get dictionary option.
     *
     * @return string|null
     */
    public function getDictionary();

    /**
     * Set count option.
     *
     * The maximum number of suggestions to return
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setCount($count);

    /**
     * Get count option.
     *
     * @return int|null
     */
    public function getCount();

    /**
     * Set onlyMorePopular option.
     *
     * Only return suggestions that result in more hits for the query than the existing query
     *
     * @param bool $onlyMorePopular
     *
     * @return self Provides fluent interface
     */
    public function setOnlyMorePopular($onlyMorePopular);

    /**
     * Get onlyMorePopular option.
     *
     * @return bool|null
     */
    public function getOnlyMorePopular();

    /**
     * Set extendedResults option.
     *
     * @param bool $extendedResults
     *
     * @return self Provides fluent interface
     */
    public function setExtendedResults($extendedResults);

    /**
     * Get extendedResults option.
     *
     * @return bool|null
     */
    public function getExtendedResults();

    /**
     * Set collate option.
     *
     * @param bool $collate
     *
     * @return self Provides fluent interface
     */
    public function setCollate($collate);

    /**
     * Get collate option.
     *
     * @return bool|null
     */
    public function getCollate();

    /**
     * Set maxCollations option.
     *
     * @param int $maxCollations
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollations($maxCollations);

    /**
     * Get maxCollations option.
     *
     * @return int|null
     */
    public function getMaxCollations();

    /**
     * Set maxCollationTries option.
     *
     * @param string $maxCollationTries
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollationTries($maxCollationTries);

    /**
     * Get maxCollationTries option.
     *
     * @return string|null
     */
    public function getMaxCollationTries();

    /**
     * Set maxCollationEvaluations option.
     *
     * @param int $maxCollationEvaluations
     *
     * @return self Provides fluent interface
     */
    public function setMaxCollationEvaluations($maxCollationEvaluations);

    /**
     * Get maxCollationEvaluations option.
     *
     * @return int|null
     */
    public function getMaxCollationEvaluations();

    /**
     * Set collateExtendedResults option.
     *
     * @param string $collateExtendedResults
     *
     * @return self Provides fluent interface
     */
    public function setCollateExtendedResults($collateExtendedResults);

    /**
     * Get collateExtendedResults option.
     *
     * @return string|null
     */
    public function getCollateExtendedResults();

    /**
     * Set accuracy option.
     *
     * @param float $accuracy
     *
     * @return self Provides fluent interface
     */
    public function setAccuracy($accuracy);

    /**
     * Get accuracy option.
     *
     * @return float|null
     */
    public function getAccuracy();

    /**
     * Set a collation param.
     *
     * @param string $param
     * @param mixed  $value
     *
     * @return self Provides fluent interface
     */
    public function setCollateParam($param, $value);

    /**
     * Returns the array of collate params.
     *
     * @return array
     */
    public function getCollateParams();
}
