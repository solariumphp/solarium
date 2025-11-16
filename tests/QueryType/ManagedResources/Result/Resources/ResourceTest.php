<?php

namespace Solarium\Tests\QueryType\ManagedResources\Result\Resources;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\ManagedResources\Result\Resources\Resource as ResourceResultItem;

class ResourceTest extends TestCase
{
    protected ResourceResultItem $resource;

    public function setUp(): void
    {
        $data = [
            'resourceId' => '/schema/analysis/stopwords/english',
            'numObservers' => 2,
            'class' => 'org.apache.solr.rest.schema.analysis.ManagedWordSetResource',
        ];

        $this->resource = new ResourceResultItem($data);
    }

    public function testConstructor(): void
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

    public function testSetAndGetResourceId(): void
    {
        $this->resource->setResourceId('/schema/analysis/synonyms/english');
        $this->assertSame('/schema/analysis/synonyms/english', $this->resource->getResourceId());
    }

    public function testSetAndGetNumObservers(): void
    {
        $this->resource->setNumObservers(2);
        $this->assertSame(2, $this->resource->getNumObservers());
    }

    public function testSetAndGetClass(): void
    {
        $this->resource->setClass('org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager');
        $this->assertSame('org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager', $this->resource->getClass());
    }

    /**
     * @testWith ["/schema/analysis/stopwords/english", "stopwords"]
     *           ["/schema/analysis/synonyms/english", "synonyms"]
     *           ["/schema/analysis/unknown/english", ""]
     */
    public function testGetType(string $resourceId, string $expectedType): void
    {
        $this->resource->setResourceId($resourceId);
        $this->assertSame($expectedType, $this->resource->getType());
    }
}
