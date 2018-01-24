<?php

namespace Solarium\Tests\QueryType\Select\Result;

use Solarium\QueryType\Select\Result\Document;

class DocumentTest extends AbstractDocumentTest
{
    protected function setUp()
    {
        $this->doc = new Document($this->fields);
    }
}
