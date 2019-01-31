<?php

namespace Solarium\Tests\Integration\SolrCloud;

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
            $this->client->setAdapter('Solarium\Core\Client\Adapter\Guzzle');
        }
    }
}
