<?php

namespace Solarium\Tests\Component\Facet;

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

    public function setUp(): void
    {
        $this->facet = new MultiQuery();
    }

    public function testConfigMode()
    {
        $options = [
            'local_key' => 'myKey',
            'local_exclude' => ['e1', 'e2'],
            'query' => [
                [
                    'local_key' => 'k1',
                    'query' => 'category:1',
                    'local_exclude' => ['fq1', 'fq2'],
                ],
                'k2' => [
                    'query' => 'category:2',
                ],
            ],
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['local_key'], $this->facet->getKey());
        $this->assertSame($options['local_exclude'], $this->facet->getLocalParameters()->getExcludes());

        $query1 = $this->facet->getQuery('k1');
        $this->assertSame('k1', $query1->getKey());
        $this->assertSame('category:1', $query1->getQuery());
        $this->assertEquals(['fq1', 'fq2', 'e1', 'e2'], $query1->getLocalParameters()->getExcludes());

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
        $facetQuery->getLocalParameters()->addExcludes($excludes);

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
        $facetQuery->getLocalParameters()->addExcludes($excludes);

        $this->facet->addQuery($facetQuery);

        $this->assertEquals($facetQuery, $this->facet->getQuery($key));
    }

    public function testAddQueryWithConfig()
    {
        $config = [
            'local_key' => 'k1',
            'query' => 'category:1',
            'local_excludes' => ['fq1', 'fq2'],
        ];

        $facetQuery = new Query($config);

        $this->facet->addQuery($config);

        $this->assertEquals($facetQuery, $this->facet->getQuery($config['local_key']));
    }

    public function testAddQueryNoKey()
    {
        $facetQuery = new Query();
        $facetQuery->setQuery('category:1');

        $this->expectException(InvalidArgumentException::class);
        $this->facet->addQuery($facetQuery);
    }

    public function testAddQueryEmptyKey()
    {
        $facetQuery = new Query();
        $facetQuery->setKey('');
        $facetQuery->setQuery('category:1');

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

        $this->assertEquals(['fq1'], $facetQuery->getLocalParameters()->getExcludes());
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
        $facetQuery1->getLocalParameters()->addExcludes(['fq1', 'fq2']);

        $facetQuery2 = new Query();
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $facetQueries = ['k1' => $facetQuery1, 'k2' => $facetQuery2];

        $config = [
            [
                'local_key' => 'k1',
                'query' => 'category:1',
                'local_exclude' => ['fq1', 'fq2'],
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

    public function testAddExclude()
    {
        $this->facet->addExclude('e1');
        $this->assertEquals(['e1'], $this->facet->getExcludes());
        $this->assertEquals(['e1'], $this->facet->getLocalParameters()->getExcludes());

        $this->facet->addExclude('e2');
        $this->assertEquals(['e1', 'e2'], $this->facet->getExcludes());
        $this->assertEquals(['e1', 'e2'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testAddExcludes()
    {
        $this->facet->addExcludes(['e1', 'e2']);
        $this->assertEquals(['e1', 'e2'], $this->facet->getExcludes());
        $this->assertEquals(['e1', 'e2'], $this->facet->getLocalParameters()->getExcludes());

        $this->facet->addExcludes('e3,e4');
        $this->assertEquals(['e1', 'e2', 'e3', 'e4'], $this->facet->getExcludes());
        $this->assertEquals(['e1', 'e2', 'e3', 'e4'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testSetExcludes()
    {
        $this->facet->setExcludes(['e1', 'e2']);
        $this->assertEquals(['e1', 'e2'], $this->facet->getExcludes());
        $this->assertEquals(['e1', 'e2'], $this->facet->getLocalParameters()->getExcludes());

        $this->facet->setExcludes('e3,e4');
        $this->assertEquals(['e3', 'e4'], $this->facet->getExcludes());
        $this->assertEquals(['e3', 'e4'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testSetAndAddTermsWithEscapedSeparator()
    {
        $this->facet->setExcludes('e1\,e2,e3');
        $this->assertEquals(['e1\,e2', 'e3'], $this->facet->getExcludes());
        $this->assertEquals(['e1\,e2', 'e3'], $this->facet->getLocalParameters()->getExcludes());

        $this->facet->addExcludes('e4\,e5,e6');
        $this->assertEquals(['e1\,e2', 'e3', 'e4\,e5', 'e6'], $this->facet->getExcludes());
        $this->assertEquals(['e1\,e2', 'e3', 'e4\,e5', 'e6'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testRemoveExclude()
    {
        $this->facet->setExcludes(['e1', 'e2']);
        $this->facet->removeExclude('e1');
        $this->assertEquals(['e2'], $this->facet->getExcludes());
        $this->assertEquals(['e2'], $this->facet->getLocalParameters()->getExcludes());
    }

    public function testClearExcludes()
    {
        $this->facet->setExcludes(['e1', 'e2']);
        $this->facet->clearExcludes();
        $this->assertEquals([], $this->facet->getExcludes());
        $this->assertEquals([], $this->facet->getLocalParameters()->getExcludes());
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

    public function testAddExcludesForwarding()
    {
        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $this->facet->addExcludes(['fq1', 'fq2']);

        $this->assertSame(
            ['fq1', 'fq2'],
            $facetQuery->getExcludes()
        );
    }

    public function testSetExcludesForwarding()
    {
        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $this->facet->setExcludes(['fq1', 'fq2']);

        $this->assertSame(
            ['fq1', 'fq2'],
            $facetQuery->getExcludes()
        );
    }

    public function testRemoveExcludeForwarding()
    {
        $this->facet->getLocalParameters()->setExclude('fq1');

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
        $this->facet->getLocalParameters()->setExclude('fq1');
        $this->facet->getLocalParameters()->setExclude('fq2');

        $facetQuery = new Query();
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $this->assertSame(
            ['fq1', 'fq2'],
            $facetQuery->getLocalParameters()->getExcludes()
        );

        $this->facet->clearExcludes();

        $this->assertSame(
            [],
            $facetQuery->getLocalParameters()->getExcludes()
        );
    }
}
