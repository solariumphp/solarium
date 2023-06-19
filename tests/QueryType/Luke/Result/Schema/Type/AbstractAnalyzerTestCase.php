<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Schema\Type\AbstractAnalyzer;
use Solarium\QueryType\Luke\Result\Schema\Type\CharFilter;
use Solarium\QueryType\Luke\Result\Schema\Type\Filter;
use Solarium\QueryType\Luke\Result\Schema\Type\Tokenizer;

abstract class AbstractAnalyzerTestCase extends TestCase
{
    /**
     * @var AbstractAnalyzer
     */
    protected $analyzer;

    abstract public function testGetClassName();

    public function testSetAndGetCharFilters()
    {
        $charFilters = [
            'FirstCharFilterFactory' => new CharFilter('FirstCharFilterFactory'),
            'NextCharFilterFactory' => new CharFilter('NextCharFilterFactory'),
        ];
        $this->assertSame($this->analyzer, $this->analyzer->setCharFilters($charFilters));
        $this->assertSame($charFilters, $this->analyzer->getCharFilters());
    }

    public function testSetAndGetTokenizer()
    {
        $tokenizer = new Tokenizer('org.example.tokenizerFactory');
        $this->assertSame($this->analyzer, $this->analyzer->setTokenizer($tokenizer));
        $this->assertSame($tokenizer, $this->analyzer->getTokenizer());
    }

    public function testSetAndGetFilters()
    {
        $filters = [
            'FirstFilterFactory' => new Filter('FirstFilterFactory'),
            'NextFilterFactory' => new Filter('NextFilterFactory'),
        ];
        $this->assertSame($this->analyzer, $this->analyzer->setFilters($filters));
        $this->assertSame($filters, $this->analyzer->getFilters());
    }

    abstract public function testToString();
}
