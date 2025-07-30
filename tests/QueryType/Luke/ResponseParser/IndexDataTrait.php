<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

/**
 * Default index data used in all ResponseParser tests.
 */
trait IndexDataTrait
{
    public function getIndexData(): array
    {
        return [
            'numDocs' => 15,
            'maxDoc' => 20,
            'deletedDocs' => 5,
            'indexHeapUsageBytes' => 2000,
            'version' => 6,
            'segmentCount' => 1,
            'current' => false,
            'hasDeletions' => true,
            'directory' => 'directory info',
            'segmentsFile' => 'segments_3',
            'segmentsFileSizeInBytes' => 200,
            'userData' => [
                'commitCommandVer' => '123456789123456789',
                'commitTimeMSec' => '123456789',
            ],
            'lastModified' => '2022-01-01T20:00:15.789Z',
        ];
    }
}
