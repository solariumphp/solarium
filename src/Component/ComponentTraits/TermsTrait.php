<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\ComponentTraits;

use Solarium\Component\TermsInterface;

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
    public function setFields($value): TermsInterface
    {
        if (\is_string($value)) {
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
    public function getFields(): array
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
    public function setLowerbound(string $value): TermsInterface
    {
        return $this->setOption('lowerbound', $value);
    }

    /**
     * Get the lowerbound term to start at.
     *
     * @return string|null
     */
    public function getLowerbound(): ?string
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
    public function setLowerboundInclude(bool $value): TermsInterface
    {
        return $this->setOption('lowerboundinclude', $value);
    }

    /**
     * Get lowerboundinclude.
     *
     * @return bool|null
     */
    public function getLowerboundInclude(): ?bool
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
    public function setMinCount(int $value): TermsInterface
    {
        return $this->setOption('mincount', $value);
    }

    /**
     * Get mincount.
     *
     * @return int|null
     */
    public function getMinCount(): ?int
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
    public function setMaxCount(int $value): TermsInterface
    {
        return $this->setOption('maxcount', $value);
    }

    /**
     * Get maxcount.
     *
     * @return int|null
     */
    public function getMaxCount(): ?int
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
    public function setPrefix(string $value): TermsInterface
    {
        return $this->setOption('prefix', $value);
    }

    /**
     * Get prefix for terms.
     *
     * @return string|null
     */
    public function getPrefix(): ?string
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
    public function setRegex(string $value): TermsInterface
    {
        return $this->setOption('regex', $value);
    }

    /**
     * Get regex.
     *
     * @return string|null
     */
    public function getRegex(): ?string
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
    public function setRegexFlags($value): TermsInterface
    {
        if (\is_string($value)) {
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
    public function getRegexFlags(): array
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
    public function setLimit(int $value): TermsInterface
    {
        return $this->setOption('limit', $value);
    }

    /**
     * Get limit.
     *
     * @return int|null
     */
    public function getLimit(): ?int
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
    public function setUpperbound(string $value): TermsInterface
    {
        return $this->setOption('upperbound', $value);
    }

    /**
     * Get the upperbound term to start at.
     *
     * @return string|null
     */
    public function getUpperbound(): ?string
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
    public function setUpperboundInclude(bool $value): TermsInterface
    {
        return $this->setOption('upperboundinclude', $value);
    }

    /**
     * Get upperboundinclude.
     *
     * @return bool|null
     */
    public function getUpperboundInclude(): ?bool
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
    public function setRaw(bool $value): TermsInterface
    {
        return $this->setOption('raw', $value);
    }

    /**
     * Get raw option.
     *
     * @return bool|null
     */
    public function getRaw(): ?bool
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
    public function setSort(string $value): TermsInterface
    {
        return $this->setOption('sort', $value);
    }

    /**
     * Get sort option.
     *
     * @return string|null
     */
    public function getSort(): ?string
    {
        return $this->getOption('sort');
    }
}
