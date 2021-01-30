<?php

namespace Solarium\Tests\QueryType\ManagedResources\Result\Resources;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\ManagedResources\Result\Resources\Resource as ResourceResultItem;

class ResourceTest extends TestCase
{
    /** @var ResourceResultItem */
    protected $resource;

    public function setUp(): void
    {
        $data = [
            'resourceId' => '/schema/analysis/stopwords/english',
            'numObservers' => 2,
            'class' => 'org.apache.solr.rest.schema.analysis.ManagedWordSetResource',
        ];

        $this->resource = new ResourceResultItem($data);
    }

    public function testConstructor()
    {
        $data = [
            'resourceId' => '/schema/analysis/synonyms/english',
            'numObservers' => 2,
            'class' => 'org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager',
        ];

        $resource = new ResourceResultItem($data);
        $this->assertSame('/schema/analysis/synonyms/english', $resource->getResourceId());
        $this->assertSame(2, $resource->getNumObservers());
        $this->assertSame('org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager', $resource->getClass());
    }

    public function testSetAndGetResourceId()
    {
        $this->resource->setResourceId('/schema/analysis/synonyms/english');
        $this->assertSame('/schema/analysis/synonyms/english', $this->resource->getResourceId());
    }

    public function testSetAndGetNumObservers()
    {
        $this->resource->setNumObservers(2);
        $this->assertSame(2, $this->resource->getNumObservers());
    }

    public function testSetAndGetClass()
    {
        $this->resource->setClass('org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager');
        $this->assertSame('org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager', $this->resource->getClass());
    }

    /**
     * @testWith ["/schema/analysis/stopwords/english", "stopwords"]
     *           ["/schema/analysis/synonyms/english", "synonyms"]
     *           ["/schema/analysis/unknown/english", ""]
     */
    public function testGetType(string $resourceId, string $expectedType)
    {
        $this->resource->setResourceId($resourceId);
        $this->assertSame($expectedType, $this->resource->getType());
    }
}
