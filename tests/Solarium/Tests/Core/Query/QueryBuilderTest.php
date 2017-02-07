<?php

namespace Solarium\Tests\Core\Query;

use Solarium\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\QueryBuilder;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function usage()
    {
        $client = new Client();

        $builder = new QueryBuilder($client);
        $builder
            ->select('title', 'content')
            ->where('id')
                ->equals('id_prefix_*')
            ->andWhere('title_s')
                ->equals('a title')
            ->orWhere('tags_ss')
                ->inSet(array('solr', 'symfony'))
            ->andWhere('latlong')
                ->inDistance(10, 10, 10);

        $query = $builder->getQuery();

        $this->assertInstanceOf(AbstractQuery::class, $query);

        $expected = 'id:id_prefix_* AND title_s:a title OR tags_ss:(solr, symfony) AND latlong:{!geofilt pt=10,10 sfield= AND latlong: d=10}';

        $this->assertEquals($expected, $query->getQuery());
    }
}
