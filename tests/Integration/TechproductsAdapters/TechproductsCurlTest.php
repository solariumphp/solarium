<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Tests\Integration\AbstractCoreTest;

/**
 * @group integration
 * @group skip_for_solr_server
 * @coversNothing
 */
class TechproductsCurlTest extends AbstractCoreTest
{
    public function setUp(): void
    {
        parent::setUp();
        // The default timeout of solarium of 5s seems to be too aggressive on travis and causes random test failures.
        // Set it to the PHP default of 13s.
        $adapter = new Curl();
        $adapter->setTimeout(CURLOPT_TIMEOUT);
        $this->client->setAdapter($adapter);
    }
}
