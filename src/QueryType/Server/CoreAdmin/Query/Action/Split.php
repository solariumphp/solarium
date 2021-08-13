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
 * Class Split.
 *
 * @see https://solr.apache.org/guide/coreadmin-api.html#coreadmin-split
 */
class Split extends AbstractAsyncAction implements CoreActionInterface
{
    use CoreActionTrait;

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
    public function setPath(array $path): self
    {
        $this->setOption('path', $path);

        return $this;
    }

    /**
     * Get the pathes that should be used to split into.
     *
     * @return array|null
     */
    public function getPath(): ?array
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
    public function setTargetCore(array $targetCore): self
    {
        $this->setOption('targetCore', $targetCore);

        return $this;
    }

    /**
     * Get the pathes that should be used to split into.
     *
     * @return array|null
     */
    public function getTargetCore(): ?array
    {
        return $this->getOption('targetCore');
    }

    /**
     * Set a comma-separated list of hash ranges in a hexadecimal format.
     *
     * @param string $ranges
     *
     * @return self Provides fluent interface
     */
    public function setRanges(string $ranges): self
    {
        $this->setOption('ranges', $ranges);

        return $this;
    }

    /**
     * Get the pathes that should be used to split into.
     *
     * @return string|null
     */
    public function getRanges(): ?string
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
    public function setSplitKey(string $splitKey): self
    {
        $this->setOption('split.key', $splitKey);

        return $this;
    }

    /**
     * Returns the key that should be used for splitting.
     *
     * @return string|null
     */
    public function getSplitKey(): ?string
    {
        return $this->getOption('split.key');
    }
}
