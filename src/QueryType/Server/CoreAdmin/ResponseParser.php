<?php

namespace Solarium\QueryType\Server\CoreAdmin;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Server\CoreAdmin\Query\Query;
use Solarium\QueryType\Server\CoreAdmin\Result\Result;
use Solarium\QueryType\Server\CoreAdmin\Result\StatusResult;

/**
 * Parse update response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Parse response data.
     *
     * @param Result $result
     *
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();
        $data = $this->parseStatus($data, $result);
        $data = $this->addHeaderInfo($data, $data);
        return $data;
    }

    /**
     * @param array  $data
     * @param Result $result
     *
     * @return array
     */
    protected function parseStatus(array $data, Result $result)
    {
        $action = $result->getQuery()->getAction();
        $type = $action->getType();

        $data['wasSuccessful'] = 200 === $result->getResponse()->getStatusCode();
        $data['statusMessage'] = $result->getResponse()->getStatusMessage();

        if (Query::ACTION_STATUS !== $type) {
            return $data;
        }

        if (!is_array($data['status'])) {
            return $data;
        }

        $coreStatusResults = [];
        $coreStatusResult = null;

        foreach ($data['status'] as $coreName => $coreStatusData) {
            $status = new StatusResult();
            $status->setCoreName($coreName);
            $status->setNumberOfDocuments($coreStatusData['index']['numDocs'] ?? 0);
            $status->setVersion($coreStatusData['index']['version'] ?? 0);
            $status->setUptime($coreStatusData['uptime'] ?? 0);

            $startTimeDate = isset($coreStatusData['startTime']) ? new \DateTime($coreStatusData['startTime']) : null;
            $status->setStartTime($startTimeDate);

            $lastModifiedDate = isset($coreStatusData['index']['lastModified']) ? new \DateTime($coreStatusData['index']['lastModified']) : null;
            $status->setLastModified($lastModifiedDate);
            $coreStatusResults[] = $status;

            // when a core name was set in the action and we have a response we remember it to set a single statusResult
            if ($coreName === $action->getCore()) {
                $coreStatusResult = $status;
            }
        }

        $data['statusResults'] = $coreStatusResults;
        $data['statusResult'] = $coreStatusResult;

        return $data;
    }
}
