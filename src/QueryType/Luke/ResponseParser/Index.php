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
use Solarium\QueryType\Luke\Result\Shards\Shard as ShardResult;

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
        $data['shardsResult'] = null;

        // available when shards.info=true in distributed mode since Solr 9.11 / 10.1 (SOLR-8127)
        if (isset($data['shards'])) {
            $data['shardsResult'] = $this->parseShards($data['shards']);
        }

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
        $index->setSegmentCount($indexData['segmentCount']);

        // indexHeapUsageBytes was removed in SOLR-15341 for Solr 9
        $index->setIndexHeapUsageBytes($indexData['indexHeapUsageBytes'] ?? null);

        // all other fields aren't present in the aggregated metrics in distributed mode since Solr 9.11 / 10.1 (SOLR-8127)
        $index->setVersion($indexData['version'] ?? null);
        $index->setCurrent($indexData['current'] ?? null);
        $index->setHasDeletions($indexData['hasDeletions'] ?? null);
        $index->setDirectory($indexData['directory'] ?? null);
        $index->setSegmentsFile($indexData['segmentsFile'] ?? null);
        $index->setSegmentsFileSizeInBytes($indexData['segmentsFileSizeInBytes'] ?? null);

        if (isset($indexData['userData'])) {
            // userData is empty if there haven't been any commits yet
            $userData = new UserData();
            $userData->setCommitCommandVer($indexData['userData']['commitCommandVer'] ?? null);
            $userData->setCommitTimeMSec($indexData['userData']['commitTimeMSec'] ?? null);
            $index->setUserData($userData);
        } else {
            $index->setUserData(null);
        }

        // lastModified is calculated from commitTimeMSec (in Solr) and will be empty too before the first commit
        $lastModified = isset($indexData['lastModified']) ? new \DateTime($indexData['lastModified']) : null;
        $index->setLastModified($lastModified);

        return $index;
    }

    /**
     * @param array $shardsData
     *
     * @return array<string, ShardResult>
     */
    protected function parseShards(array $shardsData): array
    {
        $shards = [];

        foreach ($shardsData as $address => $details) {
            $shard = new ShardResult($address);

            $shard->setIndex($this->parseIndex($details['index']));

            $shards[$address] = $shard;
        }

        return $shards;
    }
}
