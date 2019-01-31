<?php

namespace Solarium\Tests\Integration\SolrCloud;

/**
 * @group integration
 * @group solr_cloud
 */
class CollectionsHttpTest extends AbstractCollectionsTest
{
    public function setUp()
    {
        parent::setUp();
        $this->client->setAdapter('Solarium\Core\Client\Adapter\Http');
    }
}
