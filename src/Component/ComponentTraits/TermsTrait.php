<?php

namespace Solarium\Component\ComponentTraits;

/**
 * Terms component.
 *
 * A terms query provides access to the indexed terms in a field and the number of documents that match each term.
 * This can be useful for doing auto-suggest or other things that operate at the term level instead of the search
 * or document level. Retrieving terms in index order is very fast since the implementation directly uses Lucene's
 * TermEnum to iterate over the term dictionary.
 */
trait TermsTrait
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
    public function setFields($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
            $value = array_map('trim', $value);
        }

        return $this->setOption('fields', $value);
    }

    /**
     * Get the field name(s) to get the terms from.
     *
     * @return array
     */
    public function getFields()
    {
        $value = $this->getOption('fields');
        if (null === $value) {
            $value = [];
        }

        return $value;
    }

    /**
     * Set the lowerbound term to start at.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setLowerbound($value)
    {
        return $this->setOption('lowerbound', $value);
    }

    /**
     * Get the lowerbound term to start at.
     *
     * @return string
     */
    public function getLowerbound()
    {
        return $this->getOption('lowerbound');
    }

    /**
     * Set lowerboundinclude.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setLowerboundInclude($value)
    {
        return $this->setOption('lowerboundinclude', $value);
    }

    /**
     * Get lowerboundinclude.
     *
     * @return bool
     */
    public function getLowerboundInclude()
    {
        return $this->getOption('lowerboundinclude');
    }

    /**
     * Set mincount (the minimum doc frequency for terms in order to be included).
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setMinCount($value)
    {
        return $this->setOption('mincount', $value);
    }

    /**
     * Get mincount.
     *
     * @return int
     */
    public function getMinCount()
    {
        return $this->getOption('mincount');
    }

    /**
     * Set maxcount (the maximum doc frequency for terms in order to be included).
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setMaxCount($value)
    {
        return $this->setOption('maxcount', $value);
    }

    /**
     * Get maxcount.
     *
     * @return int
     */
    public function getMaxCount()
    {
        return $this->getOption('maxcount');
    }

    /**
     * Set prefix for terms.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setPrefix($value)
    {
        return $this->setOption('prefix', $value);
    }

    /**
     * Get maxcount.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->getOption('prefix');
    }

    /**
     * Set regex to restrict terms.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setRegex($value)
    {
        return $this->setOption('regex', $value);
    }

    /**
     * Get regex.
     *
     * @return string
     */
    public function getRegex()
    {
        return $this->getOption('regex');
    }

    /**
     * Set regex flags.
     *
     * Use a comma-separated string or array for multiple entries
     *
     * @param string|array $value
     *
     * @return self Provides fluent interface
     */
    public function setRegexFlags($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
            $value = array_map('trim', $value);
        }

        return $this->setOption('regexflags', $value);
    }

    /**
     * Get regex flags.
     *
     * @return array
     */
    public function getRegexFlags()
    {
        $value = $this->getOption('regexflags');
        if (null === $value) {
            $value = [];
        }

        return $value;
    }

    /**
     * Set limit.
     *
     * If < 0 all terms are included
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setLimit($value)
    {
        return $this->setOption('limit', $value);
    }

    /**
     * Get limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->getOption('limit');
    }

    /**
     * Set the upperbound term to start at.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setUpperbound($value)
    {
        return $this->setOption('upperbound', $value);
    }

    /**
     * Get the upperbound term to start at.
     *
     * @return string
     */
    public function getUpperbound()
    {
        return $this->getOption('upperbound');
    }

    /**
     * Set upperboundinclude.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setUpperboundInclude($value)
    {
        return $this->setOption('upperboundinclude', $value);
    }

    /**
     * Get upperboundinclude.
     *
     * @return bool
     */
    public function getUpperboundInclude()
    {
        return $this->getOption('upperboundinclude');
    }

    /**
     * Set raw option.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setRaw($value)
    {
        return $this->setOption('raw', $value);
    }

    /**
     * Get raw option.
     *
     * @return bool
     */
    public function getRaw()
    {
        return $this->getOption('raw');
    }

    /**
     * Set sort option.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setSort($value)
    {
        return $this->setOption('sort', $value);
    }

    /**
     * Get sort option.
     *
     * @return string
     */
    public function getSort()
    {
        return $this->getOption('sort');
    }
}
