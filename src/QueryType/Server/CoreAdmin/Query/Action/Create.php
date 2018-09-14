<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

/**
 * Class Create.
 *
 * @see https://lucene.apache.org/solr/guide/6_6/coreadmin-api.html#CoreAdminAPI-Input.1
 */
class Create extends AbstractAsyncAction
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
     */
    public function setCore(string $core)
    {
        // for some reason the core is called "name" in the create action
        $this->setOption('name', $core);
    }

    /**
     * Get the related core name.
     *
     * @return string
     */
    public function getCore(): string
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
    public function setInstanceDir(string $instanceDir)
    {
        return $this->setOption('instanceDir', $instanceDir);
    }

    /**
     * Get the instanceDir.
     *
     * @return string
     */
    public function getInstanceDir(): string
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
    public function setConfig(string $config)
    {
        return $this->setOption('config', $config);
    }

    /**
     * Get the config.
     *
     * @return string
     */
    public function getConfig(): string
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
    public function setSchema(string $schema)
    {
        return $this->setOption('schema', $schema);
    }

    /**
     * Get the schema.
     *
     * @return string
     */
    public function getSchema(): string
    {
        return (string) $this->getOption('schema');
    }

    /**
     * Set the dataDir.
     *
     * @param string $dataDir
     *
     * @return self Provides fluent interface
     */
    public function setDataDir(string $dataDir)
    {
        return $this->setOption('dataDir', $dataDir);
    }

    /**
     * Get the schema.
     *
     * @return string
     */
    public function getDataDir(): string
    {
        return (string) $this->getOption('dataDir');
    }

    /**
     * Set the configSet.
     *
     * @param string $configSet
     *
     * @return self Provides fluent interface
     */
    public function setConfigSet(string $configSet)
    {
        return $this->setOption('configSet', $configSet);
    }

    /**
     * Get the configSet.
     *
     * @return string
     */
    public function getConfigSet(): string
    {
        return (string) $this->getOption('configSet');
    }

    /**
     * Set the collection.
     *
     * @param string $collection
     *
     * @return self Provides fluent interface
     */
    public function setCollection(string $collection)
    {
        return $this->setOption('collection', $collection);
    }

    /**
     * Get the collection.
     *
     * @return string
     */
    public function getCollection(): string
    {
        return (string) $this->getOption('collection');
    }

    /**
     * Set the shard.
     *
     * @param string $shard
     *
     * @return self Provides fluent interface
     */
    public function setShard($shard)
    {
        return $this->setOption('shard', $shard);
    }

    /**
     * Get the collection.
     *
     * @return string
     */
    public function getShard(): string
    {
        return (string) $this->getOption('shard');
    }

    /**
     * Set the a property in the core.properties file.
     *
     * @param string $name
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setCoreProperty(string $name, string $value)
    {
        $option = 'property.'.$name;
        return $this->setOption($option, $value);
    }

    /**
     * Get a previously added core property.
     *
     * @return string
     */
    public function getCoreProperty($name): string
    {
        $option = 'property.'.$name;
        return (string) $this->getOption($option);
    }
}
