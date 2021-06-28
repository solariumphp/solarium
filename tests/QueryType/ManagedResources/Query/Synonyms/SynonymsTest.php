<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Synonyms;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;

class SynonymsTest extends TestCase
{
    /** @var Synonyms */
    protected $synonyms;

    public function setUp(): void
    {
        $this->synonyms = new Synonyms();
    }

    public function testSetAndGetTerm()
    {
        $this->synonyms->setTerm('mad');
        $this->assertSame('mad', $this->synonyms->getTerm());
    }

    public function testRemoveTerm()
    {
        $this->synonyms->setTerm('mad');
        $this->synonyms->removeTerm();
        $this->assertNull($this->synonyms->getTerm());
    }

    public function testSetAndGetSynonyms()
    {
        $this->synonyms->setSynonyms(['angry', 'upset']);
        $this->assertSame(['angry', 'upset'], $this->synonyms->getSynonyms());
    }
}
