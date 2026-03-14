<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

/**
 * Field-Value Faceting Parameters interface.
 */
interface FieldValueParametersInterface
{
    /**
     * Facet method enum.
     */
    public const METHOD_ENUM = 'enum';

    /**
     * Facet method fc.
     */
    public const METHOD_FC = 'fc';

    /**
     * Facet method fcs.
     */
    public const METHOD_FCS = 'fcs';

    /**
     * Facet method uif.
     */
    public const METHOD_UIF = 'uif';

    /**
     * Facet sort type count.
     */
    public const SORT_COUNT = 'count';

    /**
     * Facet sort type index.
     */
    public const SORT_INDEX = 'index';

    /**
     * Limit the terms for faceting by a prefix.
     *
     * @param string $prefix
     */
    public function setPrefix(string $prefix): static;

    /**
     * Get the facet prefix.
     *
     * @return string|null
     */
    public function getPrefix(): ?string;

    /**
     * Limit the terms for faceting by a string they must contain.
     *
     * @param string $contains
     *
     * @return self Provides fluent interface
     */
    public function setContains(string $contains): self;

    /**
     * Get the facet contains.
     *
     * @return string|null
     */
    public function getContains(): ?string;

    /**
     * Case sensitivity of matching string that facet terms must contain.
     *
     * @param bool $containsIgnoreCase
     */
    public function setContainsIgnoreCase(bool $containsIgnoreCase): static;

    /**
     * Get the case sensitivity of facet contains.
     *
     * @return bool|null
     */
    public function getContainsIgnoreCase(): ?bool;

    /**
     * Limit facet terms to those matching this regular expression.
     *
     * @param string $matches
     */
    public function setMatches(string $matches): static;

    /**
     * Get the regular expression string that facets must match.
     *
     * @return string|null
     */
    public function getMatches(): ?string;

    /**
     * Set the facet sort type.
     *
     * @param self::SORT_* $sort
     */
    public function setSort(string $sort): static;

    /**
     * Get the facet sort type.
     *
     * @return string|null
     */
    public function getSort(): ?string;

    /**
     * Set the facet limit.
     *
     * @param int $limit
     */
    public function setLimit(int $limit): static;

    /**
     * Get the facet limit.
     *
     * @return int|null
     */
    public function getLimit(): ?int;

    /**
     * Set the facet offset.
     *
     * @param int $offset
     */
    public function setOffset(int $offset): static;

    /**
     * Get the facet offset.
     *
     * @return int|null
     */
    public function getOffset(): ?int;

    /**
     * Set the facet mincount.
     *
     * @param int $minCount
     */
    public function setMinCount(int $minCount): static;

    /**
     * Get the facet mincount.
     *
     * @return int|null
     */
    public function getMinCount(): ?int;

    /**
     * Set the missing count option.
     *
     * @param bool $missing
     */
    public function setMissing(bool $missing): static;

    /**
     * Get the facet missing option.
     *
     * @return bool|null
     */
    public function getMissing(): ?bool;

    /**
     * Set the facet method.
     *
     * @param self::METHOD_* $method
     */
    public function setMethod(string $method): static;

    /**
     * Get the facet method.
     *
     * @return string|null
     */
    public function getMethod(): ?string;

    /**
     * Set the minimum document frequency for which the filterCache should be used.
     *
     * This is only used with METHOD_ENUM.
     *
     * @param int $frequency
     */
    public function setEnumCacheMinimumDocumentFrequency(int $frequency): static;

    /**
     * Get the minimum document frequency for which the filterCache should be used.
     *
     * @return int|null
     */
    public function getEnumCacheMinimumDocumentFrequency(): ?int;

    /**
     * Set to true to cap facet counts by 1.
     *
     * @param bool $exists
     */
    public function setExists(bool $exists): static;

    /**
     * Get the exists parameter.
     *
     * @return bool|null
     */
    public function getExists(): ?bool;

    /**
     * Exclude these terms from facet counts.
     *
     * Specify a comma separated list. Use \, for a literal comma.
     *
     * @param string $exclude
     */
    public function setExcludeTerms(string $exclude): static;

    /**
     * Get terms that should be excluded from the facet.
     *
     * @return string|null
     */
    public function getExcludeTerms(): ?string;

    /**
     * Set the facet overrequest count.
     *
     * @param int $count
     */
    public function setOverrequestCount(int $count): static;

    /**
     * Get the facet overrequest count.
     *
     * @return int|null
     */
    public function getOverrequestCount(): ?int;

    /**
     * Set the facet overrequest ratio.
     *
     * @param float $ratio
     */
    public function setOverrequestRatio(float $ratio): static;

    /**
     * Get the facet overrequest ratio.
     *
     * @return float|null
     */
    public function getOverrequestRatio(): ?float;

    /**
     * Set the maximum number of threads used for parallel execution.
     *
     * Omitting or specifying 0 uses only the main request thread.
     *
     * Specifying a negative number will create up to (Java's) Integer.MAX_VALUE threads.
     *
     * @param int $threads
     */
    public function setThreads(int $threads): static;

    /**
     * Get the maximum number of threads used for parallel execution.
     *
     * @return int|null
     */
    public function getThreads(): ?int;
}
