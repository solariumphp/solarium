<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Tests\Integration\AbstractTechproductsTest;

class TechproductsGuzzleTest extends AbstractTechproductsTest
{

    public function setUp()
    {
        if (!class_exists('\\GuzzleHttp\\Client')) {
            $this->markTestSkipped('Guzzle 6 not installed');
        }
        else {
            parent::setUp();
            $this->client->setAdapter('Solarium\Core\Client\Adapter\Guzzle3');
        }
    }

}
