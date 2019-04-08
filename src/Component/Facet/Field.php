<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet query.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Field_Value_Faceting_Parameters
 */
class Field extends AbstractField implements ExcludeTagsInterface
{
    use ExcludeTagsTrait;

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
}
