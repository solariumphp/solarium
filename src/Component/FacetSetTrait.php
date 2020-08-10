<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\Facet\FacetInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;

/**
 * FacetSet trait.
 */
trait FacetSetTrait
{
    /**
     * Facets.
     *
     * @var FacetInterface[]
     */
    protected $facets = [];

    /**
     * Add a facet.
     *
     * @param \Solarium\Component\Facet\FacetInterface|array $facet
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addFacet($facet): FacetSetInterface
    {
        if (\is_array($facet)) {
            $facet = $this->createFacet($facet['type'], $facet, false);
        }

        $key = $facet->getKey();

        if (0 === \strlen($key)) {
            throw new InvalidArgumentException('A facet must have a key value');
        }

        //double add calls for the same facet are ignored, but non-unique keys cause an exception
        if (\array_key_exists($key, $this->facets) && $this->facets[$key] !== $facet) {
            throw new InvalidArgumentException('A facet must have a unique key value within a query');
        }

        $this->facets[$key] = $facet;

        return $this;
    }

    /**
     * Add multiple facets.
     *
     * @param array $facets
     *
     * @return self Provides fluent interface
     */
    public function addFacets(array $facets): FacetSetInterface
    {
        foreach ($facets as $key => $facet) {
            // in case of a config array: add key to config
            if (\is_array($facet) && !isset($facet['local_key'])) {
                $facet['local_key'] = $key;
            }

            $this->addFacet($facet);
        }

        return $this;
    }

    /**
     * Get a facet.
     *
     * @param string $key
     *
     * @return FacetInterface|null
     */
    public function getFacet(string $key): ?FacetInterface
    {
        return $this->facets[$key] ?? null;
    }

    /**
     * Get all facets.
     *
     * @return FacetInterface[]
     */
    public function getFacets(): array
    {
        return $this->facets;
    }

    /**
     * Remove a single facet.
     *
     * You can remove a facet by passing its key or the facet instance
     *
     * @param string|\Solarium\Component\Facet\FacetInterface $facet
     *
     * @return self Provides fluent interface
     */
    public function removeFacet($facet): FacetSetInterface
    {
        if (\is_object($facet)) {
            $facet = $facet->getKey();
        }

        if (isset($this->facets[$facet])) {
            unset($this->facets[$facet]);
        }

        return $this;
    }

    /**
     * Remove all facets.
     *
     * @return self Provides fluent interface
     */
    public function clearFacets(): FacetSetInterface
    {
        $this->facets = [];

        return $this;
    }

    /**
     * Set multiple facets.
     *
     * This overwrites any existing facets
     *
     * @param array $facets
     *
     * @return self
     */
    public function setFacets(array $facets): FacetSetInterface
    {
        $this->clearFacets();
        $this->addFacets($facets);

        return $this;
    }

    /**
     * Create a facet instance.
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the facet
     * and it will be added to this query.
     * If you supply an options array/object that contains a key the facet will also be added to the query.
     *
     * When no key is supplied the facet cannot be added, in that case you will need to add it manually
     * after setting the key, by using the addFacet method.
     *
     * @param string            $type
     * @param array|object|null $options
     * @param bool              $add
     *
     * @throws OutOfBoundsException
     *
     * @return \Solarium\Component\Facet\FacetInterface
     */
    public function createFacet(string $type, $options = null, bool $add = true): FacetInterface
    {
        $type = strtolower($type);

        if (!isset($this->facetTypes[$type])) {
            throw new OutOfBoundsException(sprintf('Facettype unknown: %s', $type));
        }

        $class = $this->facetTypes[$type];

        if (\is_string($options)) {
            /** @var FacetInterface $facet */
            $facet = new $class();
            $facet->setKey($options);
        } else {
            $facet = new $class($options);
        }

        if ($add && null !== $facet->getKey()) {
            $this->addFacet($facet);
        }

        return $facet;
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

        if (isset($this->options['facet'])) {
            foreach ($this->options['facet'] as $key => $config) {
                if (!isset($config['local_key'])) {
                    $config['local_key'] = (string) $key;
                }

                $this->addFacet($config);
            }
        }
    }
}
