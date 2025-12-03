<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Schema\Similarity;

class SimilarityTest extends TestCase
{
    protected Similarity $similarity;

    public function setUp(): void
    {
        $this->similarity = new Similarity();
    }

    public function testSetAndGetClassName(): void
    {
        $this->assertSame($this->similarity, $this->similarity->setClassName('org.example.SimilarityClass'));
        $this->assertSame('org.example.SimilarityClass', $this->similarity->getClassName());

        $this->assertSame($this->similarity, $this->similarity->setClassName(null));
        $this->assertNull($this->similarity->getClassName());
    }

    public function testSetAndGetDetails(): void
    {
        $this->assertSame($this->similarity, $this->similarity->setDetails('similarity details'));
        $this->assertSame('similarity details', $this->similarity->getDetails());

        $this->assertSame($this->similarity, $this->similarity->setDetails(null));
        $this->assertNull($this->similarity->getDetails());
    }

    public function testToString(): void
    {
        $this->similarity->setClassName('org.example.SimilarityClass');
        $this->assertSame('org.example.SimilarityClass', (string) $this->similarity);

        $this->similarity->setClassName(null);
        $this->assertSame('', (string) $this->similarity);
    }
}
