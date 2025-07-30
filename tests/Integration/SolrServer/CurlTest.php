<?php

namespace Solarium\Tests\Integration\SolrServer;

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Tests\Integration\AbstractServerTestCase;

/**
 * @requires extension curl
 *
 * @group integration
 * @group skip_for_solr_cloud
 */
class CurlTest extends AbstractServerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // The default timeout of Solarium of 5s seems to be too aggressive on Travis and causes random test failures.
        $adapter = new Curl();
        $adapter->setTimeout(15);
        $adapter->setConnectionTimeout(5);
        self::$client->setAdapter($adapter);
    }
}
