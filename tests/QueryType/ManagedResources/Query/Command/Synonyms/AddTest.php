<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Command\Synonyms;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;
use Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Add;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;

class AddTest extends TestCase
{
    /** @var Add */
    protected $add;

    /** @var Synonyms */
    protected $synonyms;

    public function setUp(): void
    {
        $this->add = new Add();
        $this->synonyms = new Synonyms();
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMMAND_ADD, $this->add->getType());
    }

    public function testGetRequestMethod()
    {
        $this->assertSame(Request::METHOD_PUT, $this->add->getRequestMethod());
    }

    public function testSetAndGetSynonyms()
    {
        $this->add->setSynonyms($this->synonyms);
        $this->assertSame($this->synonyms, $this->add->getSynonyms());
    }

    public function testGetRawData()
    {
        $this->synonyms->setTerm('mad');
        $this->synonyms->setSynonyms(['angry', 'upset']);
        $this->add->setSynonyms($this->synonyms);
        $this->assertSame('{"mad":["angry","upset"]}', $this->add->getRawData());
    }

    public function testGetRawDataEmptyTerm()
    {
        $this->synonyms->setTerm('');
        $this->synonyms->setSynonyms(['angry', 'upset']);
        $this->add->setSynonyms($this->synonyms);
        $this->assertSame('["angry","upset"]', $this->add->getRawData());
    }

    public function testGetRawDataNoTerm()
    {
        $this->synonyms->setSynonyms(['funny', 'entertaining', 'whimsical', 'jocular']);
        $this->add->setSynonyms($this->synonyms);
        $this->assertSame('["funny","entertaining","whimsical","jocular"]', $this->add->getRawData());
    }

    public function testGetRawDataEmptySynonyms()
    {
        $this->synonyms->setSynonyms([]);
        $this->add->setSynonyms($this->synonyms);
        $this->assertNull($this->add->getRawData());
    }

    public function testGetRawDataNoSynonyms()
    {
        $this->assertNull($this->add->getRawData());
    }
}
