<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Luke\Result\Index\Index as IndexResult;
use Solarium\QueryType\Luke\Result\Index\UserData;
use Solarium\QueryType\Luke\Result\Result;

/**
 * Parse Luke index response data.
 */
class Index extends AbstractResponseParser implements ResponseParserInterface
{
    /**
     * Get result data for the response.
     *
     * @param Result $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();

        $data['indexResult'] = $this->parseIndex($data['index']);
        $data['schemaResult'] = null;
        $data['docResult'] = null;
        $data['fieldsResult'] = null;
        $data['infoResult'] = null;

        return $data;
    }

    /**
     * @param array $indexData
     *
     * @return IndexResult
     */
    protected function parseIndex(array $indexData): IndexResult
    {
        $index = new IndexResult();

        $index->setNumDocs($indexData['numDocs']);
        $index->setMaxDoc($indexData['maxDoc']);
        $index->setDeletedDocs($indexData['deletedDocs']);
        // indexHeapUsageBytes was removed in SOLR-15341 for Solr 9
        $index->setIndexHeapUsageBytes($indexData['indexHeapUsageBytes'] ?? null);
        $index->setVersion($indexData['version']);
        $index->setSegmentCount($indexData['segmentCount']);
        $index->setCurrent($indexData['current']);
        $index->setHasDeletions($indexData['hasDeletions']);
        $index->setDirectory($indexData['directory']);
        $index->setSegmentsFile($indexData['segmentsFile']);
        $index->setSegmentsFileSizeInBytes($indexData['segmentsFileSizeInBytes']);

        // userData is empty if there haven't been any commits yet
        $userData = new UserData();
        $userData->setCommitCommandVer($indexData['userData']['commitCommandVer'] ?? null);
        $userData->setCommitTimeMSec($indexData['userData']['commitTimeMSec'] ?? null);
        $index->setUserData($userData);

        // lastModified is calculated from commitTimeMSec (in Solr) and will be empty too before the first commit
        $lastModified = isset($indexData['lastModified']) ? new \DateTime($indexData['lastModified']) : null;
        $index->setLastModified($lastModified);

        return $index;
    }
}
