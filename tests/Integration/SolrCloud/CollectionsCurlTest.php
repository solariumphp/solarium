<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\Tests\Integration\AbstractCollectionsTest;

/**
 * @group integration
 * @group solr_cloud
 */
class CollectionsCurlTest extends AbstractCollectionsTest
{
    public function setUp()
    {
        parent::setUp();
        // The default timeout of solarium of 5s seems to be too aggressive on travis and causes random test failures.
        // Set it to the PHP default of 13s.
        $this->client->getEndpoint()->setTimeout(CURLOPT_TIMEOUT);
    }
}
