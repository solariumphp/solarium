<?php

namespace Solarium\Tests\Integration\SolrCloud;

use Solarium\Tests\Integration\AbstractCollectionsTest;

/**
 * @group integration
 * @group solr_cloud
 */
class CollectionsZend2HttpTest extends AbstractCollectionsTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->client->setAdapter('Solarium\Core\Client\Adapter\Zend2Http');
    }
}
