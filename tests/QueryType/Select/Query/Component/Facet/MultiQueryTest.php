<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\MultiQuery;
use Solarium\Component\Facet\Query;
use Solarium\Component\FacetSet;
use Solarium\Exception\InvalidArgumentException;

class MultiQueryTest extends TestCase
{
    /**
     * @var MultiQuery
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new MultiQuery();
    }

    public function testConfigMode()
    {
        $options = [
            'key' => 'myKey',
            'exclude' => ['e1', 'e2'],
            'query' => [
                [
                    'key' => 'k1',
                    'query' => 'category:1',
                    'exclude' => ['fq1', 'fq2'],
                ],
                'k2' => [
                    'query' => 'category:2',
                ],
            ],
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['key'], $this->facet->getKey());
        $this->assertSame($options['exclude'], $this->facet->getExcludes());

        $query1 = $this->facet->getQuery('k1');
        $this->assertSame('k1', $query1->getKey());
        $this->assertSame('category:1', $query1->getQuery());
        $this->assertEquals(['fq1', 'fq2', 'e1', 'e2'], $query1->getExcludes());

        $query2 = $this->facet->getQuery('k2');
        $this->assertSame('k2', $query2->getKey());
        $this->assertSame('category:2', $query2->getQuery());
    }

    public function testConfigModeSingleQuery()
    {
        $options = [
            'query' => 'category:2',
        ];

        $this->facet->setOptions($options);
        $this->assertSame('category:2', $this->facet->getQuery(0)->getQuery());
    }

    public function testGetType()
    {
        $this->assertSame(FacetSet::FACET_MULTIQUERY, $this->facet->getType());
    }

    public function testCreateAndGetQuery()
    {
        $key = 'k1';
        $query = 'category:1';
        $excludes = ['fq1', 'fq2'];

        $facetQuery = new Query();
        $facetQuery->setKey($key);
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        $this->facet->createQuery($key, $query, $excludes);

        $this->assertEquals($facetQuery, $this->facet->getQuery($key));
    }

    public function testAddAndGetQuery()
    {
        $key = 'k1';
        $query = 'category:1';
        $excludes = ['fq1', 'fq2'];

        $facetQuery = new Query();
        $facetQuery->setKey($key);
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        $this->facet->addQuery($facetQuery);

        $this->assertEquals($facetQuery, $this->facet->getQuery($key));
    }

    public function testAddQueryWithConfig()
    {
        $config = [
            'key' => 'k1',
            'query' => 'category:1',
            'excludes' => ['fq1', 'fq2'],
        ];

        $facetQuery = new Query($config);

        $this->facet->addQuery($config);

        $this->assertEquals($facetQuery, $this->facet->getQuery($config['key']));
    }

    public function testAddQueryNoKey()
    {
        $query = 'category:1';
        $excludes = ['fq1', 'fq2'];

        $facetQuery = new Query();
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        $this->expectException(InvalidArgumentException::class);
        $this->facet->addQuery($facetQuery);
    }

    public function testAddQueryNoUniqueKey()
    {
        $facetQuery1 = new Query();
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');

        $facetQuery2 = new Query();
        $facetQuery2->setKey('k1');
        $facetQuery2->setQuery('category:2');

        $this->facet->addQuery($facetQuery1);

        $this->expectException(InvalidArgumentException::class);
        $this->facet->addQuery($facetQuery2);
    }

    public function testAddQueryExcludeForwarding()
    {
        $this->facet->addExclude('fq1');

        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');

        $this->facet->addQuery($facetQuery);

        $this->assertEquals(['fq1'], $facetQuery->getExcludes());
    }

    public function testAddAndGetQueries()
    {
        $facetQuery1 = new Query();
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');

        $facetQuery2 = new Query();
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $this->facet->addQueries([$facetQuery1, $facetQuery2]);

        $this->assertEquals(['k1' => $facetQuery1, 'k2' => $facetQuery2], $this->facet->getQueries());
    }

    public function testAddQueriesWithConfig()
    {
        $facetQuery1 = new Query();
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');
        $facetQuery1->addExcludes(['fq1', 'fq2']);

        $facetQuery2 = new Query();
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $facetQueries = ['k1' => $facetQuery1, 'k2' => $facetQuery2];

        $config = [
            [
                'key' => 'k1',
                'query' => 'category:1',
                'exclude' => ['fq1', 'fq2'],
            ],
            'k2' => [
                'query' => 'category:2',
            ],
        ];
        $this->facet->addQueries($config);

        $this->assertEquals($facetQueries, $this->facet->getQueries());
    }

    public function testGetInvalidQuery()
    {
        $this->assertNull($this->facet->getQuery('invalidkey'));
    }

    public function testRemoveQuery()
    {
        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');

        $this->facet->addQuery($facetQuery);
        $this->assertSame(
            ['k1' => $facetQuery],
            $this->facet->getQueries()
        );

        $this->facet->removeQuery('k1');
        $this->assertSame([], $this->facet->getQueries());
    }

    public function testRemoveQueryWithObjectInput()
    {
        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');

        $this->facet->addQuery($facetQuery);
        $this->assertSame(['k1' => $facetQuery], $this->facet->getQueries());

        $this->facet->removeQuery($facetQuery);
        $this->assertSame([], $this->facet->getQueries());
    }

    public function testRemoveInvalidQuery()
    {
        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $before = $this->facet->getQueries();
        $this->facet->removeQuery('invalidkey');
        $after = $this->facet->getQueries();

        $this->assertSame($before, $after);
    }

    public function testClearQueries()
    {
        $facetQuery1 = new Query();
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');

        $facetQuery2 = new Query();
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $this->facet->addQueries([$facetQuery1, $facetQuery2]);
        $this->facet->clearQueries();
        $this->assertSame(
            [],
            $this->facet->getQueries()
        );
    }

    public function testSetQueries()
    {
        $facetQuery1 = new Query();
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');
        $this->facet->addQuery($facetQuery1);

        $facetQuery2 = new Query();
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $facetQuery3 = new Query();
        $facetQuery3->setKey('k3');
        $facetQuery3->setQuery('category:3');

        $this->facet->setQueries([$facetQuery2, $facetQuery3]);
        $this->assertSame(
            ['k2' => $facetQuery2, 'k3' => $facetQuery3],
            $this->facet->getQueries()
        );
    }

    public function testAddExcludeForwarding()
    {
        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $this->facet->addExclude('fq1');

        $this->assertSame(
            ['fq1'],
            $facetQuery->getExcludes()
        );
    }

    public function testRemoveExcludeForwarding()
    {
        $this->facet->addExclude('fq1');

        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $this->assertSame(
            ['fq1'],
            $facetQuery->getExcludes()
        );

        $this->facet->removeExclude('fq1');

        $this->assertSame(
            [],
            $facetQuery->getExcludes()
        );
    }

    public function testClearExcludesForwarding()
    {
        $this->facet->addExclude('fq1');
        $this->facet->addExclude('fq2');

        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $this->assertSame(
            ['fq1', 'fq2'],
            $facetQuery->getExcludes()
        );

        $this->facet->clearExcludes();

        $this->assertSame(
            [],
            $facetQuery->getExcludes()
        );
    }
}
