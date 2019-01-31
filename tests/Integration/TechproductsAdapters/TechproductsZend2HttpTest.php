<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Tests\Integration\AbstractTechproductsTest;

/**
 * @group integration
 */
class TechproductsZend2HttpTest extends AbstractTechproductsTest
{
    public function setUp()
    {
        parent::setUp();
        $this->client->setAdapter('Solarium\Core\Client\Adapter\Zend2Http');
    }
}
