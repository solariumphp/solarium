<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\Core\Client\Adapter\Guzzle;
use Solarium\Tests\Integration\AbstractCollectionsTest;

/**
 * @group integration
 * @group solr_cloud
 */
class CollectionsGuzzleTest extends AbstractCollectionsTest
{
    public function setUp()
    {
        if (!class_exists('\\GuzzleHttp\\Client')) {
            $this->markTestSkipped('Guzzle 6 not installed');
        } else {
            parent::setUp();
            $this->client->setAdapter(Guzzle::class);
        }
    }
}
