<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use Solarium\QueryType\Luke\Result\Schema\Type\IndexAnalyzer;

class IndexAnalyzerTest extends AbstractAnalyzerTestCase
{
    /**
     * @var IndexAnalyzer
     */
    protected $analyzer;

    public function setUp(): void
    {
        $this->analyzer = new IndexAnalyzer('org.example.IndexAnalyzerClass');
    }

    public function testGetClassName()
    {
        $this->assertSame('org.example.IndexAnalyzerClass', $this->analyzer->getClassName());
    }

    public function testToString()
    {
        $this->assertSame('org.example.IndexAnalyzerClass', (string) $this->analyzer);
    }
}
