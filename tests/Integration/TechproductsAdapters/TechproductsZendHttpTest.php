<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Tests\Integration\AbstractTechproductsTest;

class TechproductsZendHttpTest extends AbstractTechproductsTest
{
    public function setUp()
    {
        if (!class_exists('Zend_Loader_Autoloader') && !(@include_once 'Zend/Loader/Autoloader.php')) {
            $this->markTestSkipped('ZF not in include_path, skipping ZendHttp adapter tests');
        } else {
            parent::setUp();
            $this->client->setAdapter('Solarium\Core\Client\Adapter\ZendHttp');
        }
    }
}
