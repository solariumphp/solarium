<?php

namespace Solarium\Tests\Integration\SolrCloud;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\QueryTraits\TermsTrait;
use Solarium\Component\Result\Terms\Result;
use Solarium\Core\Client\ClientInterface;
use Solarium\Exception\HttpException;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add as AddStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Delete as DeleteStopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Exists as ExistsStopwords;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Add as AddSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Delete as DeleteSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Exists as ExistsSynonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Document;

abstract class AbstractSolrCloudTest extends TestCase
{
    /**
     * @var ClientInterface
     */
    protected $client;

    protected $collection = 'gettingstarted';

    public function setUp()
    {
        $config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/solr/',
                    'core' => $this->collection,
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
            $this->markTestSkipped('SolrCloud gettingstarted example not reachable.');
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
