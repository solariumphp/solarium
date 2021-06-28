<?php

namespace Solarium\Tests\QueryType\ManagedResources\Result\Resources;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Resources as ResourcesQuery;
use Solarium\QueryType\ManagedResources\Result\Resources\Resource;
use Solarium\QueryType\ManagedResources\Result\Resources\ResourceList;

class ResourceListTest extends TestCase
{
    /** @var ResourceList */
    protected $resourceList;

    public function setUp(): void
    {
        $data = <<<'JSON'
{
  "responseHeader":{
    "status":0,
    "QTime":3},
  "managedResources":[{
      "resourceId":"/schema/analysis/stopwords/english",
      "numObservers":"2",
      "class":"org.apache.solr.rest.schema.analysis.ManagedWordSetResource"},
    {
      "resourceId":"/schema/analysis/synonyms/english",
      "numObservers":"2",
      "class":"org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager"}]}
JSON;

        $query = new ResourcesQuery();
        $response = new Response($data, ['HTTP/1.1 200 OK']);
        $this->resourceList = new ResourceList($query, $response);
    }

    public function testGetName()
    {
        $this->assertSame('managedResources', $this->resourceList->getName());
    }

    public function testGetItems()
    {
        $items = [
            0 => new Resource([
                'resourceId' => '/schema/analysis/stopwords/english',
                'numObservers' => 2,
                'class' => 'org.apache.solr.rest.schema.analysis.ManagedWordSetResource',
            ]),
            1 => new Resource([
                'resourceId' => '/schema/analysis/synonyms/english',
                'numObservers' => 2,
                'class' => 'org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager',
            ]),
        ];

        $this->assertEquals($items, $this->resourceList->getItems());
    }

    public function testGetIterator()
    {
        $items = [
            0 => new Resource([
                'resourceId' => '/schema/analysis/stopwords/english',
                'numObservers' => 2,
                'class' => 'org.apache.solr.rest.schema.analysis.ManagedWordSetResource',
            ]),
            1 => new Resource([
                'resourceId' => '/schema/analysis/synonyms/english',
                'numObservers' => 2,
                'class' => 'org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager',
            ]),
        ];

        foreach ($this->resourceList as $key => $value) {
            $this->assertEquals($items[$key], $value);
        }
    }

    public function testCount()
    {
        $this->assertCount(2, $this->resourceList);
    }
}
