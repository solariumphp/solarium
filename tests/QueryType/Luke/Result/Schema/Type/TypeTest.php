<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Schema\Field\DynamicField;
use Solarium\QueryType\Luke\Result\Schema\Field\Field;
use Solarium\QueryType\Luke\Result\Schema\Similarity;
use Solarium\QueryType\Luke\Result\Schema\Type\IndexAnalyzer;
use Solarium\QueryType\Luke\Result\Schema\Type\QueryAnalyzer;
use Solarium\QueryType\Luke\Result\Schema\Type\Type;

class TypeTest extends TestCase
{
    protected Type $type;

    public function setUp(): void
    {
        $this->type = new Type('my_type');
    }

    public function testGetName(): void
    {
        $this->assertSame('my_type', $this->type->getName());
    }

    public function testAddAndGetFields(): void
    {
        $copyDests = [
            $field = new Field('field_a'),
            $dynamicField = new DynamicField('*_b'),
        ];
        $this->assertSame($this->type, $this->type->addField($field));
        $this->assertSame($this->type, $this->type->addField($dynamicField));
        $this->assertSame($copyDests, $this->type->getFields());
    }

    /**
     * If a type has no associated fields, Solr returns null rather than an empty array.
     * We normalise this to an empty array to avoid TypeErrors with array functions.
     */
    public function testGetNoFields(): void
    {
        $this->assertSame([], $this->type->getFields());
    }

    public function testSetAndGetAndIsTokenized(): void
    {
        $this->assertSame($this->type, $this->type->setTokenized(true));
        $this->assertTrue($this->type->getTokenized());
        $this->assertTrue($this->type->isTokenized());
    }

    public function testSetAndGetClassName(): void
    {
        $this->assertSame($this->type, $this->type->setClassName('org.example.MyClass'));
        $this->assertSame('org.example.MyClass', $this->type->getClassName());
    }

    public function testSetAndGetIndexAnalyzer(): void
    {
        $indexAnalyzer = new IndexAnalyzer('org.example.IndexAnalyzerClass');
        $this->assertSame($this->type, $this->type->setIndexAnalyzer($indexAnalyzer));
        $this->assertSame($indexAnalyzer, $this->type->getIndexAnalyzer());
    }

    public function testSetAndGetQueryAnalyzer(): void
    {
        $queryAnalyzer = new QueryAnalyzer('org.example.QueryAnalyzerClass');
        $this->assertSame($this->type, $this->type->setQueryAnalyzer($queryAnalyzer));
        $this->assertSame($queryAnalyzer, $this->type->getQueryAnalyzer());
    }

    public function testSetAndGetSimilarity(): void
    {
        $similarity = new Similarity();
        $this->assertSame($this->type, $this->type->setSimilarity($similarity));
        $this->assertSame($similarity, $this->type->getSimilarity());
    }

    public function testToString(): void
    {
        $this->assertSame('my_type', (string) $this->type);
    }
}
