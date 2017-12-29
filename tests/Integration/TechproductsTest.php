<?php

namespace Solarium\Tests\Integration;

use Solarium\Core\Client\ClientInterface;

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

    public function testPing()
    {
        $ping = $this->client->createPing();
        $this->client->ping($ping);
    }

    public function testSelect()
    {
        $select = $this->client->createSelect();
        $select->setSorts(['id' => 'asc']);
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

}
