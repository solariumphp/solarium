<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Core\Client\Adapter\Http;
use Solarium\Tests\Integration\AbstractCoreTest;

/**
 * @group integration
 * @group solr_server
 * @coversNothing
 */
class TechproductsHttpTest extends AbstractCoreTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->client->setAdapter(new Http());
    }
}
