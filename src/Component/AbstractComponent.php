<?php

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Core\Configurable;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Helper;

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
     * This component has no response parser...
     */
    public function getResponseParser()
    {
    }

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

    /**
     * Returns a query helper.
     *
     * @return \Solarium\Core\Query\Helper
     */
    public function getHelper()
    {
        if ($queryInstance = $this->getQueryInstance()) {
            return $this->getQueryInstance()->getHelper();
        } else {
            return new Helper();
        }
    }
}
