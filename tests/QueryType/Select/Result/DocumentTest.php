<?php

namespace Solarium\Tests\QueryType\Select\Result;

use Solarium\QueryType\Select\Result\Document;

class DocumentTest extends AbstractDocumentTest
{
    public function setUp(): void
    {
        $this->doc = new Document($this->fields);
    }
}
