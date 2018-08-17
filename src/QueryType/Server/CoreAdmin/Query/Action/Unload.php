<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

/**
 * @see https://lucene.apache.org/solr/guide/6_6/coreadmin-api.html#CoreAdminAPI-UNLOAD
 */
class Unload extends AbstractAsyncAction
{
    /**
     * Returns the action type of the core admin action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CoreAdminQuery::ACTION_UNLOAD;
    }

    /**
     * If set to true the index will be deleted when the core is unloaded.
     *
     * @param bool $deleteIndex
     *
     * @return self Provides fluent interface
     */
    public function setDeleteIndex(bool $deleteIndex)
    {
        return $this->setOption('deleteIndex', $deleteIndex);
    }

    /**
     * Indicates if a deletion was forced.
     *
     * @return bool
     */
    public function getDeleteIndex(): bool
    {
        return (string) $this->getOption('deleteIndex');
    }

    /**
     * If set to true the data dir will be removed when unloading.
     *
     * @param bool $deleteDataDir
     *
     * @return self Provides fluent interface
     */
    public function setDeleteDataDir(bool $deleteDataDir)
    {
        return $this->setOption('deleteDataDir', $deleteDataDir);
    }

    /**
     * Indicates if a deletion of the dataDir was forced.
     *
     * @return bool
     */
    public function getDeleteDataDir(): bool
    {
        return (string) $this->getOption('deleteDataDir');
    }

    /**
     * If set to true the instance dir will be removed when unloading.
     *
     * @param bool $deleteInstanceDir
     *
     * @return self Provides fluent interface
     */
    public function setDeleteInstanceDir(bool $deleteInstanceDir)
    {
        return $this->setOption('deleteInstanceDir', $deleteInstanceDir);
    }

    /**
     * Indicates if a deletion of the instanceDir was forced.
     *
     * @return bool
     */
    public function getDeleteInstanceDir(): bool
    {
        return (string) $this->getOption('deleteInstanceDir');
    }
}
