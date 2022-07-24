<?php

namespace Solarium\Tests\Plugin\CustomizeRequest;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\MinimumScoreFilter\MinimumScoreFilter;
use Solarium\Plugin\MinimumScoreFilter\Query;
use Solarium\Tests\Integration\TestClientFactory;

class MinimumScoreFilterTest extends TestCase
{
    public function testInitPlugin()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('minimumscorefilter');

        $this->assertInstanceOf(MinimumScoreFilter::class, $plugin);

        $this->assertSame(
            Query::class,
            $client->getQueryTypes()[MinimumScoreFilter::QUERY_TYPE]
        );
    }
}
