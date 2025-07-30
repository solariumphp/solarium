<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Configsets\Query\Action;

use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Configsets\Result\ConfigsetsResult;
use Solarium\QueryType\Server\Query\Action\AbstractAction;
use Solarium\QueryType\Server\Query\Action\NameParameterTrait;

/**
 * Class Create.
 *
 * @see https://solr.apache.org/guide/configsets-api.html#configsets-create
 */
class Create extends AbstractAction
{
    use NameParameterTrait;

    /**
     * Returns the action type of the Configsets API action.
     *
     * @return string
     */
    public function getType(): string
    {
        return ConfigsetsQuery::ACTION_CREATE;
    }

    /**
     * The name of the configset to copy as a base. This defaults to "_default".
     *
     * @param string $baseConfigSet
     *
     * @return self Provides fluent interface
     */
    public function setBaseConfigSet(string $baseConfigSet): self
    {
        $this->setOption('baseConfigSet', $baseConfigSet);

        return $this;
    }

    /**
     * Returns the base configset.
     *
     * @return string|null
     */
    public function getBaseConfigSet(): ?string
    {
        return $this->getOption('baseConfigSet');
    }

    /**
     * Set configset property name to value.
     *
     * @param string $name
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setProperty(string $name, string $value): self
    {
        $this->setOption('configSetProp.'.$name, $value);

        return $this;
    }

    /**
     * Get a previously added property.
     *
     * @param string $name property name
     *
     * @return string|null
     */
    public function getProperty(string $name): ?string
    {
        return $this->getOption('configSetProp.'.$name);
    }

    /**
     * Returns the namespace and class of the result class for the action.
     *
     * @return string
     */
    public function getResultClass(): string
    {
        return ConfigsetsResult::class;
    }
}
