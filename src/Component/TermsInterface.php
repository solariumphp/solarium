<?php

namespace Solarium\Component;

use Solarium\Core\ConfigurableInterface;

/**
 * Terms interface.
 */
interface TermsInterface extends ConfigurableInterface
{
    /**
     * Set the field name(s) to get the terms from.
     *
     * For multiple fields use a comma-separated string or array
     *
     * @param string|array $value
     *
     * @return self Provides fluent interface
     */
    public function setFields($value);

    /**
     * Get the field name(s) to get the terms from.
     *
     * @return array
     */
    public function getFields();

    /**
     * Set the lowerbound term to start at.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setLowerbound($value);

    /**
     * Get the lowerbound term to start at.
     *
     * @return string
     */
    public function getLowerbound();

    /**
     * Set lowerboundinclude.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setLowerboundInclude($value);

    /**
     * Get lowerboundinclude.
     *
     * @return bool
     */
    public function getLowerboundInclude();

    /**
     * Set mincount (the minimum doc frequency for terms in order to be included).
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setMinCount($value);

    /**
     * Get mincount.
     *
     * @return int
     */
    public function getMinCount();

    /**
     * Set maxcount (the maximum doc frequency for terms in order to be included).
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setMaxCount($value);

    /**
     * Get maxcount.
     *
     * @return int
     */
    public function getMaxCount();

    /**
     * Set prefix for terms.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setPrefix($value);

    /**
     * Get maxcount.
     *
     * @return string
     */
    public function getPrefix();

    /**
     * Set regex to restrict terms.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setRegex($value);

    /**
     * Get regex.
     *
     * @return string
     */
    public function getRegex();

    /**
     * Set regex flags.
     *
     * Use a comma-separated string or array for multiple entries
     *
     * @param string|array $value
     *
     * @return self Provides fluent interface
     */
    public function setRegexFlags($value);

    /**
     * Get regex flags.
     *
     * @return array
     */
    public function getRegexFlags();

    /**
     * Set limit.
     *
     * If < 0 all terms are included
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setLimit($value);

    /**
     * Get limit.
     *
     * @return int
     */
    public function getLimit();

    /**
     * Set the upperbound term to start at.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setUpperbound($value);

    /**
     * Get the upperbound term to start at.
     *
     * @return string
     */
    public function getUpperbound();

    /**
     * Set upperboundinclude.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setUpperboundInclude($value);

    /**
     * Get upperboundinclude.
     *
     * @return bool
     */
    public function getUpperboundInclude();

    /**
     * Set raw option.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setRaw($value);

    /**
     * Get raw option.
     *
     * @return bool
     */
    public function getRaw();

    /**
     * Set sort option.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setSort($value);

    /**
     * Get sort option.
     *
     * @return string
     */
    public function getSort();
}
