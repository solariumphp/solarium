<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet query.
 *
 * @see https://lucene.apache.org/solr/guide/faceting.html#field-value-faceting-parameters
 */
class Field extends AbstractField
{
    /**
     * Facet method enum.
     */
    const METHOD_ENUM = 'enum';

    /**
     * Facet method fc.
     */
    const METHOD_FC = 'fc';

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::FACET_FIELD;
    }

    /**
     * Limit the terms for faceting by a string they must contain. Since Solr 5.1.
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
     * Case sensitivity of matching string that facet terms must contain. Since Solr 5.1.
     *
     * @param bool $containsIgnoreCase
     *
     * @return self Provides fluent interface
     */
    public function setContainsIgnoreCase($containsIgnoreCase): self
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
     * Limit facet terms to those matching this regular expression. Since Solr 7.2.
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
     * Exclude these terms, comma separated list. Use \, for literal comma. Since Solr 6.5.
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
}
