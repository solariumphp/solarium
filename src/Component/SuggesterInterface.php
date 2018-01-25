<?php

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
    public function getQuery();

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
     * Set cfq option.
     *
     * A Context Filter Query used to filter suggestions based on the context field, if supported by the suggester.
     *
     * @param string $cfq
     *
     * @return self Provides fluent interface
     */
    public function setContextFilterQuery($cfq);

    /**
     * Get cfq option.
     *
     * @return string|null
     */
    public function getContextFilterQuery();

    /**
     * Set build option.
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
     * @param bool  $build
     * @param mixed $reload
     *
     * @return self Provides fluent interface
     */
    public function setReload($reload);

    /**
     * Get reload option.
     *
     * @return bool|null
     */
    public function getReload();
}
