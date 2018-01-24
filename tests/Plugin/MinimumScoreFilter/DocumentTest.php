<?php

namespace Solarium\Tests\Plugin\MinimumScoreFilter;

use Solarium\Plugin\MinimumScoreFilter\Document as FilterDocument;
use Solarium\QueryType\Select\Result\Document;
use Solarium\Tests\QueryType\Select\Result\AbstractDocumentTest;

class DocumentTest extends AbstractDocumentTest
{
    protected function setUp()
    {
        $doc = new Document($this->fields);
        $this->doc = new FilterDocument($doc, true);
    }

    public function testMarkedAsLowScore()
    {
        $this->assertTrue($this->doc->markedAsLowScore());

        $doc2 = new Document($this->fields);
        $filterDoc2 = new FilterDocument($doc2, false);
        $this->assertFalse($filterDoc2->markedAsLowScore());
    }
}
