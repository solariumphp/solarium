<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;
use Solarium\QueryType\Server\Query\Action\AbstractAction;

/**
 * Class Status.
 *
 * @see https://solr.apache.org/guide/coreadmin-api.html#coreadmin-status
 */
class Status extends AbstractAction implements CoreActionInterface
{
    use CoreActionTrait;

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
    public function setIndexInfo(bool $indexInfo): self
    {
        $this->setOption('indexInfo', $indexInfo);

        return $this;
    }

    /**
     * Get if information about the index should be retrieved.
     *
     * @return bool|null
     */
    public function getIndexInfo(): ?bool
    {
        return $this->getOption('indexInfo');
    }
}
