<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Tests\Integration\AbstractCloudTestCase;

/**
 * @group integration
 * @group skip_for_solr_server
 */
class CurlTest extends AbstractCloudTestCase
{
    public function setUp(): void
    {
        if (!\function_exists('curl_init')) {
            $this->markTestSkipped('cURL not available, skipping cURL adapter tests.');
        }

        parent::setUp();
        // The default timeout of Solarium of 5s seems to be too aggressive on Travis and causes random test failures.
        $adapter = new Curl();
        $adapter->setTimeout(15);
        $adapter->setConnectionTimeout(5);
        self::$client->setAdapter($adapter);
    }
}
