<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

/**
 * Class RequestStatus.
 *
 * @see https://lucene.apache.org/solr/guide/6_6/coreadmin-api.html#CoreAdminAPI-REQUESTSTATUS
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
    public function setRequestId($requestId)
    {
        return $this->setOption('requestid', $requestId);
    }

    /**
     * Get the requestId where that status should be retrieved for.
     *
     * @return string
     */
    public function getRequestId(): string
    {
        return (string) $this->getOption('requestid');
    }
}
