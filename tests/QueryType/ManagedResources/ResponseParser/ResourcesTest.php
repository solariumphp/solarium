<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\ManagedResources\Query\Resources;
use Solarium\QueryType\ManagedResources\ResponseParser\Resources as ResourcesResponseParser;
use Solarium\QueryType\ManagedResources\Result\Resources\ResourceList;

class ResourcesTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'managedResources' => [
                [
                    'resourceId' => '/schema/analysis/stopwords/dutch',
                    'numObservers' => '1',
                    'class' => 'org.apache.solr.rest.schema.analysis.ManagedWordSetResource',
                ],
                [
                    'resourceId' => '/schema/analysis/synonyms/dutch',
                    'numObservers' => '1',
                    'class' => 'org.apache.solr.rest.schema.analysis.ManagedSynonymFilterFactory$SynonymManager',
                ],
            ],
            'responseHeader' => [
                'status' => 1,
                'QTime' => 5,
            ],
        ];

        $query = new Resources();
        $resultStub = $this->createMock(ResourceList::class);
        $resultStub->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $parser = new ResourcesResponseParser();

        $result = $parser->parse($resultStub);

        $this->assertCount(count($data['managedResources']), $result['items']);
        $this->assertSame('/schema/analysis/stopwords/dutch', $result['items'][0]->getResourceId());
        $this->assertSame(1, $result['items'][0]->getNumObservers());
        $this->assertSame('org.apache.solr.rest.schema.analysis.ManagedWordSetResource', $result['items'][0]->getClass());
        $this->assertSame('/schema/analysis/synonyms/dutch', $result['items'][1]->getResourceId());
        $this->assertSame(1, $result['items'][1]->getNumObservers());
        $this->assertSame('org.apache.solr.rest.schema.analysis.ManagedSynonymFilterFactory$SynonymManager', $result['items'][1]->getClass());
    }
}
