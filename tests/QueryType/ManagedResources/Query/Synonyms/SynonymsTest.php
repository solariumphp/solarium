<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Synonyms;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;

class SynonymsTest extends TestCase
{
    protected Synonyms $synonyms;

    public function setUp(): void
    {
        $this->synonyms = new Synonyms();
    }

    public function testSetAndGetTerm(): void
    {
        $this->synonyms->setTerm('mad');
        $this->assertSame('mad', $this->synonyms->getTerm());
    }

    public function testRemoveTerm(): void
    {
        $this->synonyms->setTerm('mad');
        $this->synonyms->removeTerm();
        $this->assertNull($this->synonyms->getTerm());
    }

    public function testSetAndGetSynonyms(): void
    {
        $this->synonyms->setSynonyms(['angry', 'upset']);
        $this->assertSame(['angry', 'upset'], $this->synonyms->getSynonyms());
    }
}
