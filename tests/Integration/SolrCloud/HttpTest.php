<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\Core\Client\Adapter\Http;
use Solarium\Tests\Integration\AbstractCloudTestCase;

/**
 * @group integration
 * @group skip_for_solr_server
 */
class HttpTest extends AbstractCloudTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        self::$client->setAdapter(new Http());
    }
}
