<?php

namespace Solarium\Tests\Integration\SolrServer;

use Solarium\Core\Client\Adapter\Http;
use Solarium\Tests\Integration\AbstractServerTest;

/**
 * @group integration
 * @group skip_for_solr_cloud
 * @coversNothing
 */
class HttpTest extends AbstractServerTest
{
    public function setUp(): void
    {
        parent::setUp();
        self::$client->setAdapter(new Http());
    }
}
