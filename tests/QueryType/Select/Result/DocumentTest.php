<?php

namespace Solarium\Tests\QueryType\Select\Result;

use Solarium\QueryType\Select\Result\Document;

class DocumentTest extends AbstractDocumentTestCase
{
    public function setUp(): void
    {
        $this->doc = new Document($this->fields);
    }
}
