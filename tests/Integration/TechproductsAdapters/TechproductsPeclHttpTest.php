<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Core\Client\Adapter\PeclHttp;
use Solarium\Tests\Integration\AbstractTechproductsTest;

class TechproductsPeclHttpTestTest extends AbstractTechproductsTest
{
    public function setUp()
    {
        if (!function_exists('http_get')) {
            $this->markTestSkipped('Pecl_http not available, skipping PeclHttp adapter tests');
        }
        else {
            parent::setUp();
            $this->client->setAdapter(new PeclHttp(array('timeout' => 10)));
        }
    }
}
