<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\ResponseParser\ComponentParserInterface;
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
    abstract public function getType(): string;

    /**
     * Get the request builder class for this query.
     *
     * @return ComponentRequestBuilderInterface
     */
    abstract public function getRequestBuilder(): ComponentRequestBuilderInterface;

    /**
     * This component has no response parser...
     *
     * @return \Solarium\Component\ResponseParser\ComponentParserInterface|null
     */
    public function getResponseParser(): ?ComponentParserInterface
    {
        return null;
    }

    /**
     * Set parent query instance.
     *
     * @param AbstractQuery $instance
     *
     * @return self Provides fluent interface
     */
    public function setQueryInstance(AbstractQuery $instance): self
    {
        $this->queryInstance = $instance;

        return $this;
    }

    /**
     * Get parent query instance.
     *
     * @return AbstractQuery|null
     */
    public function getQueryInstance(): ?AbstractQuery
    {
        return $this->queryInstance;
    }

    /**
     * Returns a query helper.
     *
     * @return \Solarium\Core\Query\Helper
     */
    public function getHelper(): Helper
    {
        if ($queryInstance = $this->getQueryInstance()) {
            return $queryInstance->getHelper();
        }

        return new Helper();
    }
}
