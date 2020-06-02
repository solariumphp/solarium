<?php

namespace Solarium\Tests\Integration\SolrServer;

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Tests\Integration\AbstractServerTest;

/**
 * @group integration
 * @group skip_for_solr_cloud
 * @coversNothing
 */
class CurlTest extends AbstractServerTest
{
    public function setUp(): void
    {
        parent::setUp();
        // The default timeout of solarium of 5s seems to be too aggressive on travis and causes random test failures.
        // Set it to the PHP default of 13s.
        $adapter = new Curl();
        $adapter->setTimeout(CURLOPT_TIMEOUT);
        self::$client->setAdapter($adapter);
    }
}
