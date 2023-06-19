<?php

namespace Solarium\Tests\Integration\SolrServer;

use Solarium\Core\Client\Adapter\Http;
use Solarium\Tests\Integration\AbstractServerTestCase;

/**
 * @group integration
 * @group skip_for_solr_cloud
 */
class HttpTest extends AbstractServerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        self::$client->setAdapter(new Http());
    }
}
