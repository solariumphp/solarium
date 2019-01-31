<?php

namespace Solarium\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\QueryTraits\TermsTrait;
use Solarium\Core\Client\ClientInterface;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

abstract class AbstractSolrCloudTest extends TestCase
{
    /**
     * @var ClientInterface
     */
    protected $client;

    protected $collection = 'techproducts';

    public function setUp()
    {
        $config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/solr/',
                    'collection' => $this->collection,
                ],
            ],
            // Curl is the default adapter.
            //'adapter' => 'Solarium\Core\Client\Adapter\Curl',
        ];

        $this->client = new \Solarium\Client($config);

        try {
            $ping = $this->client->createPing();
            $this->client->ping($ping);
        } catch (\Exception $e) {
            $this->markTestSkipped('SolrCloud techproducts example not reachable.');
        }
    }

    /**
     * The ping test succeeds if no exception is thrown.
     */
    public function testPing()
    {
        $ping = $this->client->createPing();
        $result = $this->client->ping($ping);
        $this->assertSame(0, $result->getStatus());
    }
}

class TestQuery extends SelectQuery
{
    use TermsTrait;

    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->componentTypes[ComponentAwareQueryInterface::COMPONENT_TERMS] = 'Solarium\Component\Terms';
        // Unfortunately the terms request Handler is the only one containing a terms component.
        $this->setHandler('terms');
    }
}
