<?php

namespace Solarium\Tests\Plugin\BufferedAdd\Event;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedAdd\Event\AddDocument;
use Solarium\QueryType\Update\Query\Document;

class AddDocumentTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $document = new Document();

        $event = new AddDocument($document);

        $this->assertSame($document, $event->getDocument());

        return $event;
    }
}
