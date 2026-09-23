<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;
use Solarium\Support\Utility;

/**
 * Facet interval.
 *
 * @see https://solr.apache.org/guide/faceting.html#interval-faceting
 */
class Interval extends AbstractFacet
{
    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::FACET_INTERVAL;
    }

    /**
     * Set the field name.
     *
     * @param string $field
     */
    public function setField(string $field): static
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
     * Set set counts.
     *
     * Use one of the constants as value.
     * If you want to use multiple values supply an array or comma separated string
     *
     * @param string|string[] $set
     */
    public function setSet(string|array $set): static
    {
        $this->setOption('set', Utility::stringOrArrayToArray($set));

        return $this;
    }

    /**
     * Get set counts.
     *
     * @return string[]
     */
    public function getSet(): array
    {
        return $this->getOption('set') ?? [];
    }

    /**
     * Initialize options.
     *
     * {@internal Several options need some extra checks or setup work,
     *            for these options the setters are called.}
     */
    protected function init(): void
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'set':
                    $this->setSet($value);
                    break;
            }
        }
    }
}
