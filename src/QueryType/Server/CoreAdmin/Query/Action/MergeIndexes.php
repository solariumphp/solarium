<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class MergeIndexes extends AbstractAsyncAction
{
    /**
     * Returns the action type of the core admin action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CoreAdminQuery::ACTION_MERGE_INDEXES;
    }

    /**
     * Directories that should be merged.
     *
     * @param string[] $indexDir
     *
     * @return self Provides fluent interface
     */
    public function setIndexDir(array $indexDir)
    {
        return $this->setOption('indexDir', $indexDir);
    }

    /**
     * Get the other core that should be the new name.
     *
     * @return string[]
     */
    public function getIndexDir(): array
    {
        return (array) $this->getOption('indexDir');
    }

    /**
     * Directories that should be merged.
     *
     * @param string[] $srcCore
     *
     * @return self Provides fluent interface
     */
    public function setSrcCore(array $srcCore)
    {
        return $this->setOption('srcCore', $srcCore);
    }

    /**
     * Get the other core that should be the new name.
     *
     * @return string[]
     */
    public function getSrcCore(): array
    {
        return (array) $this->getOption('srcCore');
    }
}
