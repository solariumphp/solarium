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
 * Class Unload.
 *
 * @see https://solr.apache.org/guide/coreadmin-api.html#coreadmin-unload
 */
class Unload extends AbstractAsyncAction implements CoreActionInterface
{
    use CoreActionTrait;

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
    public function setDeleteIndex(bool $deleteIndex): self
    {
        $this->setOption('deleteIndex', $deleteIndex);

        return $this;
    }

    /**
     * Indicates if a deletion was forced.
     *
     * @return bool|null
     */
    public function getDeleteIndex(): ?bool
    {
        return $this->getOption('deleteIndex');
    }

    /**
     * If set to true the data dir will be removed when unloading.
     *
     * @param bool $deleteDataDir
     *
     * @return self Provides fluent interface
     */
    public function setDeleteDataDir(bool $deleteDataDir): self
    {
        $this->setOption('deleteDataDir', $deleteDataDir);

        return $this;
    }

    /**
     * Indicates if a deletion of the dataDir was forced.
     *
     * @return bool|null
     */
    public function getDeleteDataDir(): ?bool
    {
        return $this->getOption('deleteDataDir');
    }

    /**
     * If set to true the instance dir will be removed when unloading.
     *
     * @param bool $deleteInstanceDir
     *
     * @return self Provides fluent interface
     */
    public function setDeleteInstanceDir(bool $deleteInstanceDir): self
    {
        $this->setOption('deleteInstanceDir', $deleteInstanceDir);

        return $this;
    }

    /**
     * Indicates if a deletion of the instanceDir was forced.
     *
     * @return bool|null
     */
    public function getDeleteInstanceDir(): ?bool
    {
        return $this->getOption('deleteInstanceDir');
    }
}
