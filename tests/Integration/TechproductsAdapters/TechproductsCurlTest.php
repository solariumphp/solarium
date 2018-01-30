<?php

namespace Solarium\Tests\Integration\TechproductsAdapters;

use Solarium\Tests\Integration\AbstractTechproductsTest;

/**
 * @group integration
 */
class TechproductsCurlTest extends AbstractTechproductsTest
{
    public function setUp()
    {
        parent::setUp();
        // The default timeout of solarium of 5s seems to be too aggressive on travis and causes random test failures.
        // Set it to the PHP default of 13s.
        $this->client->getEndpoint()->setTimeout(CURLOPT_TIMEOUT);
    }
}
