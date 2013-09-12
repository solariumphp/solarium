<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use Solarium\QueryType\Select\Query\Component\Facet\MultiQuery;
use Solarium\QueryType\Select\Query\Component\Facet\Query;
use Solarium\QueryType\Select\Query\Component\FacetSet;

class MultiQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MultiQuery
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new MultiQuery;
    }

    public function testConfigMode()
    {
        $options = array(
            'key' => 'myKey',
            'exclude' => array('e1', 'e2'),
            'query' => array(
                array(
                    'key' => 'k1',
                    'query' => 'category:1',
                    'exclude' => array('fq1', 'fq2')
                ),
                'k2' => array(
                    'query' => 'category:2',
                ),
            )
        );

        $this->facet->setOptions($options);

        $this->assertEquals($options['key'], $this->facet->getKey());
        $this->assertEquals($options['exclude'], $this->facet->getExcludes());

        $query1 = $this->facet->getQuery('k1');
        $this->assertEquals('k1', $query1->getKey());
        $this->assertEquals('category:1', $query1->getQuery());
        $this->assertEquals(array('fq1', 'fq2', 'e1', 'e2'), $query1->getExcludes());

        $query2 = $this->facet->getQuery('k2');
        $this->assertEquals('k2', $query2->getKey());
        $this->assertEquals('category:2', $query2->getQuery());
    }

    public function testConfigModeSingleQuery()
    {
        $options = array(
            'query' => 'category:2',
        );

        $this->facet->setOptions($options);
        $this->assertEquals('category:2', $this->facet->getQuery(0)->getQuery());

    }

    public function testGetType()
    {
        $this->assertEquals(
            FacetSet::FACET_MULTIQUERY,
            $this->facet->getType()
        );
    }

    public function testCreateAndGetQuery()
    {
        $key = 'k1';
        $query = 'category:1';
        $excludes = array('fq1', 'fq2');

        $facetQuery = new Query;
        $facetQuery->setKey($key);
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        $this->facet->createQuery($key, $query, $excludes);

        $this->assertEquals(
            $facetQuery,
            $this->facet->getQuery($key)
        );
    }

    public function testAddAndGetQuery()
    {
        $key = 'k1';
        $query = 'category:1';
        $excludes = array('fq1', 'fq2');

        $facetQuery = new Query;
        $facetQuery->setKey($key);
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        $this->facet->addQuery($facetQuery);

        $this->assertEquals(
            $facetQuery,
            $this->facet->getQuery($key)
        );
    }

    public function testAddQueryWithConfig()
    {
        $config = array(
            'key' => 'k1',
            'query' => 'category:1',
            'excludes' => array('fq1', 'fq2')
        );

        $facetQuery = new Query($config);

        $this->facet->addQuery($config);

        $this->assertEquals(
            $facetQuery,
            $this->facet->getQuery($config['key'])
        );
    }

    public function testAddQueryNoKey()
    {
        $query = 'category:1';
        $excludes = array('fq1', 'fq2');

        $facetQuery = new Query;
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->facet->addQuery($facetQuery);
    }

    public function testAddQueryNoUniqueKey()
    {
        $facetQuery1 = new Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');

        $facetQuery2 = new Query;
        $facetQuery2->setKey('k1');
        $facetQuery2->setQuery('category:2');

        $this->facet->addQuery($facetQuery1);

        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->facet->addQuery($facetQuery2);
    }

    public function testAddQueryExcludeForwarding()
    {
        $this->facet->addExclude('fq1');

        $facetQuery = new Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');

        $this->facet->addQuery($facetQuery);

        $this->assertEquals(
            array('fq1'),
            $facetQuery->getExcludes()
        );
    }

    public function testAddAndGetQueries()
    {
        $facetQuery1 = new Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');

        $facetQuery2 = new Query;
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $this->facet->addQueries(array($facetQuery1, $facetQuery2));

        $this->assertEquals(
            array('k1' => $facetQuery1, 'k2' => $facetQuery2),
            $this->facet->getQueries()
        );
    }

    public function testAddQueriesWithConfig()
    {
        $facetQuery1 = new Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');
        $facetQuery1->addExcludes(array('fq1', 'fq2'));

        $facetQuery2 = new Query;
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $facetQueries = array('k1' => $facetQuery1, 'k2' => $facetQuery2);

        $config = array(
            array(
                'key' => 'k1',
                'query' => 'category:1',
                'exclude' => array('fq1', 'fq2')
            ),
            'k2' => array(
                'query' => 'category:2',
            ),
        );
        $this->facet->addQueries($config);

        $this->assertEquals(
            $facetQueries,
            $this->facet->getQueries()
        );
    }

    public function testGetInvalidQuery()
    {
        $this->assertEquals(
            null,
            $this->facet->getQuery('invalidkey')
        );
    }

    public function testRemoveQuery()
    {
        $facetQuery = new Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');

        $this->facet->addQuery($facetQuery);
        $this->assertEquals(
            array('k1' => $facetQuery),
            $this->facet->getQueries()
        );

        $this->facet->removeQuery('k1');
        $this->assertEquals(
            array(),
            $this->facet->getQueries()
        );
    }

    public function testRemoveQueryWithObjectInput()
    {
        $facetQuery = new Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');

        $this->facet->addQuery($facetQuery);
        $this->assertEquals(
            array('k1' => $facetQuery),
            $this->facet->getQueries()
        );

        $this->facet->removeQuery($facetQuery);
        $this->assertEquals(
            array(),
            $this->facet->getQueries()
        );
    }

    public function testRemoveInvalidQuery()
    {
        $facetQuery = new Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $before = $this->facet->getQueries();
        $this->facet->removeQuery('invalidkey');
        $after = $this->facet->getQueries();

        $this->assertEquals(
            $before,
            $after
        );
    }

    public function testClearQueries()
    {
        $facetQuery1 = new Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');

        $facetQuery2 = new Query;
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $this->facet->addQueries(array($facetQuery1, $facetQuery2));
        $this->facet->clearQueries();
        $this->assertEquals(
            array(),
            $this->facet->getQueries()
        );
    }

    public function testSetQueries()
    {
        $facetQuery1 = new Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');
        $this->facet->addQuery($facetQuery1);

        $facetQuery2 = new Query;
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $facetQuery3 = new Query;
        $facetQuery3->setKey('k3');
        $facetQuery3->setQuery('category:3');

        $this->facet->setQueries(array($facetQuery2, $facetQuery3));
        $this->assertEquals(
            array('k2' => $facetQuery2, 'k3' => $facetQuery3),
            $this->facet->getQueries()
        );
    }

    public function testAddExcludeForwarding()
    {
        $facetQuery = new Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $this->facet->addExclude('fq1');

        $this->assertEquals(
            array('fq1'),
            $facetQuery->getExcludes()
        );
    }

    public function testRemoveExcludeForwarding()
    {
        $this->facet->addExclude('fq1');

        $facetQuery = new Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $this->assertEquals(
            array('fq1'),
            $facetQuery->getExcludes()
        );

        $this->facet->removeExclude('fq1');

        $this->assertEquals(
            array(),
            $facetQuery->getExcludes()
        );
    }

    public function testClearExcludesForwarding()
    {
        $this->facet->addExclude('fq1');
        $this->facet->addExclude('fq2');

        $facetQuery = new Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->facet->addQuery($facetQuery);

        $this->assertEquals(
            array('fq1', 'fq2'),
            $facetQuery->getExcludes()
        );

        $this->facet->clearExcludes();

        $this->assertEquals(
            array(),
            $facetQuery->getExcludes()
        );
    }
}
