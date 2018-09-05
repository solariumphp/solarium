<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\ManagedResources\ResponseParser\Resources as ResourcesResponseParser;

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

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $parser = new ResourcesResponseParser();

        $result = $parser->parse($resultStub);

        $this->assertSame(count($data['managedResources']), count($result['items']));
        $this->assertSame('managedResources', $result['items']->getName());
        $this->assertSame('/schema/analysis/stopwords/dutch', $result['items']->getItems()[0]['resourceId']);
        $this->assertSame('/schema/analysis/synonyms/dutch', $result['items']->getItems()[1]['resourceId']);
    }

}
