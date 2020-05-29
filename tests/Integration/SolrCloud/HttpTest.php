<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\Core\Client\Adapter\Http;
use Solarium\Tests\Integration\AbstractCloudTest;

/**
 * @group integration
 * @group skip_for_solr_server
 * @coversNothing
 */
class HttpTest extends AbstractCloudTest
{
    public function setUp(): void
    {
        parent::setUp();
        self::$client->setAdapter(new Http());
    }
}
