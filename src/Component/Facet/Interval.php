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
     * Set set counts.
     *
     * Use one of the constants as value.
     * If you want to use multiple values supply an array or comma separated string
     *
     * @param string|array $set
     *
     * @return self Provides fluent interface
     */
    public function setSet($set): self
    {
        if (\is_string($set)) {
            $set = explode(',', $set);
            $set = array_map('trim', $set);
        }

        $this->setOption('set', $set);

        return $this;
    }

    /**
     * Get set counts.
     *
     * @return array
     */
    public function getSet(): array
    {
        $set = $this->getOption('set');
        if (null === $set) {
            $set = [];
        }

        return $set;
    }

    /**
     * Initialize options.
     *
     * {@internal Several options need some extra checks or setup work,
     *            for these options the setters are called.}
     */
    protected function init()
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
