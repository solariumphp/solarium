<?php

namespace Solarium\Tests\QueryType\ManagedResources\Result\Synonyms;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\ManagedResources\Result\Synonyms\Synonyms;

class SynonymsTest extends TestCase
{
    protected Synonyms $synonyms;

    public function setUp(): void
    {
        $this->synonyms = new Synonyms('happy', ['glad', 'joyful']);
    }

    public function testConstructor(): void
    {
        $synonyms = new Synonyms('mad', ['angry', 'upset']);
        $this->assertSame('mad', $synonyms->getTerm());
        $this->assertSame(['angry', 'upset'], $synonyms->getSynonyms());
    }

    public function testSetAndGetTerm(): void
    {
        $this->synonyms->setTerm('mad');
        $this->assertSame('mad', $this->synonyms->getTerm());
    }

    public function testSetAndGetSynonyms(): void
    {
        $this->synonyms->setSynonyms(['angry', 'upset']);
        $this->assertSame(['angry', 'upset'], $this->synonyms->getSynonyms());
    }
}
