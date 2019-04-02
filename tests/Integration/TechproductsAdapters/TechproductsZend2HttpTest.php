<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Tests\Integration\AbstractCoreTest;

/**
 * @group integration
 * @group solr_no_cloud
 */
class TechproductsZend2HttpTest extends AbstractCoreTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->client->setAdapter('Solarium\Core\Client\Adapter\Zend2Http');
    }
}
