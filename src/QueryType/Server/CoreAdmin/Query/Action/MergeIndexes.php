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
 * Class MergeIndexes.
 *
 * @see https://solr.apache.org/guide/coreadmin-api.html#coreadmin-mergeindexes
 */
class MergeIndexes extends AbstractAsyncAction implements CoreActionInterface
{
    use CoreActionTrait;

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
    public function setIndexDir(array $indexDir): self
    {
        $this->setOption('indexDir', $indexDir);

        return $this;
    }

    /**
     * Get the other core that should be the new name.
     *
     * @return string[]|null
     */
    public function getIndexDir(): ?array
    {
        return $this->getOption('indexDir');
    }

    /**
     * Directories that should be merged.
     *
     * @param string[] $srcCore
     *
     * @return self Provides fluent interface
     */
    public function setSrcCore(array $srcCore): self
    {
        $this->setOption('srcCore', $srcCore);

        return $this;
    }

    /**
     * Get the other core that should be the new name.
     *
     * @return string[]|null
     */
    public function getSrcCore(): ?array
    {
        return $this->getOption('srcCore');
    }
}
