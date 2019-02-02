<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Tests\Integration\AbstractCoreTest;

/**
 * @group integration
 * @group solr_no_cloud
 */
class TechproductsGuzzle3Test extends AbstractCoreTest
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
