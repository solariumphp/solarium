<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
     */
    public function setPrefix(string $prefix): static
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
     */
    public function setContains(string $contains): static
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
     */
    public function setContainsIgnoreCase(bool $containsIgnoreCase): static
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
     */
    public function setMatches(string $matches): static
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
     * @param self::SORT_* $sort
     */
    public function setSort(string $sort): static
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
     */
    public function setLimit(int $limit): static
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
     */
    public function setOffset(int $offset): static
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
     */
    public function setMinCount(int $minCount): static
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
     */
    public function setMissing(bool $missing): static
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
     * @param self::METHOD_* $method
     */
    public function setMethod(string $method): static
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
     */
    public function setEnumCacheMinimumDocumentFrequency(int $frequency): static
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
     * @param bool $exists
     */
    public function setExists(bool $exists): static
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
     */
    public function setExcludeTerms(string $exclude): static
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
     */
    public function setOverrequestCount(int $count): static
    {
        $this->setOption('overrequest.count', $count);

        return $this;
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
     */
    public function setOverrequestRatio(float $ratio): static
    {
        $this->setOption('overrequest.ratio', $ratio);

        return $this;
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
     */
    public function setThreads(int $threads): static
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
