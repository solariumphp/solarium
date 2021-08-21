<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;
use Solarium\QueryType\Server\Query\Action\AbstractAsyncAction;

/**
 * Class Create.
 *
 * @see https://solr.apache.org/guide/coreadmin-api.html#coreadmin-create
 */
class Create extends AbstractAsyncAction implements CoreActionInterface
{
    /**
     * Returns the action type of the core admin action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CoreAdminQuery::ACTION_CREATE;
    }

    /**
     * Set the core name that should be reloaded.
     *
     * @param string $core
     *
     * @return self Provides fluent interface
     */
    public function setCore(string $core): CoreActionInterface
    {
        // for some reason the core is called "name" in the create action
        $this->setOption('name', $core);

        return $this;
    }

    /**
     * Get the related core name.
     *
     * @return string|null
     */
    public function getCore(): ?string
    {
        // for some reason the core is called "name" in the create action
        return $this->getOption('name');
    }

    /**
     * Set the instanceDir.
     *
     * @param string $instanceDir
     *
     * @return self Provides fluent interface
     */
    public function setInstanceDir(string $instanceDir): self
    {
        $this->setOption('instanceDir', $instanceDir);

        return $this;
    }

    /**
     * Get the instanceDir.
     *
     * @return string|null
     */
    public function getInstanceDir(): ?string
    {
        return (string) $this->getOption('instanceDir');
    }

    /**
     * Set the config.
     *
     * @param string $config
     *
     * @return self Provides fluent interface
     */
    public function setConfig(string $config): self
    {
        $this->setOption('config', $config);

        return $this;
    }

    /**
     * Get the config.
     *
     * @return string|null
     */
    public function getConfig(): ?string
    {
        return $this->getOption('config');
    }

    /**
     * Set the schema.
     *
     * @param string $schema
     *
     * @return self Provides fluent interface
     */
    public function setSchema(string $schema): self
    {
        $this->setOption('schema', $schema);

        return $this;
    }

    /**
     * Get the schema.
     *
     * @return string|null
     */
    public function getSchema(): ?string
    {
        return $this->getOption('schema');
    }

    /**
     * Set the dataDir.
     *
     * @param string $dataDir
     *
     * @return self Provides fluent interface
     */
    public function setDataDir(string $dataDir): self
    {
        $this->setOption('dataDir', $dataDir);

        return $this;
    }

    /**
     * Get the schema.
     *
     * @return string|null
     */
    public function getDataDir(): ?string
    {
        return $this->getOption('dataDir');
    }

    /**
     * Set the configSet.
     *
     * @param string $configSet
     *
     * @return self Provides fluent interface
     */
    public function setConfigSet(string $configSet): self
    {
        $this->setOption('configSet', $configSet);

        return $this;
    }

    /**
     * Get the configSet.
     *
     * @return string|null
     */
    public function getConfigSet(): ?string
    {
        return $this->getOption('configSet');
    }

    /**
     * Set the collection.
     *
     * @param string $collection
     *
     * @return self Provides fluent interface
     */
    public function setCollection(string $collection): self
    {
        $this->setOption('collection', $collection);

        return $this;
    }

    /**
     * Get the collection.
     *
     * @return string|null
     */
    public function getCollection(): ?string
    {
        return $this->getOption('collection');
    }

    /**
     * Set the shard.
     *
     * @param string $shard
     *
     * @return self Provides fluent interface
     */
    public function setShard($shard): self
    {
        $this->setOption('shard', $shard);

        return $this;
    }

    /**
     * Get the collection.
     *
     * @return string|null
     */
    public function getShard(): ?string
    {
        return $this->getOption('shard');
    }

    /**
     * Set the a property in the core.properties file.
     *
     * @param string $name
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setCoreProperty(string $name, string $value): self
    {
        $option = 'property.'.$name;
        $this->setOption($option, $value);

        return $this;
    }

    /**
     * Get a previously added core property.
     *
     * @param mixed $name
     *
     * @return string
     */
    public function getCoreProperty($name): string
    {
        $option = 'property.'.$name;

        return $this->getOption($option);
    }
}
