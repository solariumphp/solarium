<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet interval.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Interval_Faceting
 */
class Interval extends AbstractFacet
{
    use ExcludeTagsTrait {
        init as excludeTagsInit;
    }

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType()
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
    public function setField($field)
    {
        return $this->setOption('field', $field);
    }

    /**
     * Get the field name.
     *
     * @return string
     */
    public function getField()
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
    public function setSet($set)
    {
        if (is_string($set)) {
            $set = explode(',', $set);
            $set = array_map('trim', $set);
        }

        return $this->setOption('set', $set);
    }

    /**
     * Get set counts.
     *
     * @return array
     */
    public function getSet()
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
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        $this->excludeTagsInit();

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'set':
                    $this->setSet($value);
                    break;
            }
        }
    }
}
