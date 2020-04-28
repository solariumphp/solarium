<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\Core\Client\Adapter\Http;
use Solarium\Tests\Integration\AbstractCollectionsTest;

/**
 * @group integration
 * @group solr_cloud
 */
class CollectionsHttpTest extends AbstractCollectionsTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->client->setAdapter(new Http());
    }
}
