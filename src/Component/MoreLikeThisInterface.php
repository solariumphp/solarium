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
 * MoreLikeThis Interface.
 */
interface MoreLikeThisInterface extends ConfigurableInterface
{
    /**
     * Set minimumtermfrequency option.
     *
     * Minimum Term Frequency - the frequency below which terms will be ignored
     * in the source doc.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $minimum
     *
     * @return self Provides fluent interface
     */
    public function setMinimumTermFrequency(int $minimum): self;

    /**
     * Get minimumtermfrequency option.
     *
     * @return int|null
     */
    public function getMinimumTermFrequency(): ?int;

    /**
     * Set minimumdocumentfrequency option.
     *
     * Minimum Document Frequency - the frequency at which words will be
     * ignored which do not occur in at least this many docs.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $minimum
     *
     * @return self Provides fluent interface
     */
    public function setMinimumDocumentFrequency(int $minimum): self;

    /**
     * Get minimumdocumentfrequency option.
     *
     * @return int|null
     */
    public function getMinimumDocumentFrequency(): ?int;

    /**
     * Set maximumdocumentfrequency option.
     *
     * Maximum Document Frequency - the frequency at which words will be
     * ignored which occur in more than this many docs.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaximumDocumentFrequency(int $maximum): self;

    /**
     * Get maximumdocumentfrequency option.
     *
     * @return int|null
     */
    public function getMaximumDocumentFrequency(): ?int;

    /**
     * Set maximumdocumentfrequencypercentage option.
     *
     * Maximum Document Frequency Percentage - a relative ratio at which words will be
     * ignored which occur in more than this percentage of the docs in the index.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $maximumpercentage A percentage between 0 and 100
     *
     * @throws \Solarium\Exception\DomainException
     *
     * @return self Provides fluent interface
     */
    public function setMaximumDocumentFrequencyPercentage(int $maximumpercentage): self;

    /**
     * Get maximumdocumentfrequencypercentage option.
     *
     * @return int|null
     */
    public function getMaximumDocumentFrequencyPercentage(): ?int;

    /**
     * Set minimumwordlength option.
     *
     * Minimum word length below which words will be ignored.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $minimum
     *
     * @return self Provides fluent interface
     */
    public function setMinimumWordLength(int $minimum): self;

    /**
     * Get minimumwordlength option.
     *
     * @return int|null
     */
    public function getMinimumWordLength(): ?int;

    /**
     * Set maximumwordlength option.
     *
     * Maximum word length above which words will be ignored.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaximumWordLength(int $maximum): self;

    /**
     * Get maximumwordlength option.
     *
     * @return int|null
     */
    public function getMaximumWordLength(): ?int;

    /**
     * Set maximumqueryterms option.
     *
     * Maximum number of query terms that will be included in any generated
     * query.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaximumQueryTerms(int $maximum): self;

    /**
     * Get maximumqueryterms option.
     *
     * @return int|null
     */
    public function getMaximumQueryTerms(): ?int;

    /**
     * Set maximumnumberoftokens option.
     *
     * Maximum number of tokens to parse in each example doc field that is not
     * stored with TermVector support.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $maximum
     *
     * @return self Provides fluent interface
     */
    public function setMaximumNumberOfTokens(int $maximum): self;

    /**
     * Get maximumnumberoftokens option.
     *
     * @return int|null
     */
    public function getMaximumNumberOfTokens(): ?int;

    /**
     * Set boost option.
     *
     * If true the query will be boosted by the interesting term relevance.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param bool $boost
     *
     * @return self Provides fluent interface
     */
    public function setBoost(bool $boost): self;

    /**
     * Get boost option.
     *
     * @return bool|null
     */
    public function getBoost(): ?bool;

    /**
     * Set queryfields option.
     *
     * Query fields and their boosts using the same format as that used in
     * DisMaxQParserPlugin. These fields must also be specified in fields.
     *
     * Separate multiple fields with commas if you use string input.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param string|array $queryFields
     *
     * @return self Provides fluent interface
     */
    public function setQueryFields($queryFields): self;

    /**
     * Get queryfields option.
     *
     * @return array
     */
    public function getQueryFields(): array;

    /**
     * Set the interestingTerms parameter. Must be one of: none, list, details.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param string $term
     *
     * @return self Provides fluent interface
     */
    public function setInterestingTerms(string $term): self;

    /**
     * Get the interestingTerms parameter.
     *
     * @return string|null
     */
    public function getInterestingTerms(): ?string;
}
