<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Tests\Integration\AbstractCloudTest;

/**
 * @group integration
 * @group skip_for_solr_server
 * @coversNothing
 */
class CurlTest extends AbstractCloudTest
{
    public function setUp(): void
    {
        parent::setUp();
        // The default timeout of Solarium of 5s seems to be too aggressive on Travis and causes random test failures.
        // Set it to the PHP default of 13s.
        $adapter = new Curl();
        $adapter->setTimeout(CURLOPT_TIMEOUT);
        self::$client->setAdapter($adapter);
    }
}
