<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\Tests\Integration\AbstractCollectionsTest;

/**
 * @group integration
 * @group solr_cloud
 */
class CollectionsGuzzle3Test extends AbstractCollectionsTest
{
    public function setUp()
    {
        if (!class_exists('\\Guzzle\\Http\\Client')) {
            $this->markTestSkipped('Guzzle 3 not installed');
        } else {
            parent::setUp();
            $this->client->setAdapter('Solarium\Core\Client\Adapter\Guzzle3');
        }
    }
}
