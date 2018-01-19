<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Tests\Integration\AbstractTechproductsTest;

class TechproductsGuzzle3Test extends AbstractTechproductsTest
{

    public function setUp()
    {
        if (!class_exists('\\Guzzle\\Http\\Client')) {
            $this->markTestSkipped('Guzzle 3 not installed');
        }
        else {
            parent::setUp();
            $this->client->setAdapter('Solarium\Core\Client\Adapter\Guzzle');
        }
    }

}
