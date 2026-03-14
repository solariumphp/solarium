<?php

namespace Solarium\Tests\Plugin\MinimumScoreFilter;

use Solarium\Core\Query\DocumentInterface;
use Solarium\Plugin\MinimumScoreFilter\Document as FilterDocument;
use Solarium\QueryType\Select\Result\Document;
use Solarium\Tests\QueryType\Select\Result\AbstractDocumentTestCase;

class DocumentTest extends AbstractDocumentTestCase
{
    /**
     * @var FilterDocument
     */
    protected DocumentInterface $doc;

    public function setUp(): void
    {
        $doc = new Document($this->fields);
        $this->doc = new FilterDocument($doc, true);
    }

    public function testMarkedAsLowScore(): void
    {
        $this->assertTrue($this->doc->markedAsLowScore());

        $doc2 = new Document($this->fields);
        $filterDoc2 = new FilterDocument($doc2, false);
        $this->assertFalse($filterDoc2->markedAsLowScore());
    }

    public function testMethodCallForwarding(): void
    {
        $doc = new TestDocument($this->fields);
        $filterDoc = new TestFilterDocument($doc, true);

        $filterDoc->setTestValue(42);
        $this->assertSame(42, $filterDoc->getTestValue());

        $filterDoc->addTestValues(3, 4);
        $this->assertSame(49, $filterDoc->getTestValue());
    }
}

/**
 * @internal extends FilterDocument to mixin TestDocument for PHPStan
 *
 * @mixin TestDocument
 */
class TestFilterDocument extends FilterDocument
{
}

class TestDocument extends Document
{
    protected int $value = 0;

    public function setTestValue(int $value): void
    {
        $this->value = $value;
    }

    public function addTestValues(int $value1, int $value2): void
    {
        $this->value += $value1;
        $this->value += $value2;
    }

    public function getTestValue(): int
    {
        return $this->value;
    }
}
