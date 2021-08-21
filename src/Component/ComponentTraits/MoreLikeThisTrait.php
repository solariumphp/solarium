<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\ComponentTraits;

use Solarium\Component\MoreLikeThisInterface;
use Solarium\Exception\DomainException;

/**
 * MoreLikeThis Query Trait.
 */
trait MoreLikeThisTrait
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
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setMinimumTermFrequency(int $minimum): MoreLikeThisInterface
    {
        $this->setOption('minimumtermfrequency', $minimum);

        return $this;
    }

    /**
     * Get minimumtermfrequency option.
     *
     * @return int|null
     */
    public function getMinimumTermFrequency(): ?int
    {
        return $this->getOption('minimumtermfrequency');
    }

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
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setMinimumDocumentFrequency(int $minimum): MoreLikeThisInterface
    {
        $this->setOption('minimumdocumentfrequency', $minimum);

        return $this;
    }

    /**
     * Get minimumdocumentfrequency option.
     *
     * @return int|null
     */
    public function getMinimumDocumentFrequency(): ?int
    {
        return $this->getOption('minimumdocumentfrequency');
    }

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
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setMaximumDocumentFrequency(int $maximum): MoreLikeThisInterface
    {
        $this->setOption('maximumdocumentfrequency', $maximum);

        return $this;
    }

    /**
     * Get maximumdocumentfrequency option.
     *
     * @return int|null
     */
    public function getMaximumDocumentFrequency(): ?int
    {
        return $this->getOption('maximumdocumentfrequency');
    }

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
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setMaximumDocumentFrequencyPercentage(int $maximumpercentage): MoreLikeThisInterface
    {
        if (0 > $maximumpercentage || 100 < $maximumpercentage) {
            throw new DomainException(sprintf('Maximum percentage %d is not between 0 and 100.', $maximumpercentage));
        }

        $this->setOption('maximumdocumentfrequencypercentage', $maximumpercentage);

        return $this;
    }

    /**
     * Get maximumdocumentfrequencypercentage option.
     *
     * @return int|null
     */
    public function getMaximumDocumentFrequencyPercentage(): ?int
    {
        return $this->getOption('maximumdocumentfrequencypercentage');
    }

    /**
     * Set minimumwordlength option.
     *
     * Minimum word length below which words will be ignored.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $minimum
     *
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setMinimumWordLength(int $minimum): MoreLikeThisInterface
    {
        $this->setOption('minimumwordlength', $minimum);

        return $this;
    }

    /**
     * Get minimumwordlength option.
     *
     * @return int|null
     */
    public function getMinimumWordLength(): ?int
    {
        return $this->getOption('minimumwordlength');
    }

    /**
     * Set maximumwordlength option.
     *
     * Maximum word length above which words will be ignored.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param int $maximum
     *
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setMaximumWordLength(int $maximum): MoreLikeThisInterface
    {
        $this->setOption('maximumwordlength', $maximum);

        return $this;
    }

    /**
     * Get maximumwordlength option.
     *
     * @return int|null
     */
    public function getMaximumWordLength(): ?int
    {
        return $this->getOption('maximumwordlength');
    }

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
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setMaximumQueryTerms(int $maximum): MoreLikeThisInterface
    {
        $this->setOption('maximumqueryterms', $maximum);

        return $this;
    }

    /**
     * Get maximumqueryterms option.
     *
     * @return int|null
     */
    public function getMaximumQueryTerms(): ?int
    {
        return $this->getOption('maximumqueryterms');
    }

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
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setMaximumNumberOfTokens(int $maximum): MoreLikeThisInterface
    {
        $this->setOption('maximumnumberoftokens', $maximum);

        return $this;
    }

    /**
     * Get maximumnumberoftokens option.
     *
     * @return int|null
     */
    public function getMaximumNumberOfTokens(): ?int
    {
        return $this->getOption('maximumnumberoftokens');
    }

    /**
     * Set boost option.
     *
     * If true the query will be boosted by the interesting term relevance.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param bool $boost
     *
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setBoost(bool $boost): MoreLikeThisInterface
    {
        $this->setOption('boost', $boost);

        return $this;
    }

    /**
     * Get boost option.
     *
     * @return bool|null
     */
    public function getBoost(): ?bool
    {
        return $this->getOption('boost');
    }

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
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setQueryFields($queryFields): MoreLikeThisInterface
    {
        if (\is_string($queryFields)) {
            $queryFields = explode(',', $queryFields);
            $queryFields = array_map('trim', $queryFields);
        }

        $this->setOption('queryfields', $queryFields);

        return $this;
    }

    /**
     * Get queryfields option.
     *
     * @return array
     */
    public function getQueryFields(): array
    {
        $queryfields = $this->getOption('queryfields');
        if (null === $queryfields) {
            $queryfields = [];
        }

        return $queryfields;
    }

    /**
     * Set the interestingTerms parameter. Must be one of: none, list, details.
     *
     * Controls how the component presents the "interesting" terms.
     *
     * @see https://solr.apache.org/guide/morelikethis.html#common-handler-and-component-parameters
     *
     * @param string $setting
     *
     * @return MoreLikeThisInterface Provides fluent interface
     */
    public function setInterestingTerms(string $setting): MoreLikeThisInterface
    {
        $this->setOption('interestingTerms', $setting);

        return $this;
    }

    /**
     * Get the interestingTerms parameter.
     *
     * @return string|null
     */
    public function getInterestingTerms(): ?string
    {
        return $this->getOption('interestingTerms');
    }
}
