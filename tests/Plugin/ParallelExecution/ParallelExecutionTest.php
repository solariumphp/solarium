<?php

namespace Solarium\Tests\Plugin\ParallelExecution;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Plugin\ParallelExecution\ParallelExecution;

class ParallelExecutionTest extends TestCase
{
    /**
     * @var ParallelExecution
     */
    protected $plugin;

    public function setUp()
    {
        $this->plugin = new ParallelExecution();
    }

    public function testAddAndGetQueries()
    {
        $client = new Client();
        $client->clearEndpoints();
        $client->createEndpoint('local1');
        $endpoint2 = $client->createEndpoint('local2');

        $this->plugin->initPlugin($client, []);

        $query1 = $client->createSelect()->setQuery('test1');
        $query2 = $client->createSelect()->setQuery('test2');

        $this->plugin->addQuery(1, $query1);
        $this->plugin->addQuery(2, $query2, $endpoint2);

        $this->assertSame(
            [
                1 => ['query' => $query1, 'endpoint' => 'local1'],
                2 => ['query' => $query2, 'endpoint' => 'local2'],
            ],
            $this->plugin->getQueries()
        );
    }

    public function testClearQueries()
    {
        $client = new Client();
        $this->plugin->initPlugin($client, []);

        $query1 = $client->createSelect()->setQuery('test1');
        $query2 = $client->createSelect()->setQuery('test2');

        $this->plugin->addQuery(1, $query1);
        $this->plugin->addQuery(2, $query2);
        $this->plugin->clearQueries();

        $this->assertSame(
            [],
            $this->plugin->getQueries()
        );
    }
}
