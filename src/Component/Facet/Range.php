<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet range.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Facet_by_Range
 */
class Range extends AbstractRange
{
    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::FACET_RANGE;
    }

    /**
     * Set the facet mincount.
     *
     * @param int $minCount
     *
     * @return self Provides fluent interface
     */
    public function setMinCount(int $minCount): self
    {
        $this->setOption('mincount', $minCount);

        return $this;
    }

    /**
     * Get the facet mincount.
     *
     * @return int|null
     */
    public function getMinCount(): ?int
    {
        return $this->getOption('mincount');
    }

    /**
     * @param string $tag
     *
     * @return \Solarium\Core\Configurable
     */
    public function setTag($tag)
    {
        return $this->setOption('tag', $tag);
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->getOption('tag');
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        parent::init();

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'exclude':
                    $this->getLocalParameters()->addExcludes($value);
                    break;
            }
        }
    }
}
