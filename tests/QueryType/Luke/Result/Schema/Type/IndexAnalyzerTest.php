<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use Solarium\QueryType\Luke\Result\Schema\Type\AbstractAnalyzer;
use Solarium\QueryType\Luke\Result\Schema\Type\IndexAnalyzer;

class IndexAnalyzerTest extends AbstractAnalyzerTestCase
{
    protected AbstractAnalyzer|IndexAnalyzer $analyzer;

    public function setUp(): void
    {
        $this->analyzer = new IndexAnalyzer('org.example.IndexAnalyzerClass');
    }

    public function testGetClassName(): void
    {
        $this->assertSame('org.example.IndexAnalyzerClass', $this->analyzer->getClassName());
    }

    public function testToString(): void
    {
        $this->assertSame('org.example.IndexAnalyzerClass', (string) $this->analyzer);
    }
}
