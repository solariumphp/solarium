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
 * @see https://solr.apache.org/guide/faceting.html#field-value-faceting-parameters
 */
class Field extends AbstractFacet implements FieldValueParametersInterface
{
    use FieldValueParametersTrait;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'field' => 'id',
    ];

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
     * Set the name of the field that should be treated as a facet.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function setField(string $field): self
    {
        $this->setOption('field', $field);

        return $this;
    }

    /**
     * Get the field name.
     *
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->getOption('field');
    }

    /**
     * Add a term.
     *
     * @param string $term
     *
     * @return self Provides fluent interface
     */
    public function addTerm(string $term): self
    {
        $this->getLocalParameters()->setTerm($term);

        return $this;
    }

    /**
     * Add multiple terms.
     *
     * @param array|string $terms array or string with comma separated terms
     *
     * @return self Provides fluent interface
     */
    public function addTerms($terms): self
    {
        if (\is_string($terms)) {
            $terms = preg_split('/(?<!\\\\),/', $terms);
        }

        $this->getLocalParameters()->addTerms($terms);

        return $this;
    }

    /**
     * Set the list of terms.
     *
     * This overwrites any existing terms.
     *
     * @param array|string $terms
     *
     * @return self Provides fluent interface
     */
    public function setTerms($terms): self
    {
        $this->clearTerms()->addTerms($terms);

        return $this;
    }

    /**
     * Remove a single term.
     *
     * @param string $term
     *
     * @return self Provides fluent interface
     */
    public function removeTerm(string $term): self
    {
        $this->getLocalParameters()->removeTerm($term);

        return $this;
    }

    /**
     * Remove all terms.
     *
     * @return self Provides fluent interface
     */
    public function clearTerms(): self
    {
        $this->getLocalParameters()->clearTerms();

        return $this;
    }

    /**
     * Get the list of terms.
     *
     * @return array
     */
    public function getTerms(): array
    {
        return $this->getLocalParameters()->getTerms();
    }
}
