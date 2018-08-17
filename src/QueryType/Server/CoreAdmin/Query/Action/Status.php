<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class Status extends AbstractCoreAction
{
    /**
     * Returns the action type of the core admin action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CoreAdminQuery::ACTION_STATUS;
    }

    /**
     * Indicates if the indexInfo should be returned. By default this is the case. Can be set to false
     * to improve the request performance if the indexInfo is not required.
     *
     * @param bool $indexInfo
     *
     * @return self Provides fluent interface
     */
    public function setIndexInfo(bool $indexInfo)
    {
        return $this->setOption('indexInfo', $indexInfo);
    }

    /**
     * Get if information about the index should be retrieved.
     *
     * @return bool
     */
    public function getIndexInfo(): bool
    {
        return (string) $this->getOption('indexInfo');
    }
}
