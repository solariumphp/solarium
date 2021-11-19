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
}
