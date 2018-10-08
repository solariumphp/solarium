<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

/**
 * @see https://lucene.apache.org/solr/guide/6_6/coreadmin-api.html#CoreAdminAPI-SPLIT
 */
class Split extends AbstractAsyncAction
{
    /**
     * Returns the action type of the core admin action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CoreAdminQuery::ACTION_SPLIT;
    }

    /**
     * The directories that should be used to split into.
     *
     * @param string[] $path
     *
     * @return self Provides fluent interface
     */
    public function setPath(array $path)
    {
        return $this->setOption('path', $path);
    }

    /**
     * Get the pathes that should be used to split into.
     *
     * @return array
     */
    public function getPath(): array
    {
        return (array) $this->getOption('path');
    }

    /**
     * The target core names to split into.
     *
     * @param string[] $targetCore
     *
     * @return self Provides fluent interface
     */
    public function setTargetCore(array $targetCore)
    {
        return $this->setOption('targetCore', $targetCore);
    }

    /**
     * Get the pathes that should be used to split into.
     *
     * @return array
     */
    public function getTargetCore(): array
    {
        return (array) $this->getOption('targetCore');
    }

    /**
     * Set a comma-separated list of hash ranges in a hexadecimal format.
     *
     * @param string $ranges
     *
     * @return self Provides fluent interface
     */
    public function setRanges(string $ranges)
    {
        return $this->setOption('ranges', $ranges);
    }

    /**
     * Get the pathes that should be used to split into.
     *
     * @return string
     */
    public function getRanges(): string
    {
        return (string) $this->getOption('ranges');
    }

    /**
     * Set a key that should be used for splitting.
     *
     * @param string $splitKey
     *
     * @return self Provides fluent interface
     */
    public function setSplitKey(string $splitKey)
    {
        return $this->setOption('split.key', $splitKey);
    }

    /**
     * Returns the key that should be used for splitting.
     *
     * @return string
     */
    public function getSplitKey(): string
    {
        return (string) $this->getOption('split.key');
    }
}
