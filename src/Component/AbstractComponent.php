<?php

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Core\Configurable;
use Solarium\Core\Query\AbstractQuery;

/**
 * Query component base class.
 */
abstract class AbstractComponent extends Configurable
{
    /**
     * @var AbstractQuery
     */
    protected $queryInstance;

    /**
     * Get component type.
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Get the request builder class for this query.
     *
     * @return ComponentRequestBuilderInterface
     */
    abstract public function getRequestBuilder();

    /**
     * Get the response parser class for this query.
     *
     * @return ComponentParserInterface
     */
    abstract public function getResponseParser();

    /**
     * Set parent query instance.
     *
     * @param AbstractQuery $instance
     *
     * @return self Provides fluent interface
     */
    public function setQueryInstance(AbstractQuery $instance)
    {
        $this->queryInstance = $instance;

        return $this;
    }

    /**
     * Get parent query instance.
     *
     * @return AbstractQuery
     */
    public function getQueryInstance()
    {
        return $this->queryInstance;
    }
}
