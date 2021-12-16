<?php

namespace Solarium\Component\Facet;

/**
 * Field-Value Faceting Parameters trait.
 */
trait FieldValueParametersTrait
{
    /**
     * Limit the terms for faceting by a prefix.
     *
     * @param string $prefix
     *
     * @return self Provides fluent interface
     */
    public function setPrefix(string $prefix): self
    {
        $this->setOption('prefix', $prefix);

        return $this;
    }

    /**
     * Get the facet prefix.
     *
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->getOption('prefix');
    }

    /**
     * Limit the terms for faceting by a string they must contain.
     *
     * @param string $contains
     *
     * @return self Provides fluent interface
     */
    public function setContains(string $contains): self
    {
        $this->setOption('contains', $contains);

        return $this;
    }

    /**
     * Get the facet contains.
     *
     * @return string|null
     */
    public function getContains(): ?string
    {
        return $this->getOption('contains');
    }

    /**
     * Case sensitivity of matching string that facet terms must contain.
     *
     * @param bool $containsIgnoreCase
     *
     * @return self Provides fluent interface
     */
    public function setContainsIgnoreCase(bool $containsIgnoreCase): self
    {
        $this->setOption('containsignorecase', $containsIgnoreCase);

        return $this;
    }

    /**
     * Get the case sensitivity of facet contains.
     *
     * @return bool|null
     */
    public function getContainsIgnoreCase(): ?bool
    {
        return $this->getOption('containsignorecase');
    }

    /**
     * Limit facet terms to those matching this regular expression.
     *
     * @param string $matches
     *
     * @return self Provides fluent interface
     */
    public function setMatches(string $matches): self
    {
        $this->setOption('matches', $matches);

        return $this;
    }

    /**
     * Get the regular expression string that facets must match.
     *
     * @return string|null
     */
    public function getMatches(): ?string
    {
        return $this->getOption('matches');
    }

    /**
     * Set the facet sort type.
     *
     * Use one of the SORT_* constants as the value.
     *
     * @param string $sort
     *
     * @return self Provides fluent interface
     */
    public function setSort(string $sort): self
    {
        $this->setOption('sort', $sort);

        return $this;
    }

    /**
     * Get the facet sort order.
     *
     * @return string|null
     */
    public function getSort(): ?string
    {
        return $this->getOption('sort');
    }

    /**
     * Set the facet limit.
     *
     * @param int $limit
     *
     * @return self Provides fluent interface
     */
    public function setLimit(int $limit): self
    {
        $this->setOption('limit', $limit);

        return $this;
    }

    /**
     * Get the facet limit.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->getOption('limit');
    }

    /**
     * Set the facet offset.
     *
     * @param int $offset
     *
     * @return self Provides fluent interface
     */
    public function setOffset(int $offset): self
    {
        $this->setOption('offset', $offset);

        return $this;
    }

    /**
     * Get the facet offset.
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->getOption('offset');
    }

    /**
     * Set the facet mincount.
     *
     * @param int $minCount
     *
     * @return self Provides fluent interface
     */
    public function setMinCount(int $minCount): self
    {
        $this->setOption('mincount', $minCount);

        return $this;
    }

    /**
     * Get the facet mincount.
     *
     * @return int|null
     */
    public function getMinCount(): ?int
    {
        return $this->getOption('mincount');
    }

    /**
     * Set the missing count option.
     *
     * @param bool $missing
     *
     * @return self Provides fluent interface
     */
    public function setMissing(bool $missing): self
    {
        $this->setOption('missing', $missing);

        return $this;
    }

    /**
     * Get the facet missing option.
     *
     * @return bool|null
     */
    public function getMissing(): ?bool
    {
        return $this->getOption('missing');
    }

    /**
     * Set the facet method.
     *
     * Use one of the METHOD_* constants as value.
     *
     * @param string $method
     *
     * @return self Provides fluent interface
     */
    public function setMethod(string $method): self
    {
        $this->setOption('method', $method);

        return $this;
    }

    /**
     * Get the facet method.
     *
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->getOption('method');
    }

    /**
     * Set the minimum document frequency for which the filterCache should be used.
     *
     * This is only used with METHOD_ENUM.
     *
     * @param int $frequency
     *
     * @return self Provides fluent interface
     */
    public function setEnumCacheMinimumDocumentFrequency($frequency): self
    {
        $this->setOption('enum.cache.minDf', $frequency);

        return $this;
    }

    /**
     * Get the minimum document frequency for which the filterCache should be used.
     *
     * @return int|null
     */
    public function getEnumCacheMinimumDocumentFrequency(): ?int
    {
        return $this->getOption('enum.cache.minDf');
    }

    /**
     * Set to true to cap facet counts by 1.
     *
     * @param int $exists
     *
     * @return self Provides fluent interface
     */
    public function setExists(bool $exists): self
    {
        $this->setOption('exists', $exists);

        return $this;
    }

    /**
     * Get the exists parameter.
     *
     * @return bool|null
     */
    public function getExists(): ?bool
    {
        return $this->getOption('exists');
    }

    /**
     * Exclude these terms from facet counts.
     *
     * Specify a comma separated list. Use \, for a literal comma.
     *
     * @param string $exclude
     *
     * @return self Provides fluent interface
     */
    public function setExcludeTerms(string $exclude): self
    {
        $this->setOption('excludeTerms', $exclude);

        return $this;
    }

    /**
     * Get terms that should be excluded from the facet.
     *
     * @return string|null
     */
    public function getExcludeTerms(): ?string
    {
        return $this->getOption('excludeTerms');
    }

    /**
     * Set the facet overrequest count.
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setOverrequestCount($count): self
    {
        return $this->setOption('overrequest.count', $count);
    }

    /**
     * Get the facet overrequest count.
     *
     * @return int|null
     */
    public function getOverrequestCount(): ?int
    {
        return $this->getOption('overrequest.count');
    }

    /**
     * Set the facet overrequest ratio.
     *
     * @param float $ratio
     *
     * @return self Provides fluent interface
     */
    public function setOverrequestRatio($ratio): self
    {
        return $this->setOption('overrequest.ratio', $ratio);
    }

    /**
     * Get the facet overrequest ratio.
     *
     * @return float|null
     */
    public function getOverrequestRatio(): ?float
    {
        return $this->getOption('overrequest.ratio');
    }

    /**
     * Set the maximum number of threads used for parallel execution.
     *
     * Omitting or specifying 0 uses only the main request thread.
     *
     * Specifying a negative number will create up to (Java's) Integer.MAX_VALUE threads.
     *
     * @param int $threads
     *
     * @return self Provides fluent interface
     */
    public function setThreads(int $threads): self
    {
        $this->setOption('threads', $threads);

        return $this;
    }

    /**
     * Get the maximum number of threads used for parallel execution.
     *
     * @return int|null
     */
    public function getThreads(): ?int
    {
        return $this->getOption('threads');
    }
}
