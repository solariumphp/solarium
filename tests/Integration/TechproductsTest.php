<?php

namespace Solarium\Tests\Integration;

use Solarium\Core\Client\ClientInterface;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

class TechproductsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ClientInterface
     */
    protected $client;

    public function setUp()
    {
        $config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/solr/',
                    'core' => 'techproducts',
                ]
            ]
        ];

        $this->client = new \Solarium\Client($config);
    }

    /**
     * The ping test succeeds if no exception is thrown.
     */
    public function testPing()
    {
        $ping = $this->client->createPing();
        $result = $this->client->ping($ping);
        $this->assertEquals(0, $result->getStatus());
    }

    public function testSelect()
    {
        $select = $this->client->createSelect();
        $select->setSorts(['id' => SelectQuery::SORT_ASC]);
        $result = $this->client->select($select);
        $this->assertEquals(32, $result->getNumFound());
        $this->assertEquals(10, $result->count());

        $ids = [];
        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($result as $document) {
            $ids[] = $document->id;
        }
        $this->assertEquals([
            "0579B002",
            "100-435805",
            "3007WFP",
            "6H500F0",
            "9885A004",
            "EN7800GTX/2DHTV/256M",
            "EUR",
            "F8V7067-APL-KIT",
            "GB18030TEST",
            "GBP",
            ], $ids);
    }

    public function testSpellcheck()
    {
        $spellcheck = $this->client->createSpellcheck();
        $spellcheck->setQuery('power cort');
        // Some spellcheck dictionaries needs to build first, but not on every request!
        $spellcheck->setBuild(true);
        $result = $this->client->spellcheck($spellcheck);
        $words = [];
        foreach ($result as $term => $suggestions) {
            $this->assertEquals('cort', $term);
            foreach ($suggestions as $suggestion) {
                $words[] = $suggestion['word'];
            }
        }
        $this->assertEquals([
            'corp',
            'cord',
            'card',
            ], $words);
    }

    public function testSuggester()
    {
        $suggester = $this->client->createSuggester();
        // The techproducts example doesn't provide a default suggester, but 'mySuggester'.
        $suggester->setDictionary('mySuggester');
        $suggester->setQuery('electronics');
        // A suggester dictionary needs to build first, but not on every request!
        $suggester->setBuild(true);
        $result = $this->client->suggester($suggester);
        $phrases = [];
        foreach ($result as $dictionary => $terms) {
            $this->assertEquals('mySuggester', $dictionary);
            foreach ($terms as $term => $suggestions) {
                $this->assertEquals('electronics', $term);
                foreach ($suggestions as $suggestion) {
                    $phrases[] = $suggestion['term'];
                }
            }
        }
        $this->assertEquals([
            'electronics',
            'electronics and computer1',
            'electronics and stuff2'
            ], $phrases);
    }
}
