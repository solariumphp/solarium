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
 * Class RequestStatus.
 *
 * @see https://solr.apache.org/guide/coreadmin-api.html#coreadmin-requeststatus
 */
class RequestStatus extends AbstractAction
{
    /**
     * Returns the action type of the core admin action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CoreAdminQuery::ACTION_REQUEST_STATUS;
    }

    /**
     * Set the requestId to get the status from.
     *
     * @param string $requestId
     *
     * @return self Provides fluent interface
     */
    public function setRequestId($requestId): self
    {
        $this->setOption('requestid', $requestId);

        return $this;
    }

    /**
     * Get the requestId where that status should be retrieved for.
     *
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        return $this->getOption('requestid');
    }
}
