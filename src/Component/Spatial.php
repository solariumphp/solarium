<?php

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\Spatial as RequestBuilder;

/**
 * Spatial component.
 *
 * @see https://cwiki.apache.org/confluence/display/solr/Spatial+Search
 */
class Spatial extends AbstractComponent
{
    /**
     * Get component type.
     *
     * @return string
     */
    public function getType()
    {
        return ComponentAwareQueryInterface::COMPONENT_SPATIAL;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * @param string $sfield
     */
    public function setField($sfield)
    {
        $this->setOption('sfield', $sfield);
    }

    /**
     * @param int $distance
     */
    public function setDistance($distance)
    {
        $this->setOption('d', $distance);
    }

    /**
     * @param string $point
     */
    public function setPoint($point)
    {
        $this->setOption('pt', $point);
    }

    /**
     * Get sfield option.
     *
     * @return string|null
     */
    public function getField()
    {
        return $this->getOption('sfield');
    }

    /**
     * Get d option.
     *
     * @return int|null
     */
    public function getDistance()
    {
        return $this->getOption('d');
    }

    /**
     * Get pt option.
     *
     * @return int|null
     */
    public function getPoint()
    {
        return $this->getOption('pt');
    }
}
