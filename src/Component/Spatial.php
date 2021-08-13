<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\Spatial as RequestBuilder;

/**
 * Spatial component.
 *
 * @see https://solr.apache.org/guide/spatial-search.html
 */
class Spatial extends AbstractComponent
{
    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_SPATIAL;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * @param string $sfield
     *
     * @return self Provides fluent interface
     */
    public function setField(string $sfield): self
    {
        $this->setOption('sfield', $sfield);

        return $this;
    }

    /**
     * @param int $distance
     *
     * @return self Provides fluent interface
     */
    public function setDistance(int $distance): self
    {
        $this->setOption('d', $distance);

        return $this;
    }

    /**
     * @param string $point The center point using the format "lat,lon" if latitude & longitude. Otherwise, "x,y" for
     *                      PointType or "x y" for RPT field types.
     *
     * @return self Provides fluent interface
     */
    public function setPoint(string $point): self
    {
        $this->setOption('pt', $point);

        return $this;
    }

    /**
     * Get sfield option.
     *
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->getOption('sfield');
    }

    /**
     * Get d option.
     *
     * @return int|null
     */
    public function getDistance(): ?int
    {
        return $this->getOption('d');
    }

    /**
     * Get pt option.
     *
     * @return string|null
     */
    public function getPoint(): ?string
    {
        return $this->getOption('pt');
    }
}
