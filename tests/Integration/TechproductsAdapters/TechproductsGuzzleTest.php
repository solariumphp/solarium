<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Tests\Integration\AbstractCoreTest;

/**
 * @group integration
 * @group solr_no_cloud
 */
class TechproductsGuzzleTest extends AbstractCoreTest
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
