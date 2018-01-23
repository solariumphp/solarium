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

namespace Solarium\Tests\QueryType\Select\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Component\MoreLikeThis;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Select\Query\FilterQuery;
use Solarium\QueryType\Select\Query\Query;

abstract class AbstractQueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_SELECT, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\ResponseParser',
            $this->query->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder',
            $this->query->getRequestBuilder()
        );
    }

    public function testSetAndGetStart()
    {
        $this->query->setStart(234);
        $this->assertSame(234, $this->query->getStart());
    }

    public function testSetAndGetQueryWithTrim()
    {
        $this->query->setQuery(' *:* ');
        $this->assertSame('*:*', $this->query->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->query->setQuery('id:%1%', array(678));
        $this->assertSame('id:678', $this->query->getQuery());
    }

    public function testSetAndGetQueryDefaultOperator()
    {
        $value = Query::QUERY_OPERATOR_AND;

        $this->query->setQueryDefaultOperator($value);
        $this->assertSame($value, $this->query->getQueryDefaultOperator());
    }

    public function testSetAndGetQueryDefaultField()
    {
        $value = 'mydefault';

        $this->query->setQueryDefaultField($value);
        $this->assertSame($value, $this->query->getQueryDefaultField());
    }

    public function testSetAndGetResultClass()
    {
        $this->query->setResultClass('MyResult');
        $this->assertSame('MyResult', $this->query->getResultClass());
    }

    public function testSetAndGetDocumentClass()
    {
        $this->query->setDocumentClass('MyDocument');
        $this->assertSame('MyDocument', $this->query->getDocumentClass());
    }

    public function testSetAndGetRows()
    {
        $this->query->setRows(100);
        $this->assertSame(100, $this->query->getRows());
    }

    public function testAddField()
    {
        $expectedFields = $this->query->getFields();
        $expectedFields[] = 'newfield';
        $this->query->addField('newfield');
        $this->assertSame($expectedFields, $this->query->getFields());
    }

    public function testClearFields()
    {
        $this->query->addField('newfield');
        $this->query->clearFields();
        $this->assertSame(array(), $this->query->getFields());
    }

    public function testAddFields()
    {
        $fields = array('field1', 'field2');

        $this->query->clearFields();
        $this->query->addFields($fields);
        $this->assertSame($fields, $this->query->getFields());
    }

    public function testAddFieldsAsStringWithTrim()
    {
        $this->query->clearFields();
        $this->query->addFields('field1, field2');
        $this->assertSame(array('field1', 'field2'), $this->query->getFields());
    }

    public function testRemoveField()
    {
        $this->query->clearFields();
        $this->query->addFields(array('field1', 'field2'));
        $this->query->removeField('field1');
        $this->assertSame(array('field2'), $this->query->getFields());
    }

    public function testSetFields()
    {
        $this->query->clearFields();
        $this->query->addFields(array('field1', 'field2'));
        $this->query->setFields(array('field3', 'field4'));
        $this->assertSame(array('field3', 'field4'), $this->query->getFields());
    }

    public function testAddSort()
    {
        $this->query->addSort('field1', Query::SORT_DESC);
        $this->assertSame(
            array('field1' => Query::SORT_DESC),
            $this->query->getSorts()
        );
    }

    public function testAddSorts()
    {
        $sorts = array(
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        );

        $this->query->addSorts($sorts);
        $this->assertSame(
            $sorts,
            $this->query->getSorts()
        );
    }

    public function testRemoveSort()
    {
        $sorts = array(
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        );

        $this->query->addSorts($sorts);
        $this->query->removeSort('field1');
        $this->assertSame(
            array('field2' => Query::SORT_ASC),
            $this->query->getSorts()
        );
    }

    public function testRemoveInvalidSort()
    {
        $sorts = array(
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        );

        $this->query->addSorts($sorts);
        $this->query->removeSort('invalidfield'); //continue silently
        $this->assertSame(
            $sorts,
            $this->query->getSorts()
        );
    }

    public function testClearSorts()
    {
        $sorts = array(
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        );

        $this->query->addSorts($sorts);
        $this->query->clearSorts();
        $this->assertSame(
            array(),
            $this->query->getSorts()
        );
    }

    public function testSetSorts()
    {
        $sorts = array(
            'field1' => Query::SORT_DESC,
            'field2' => Query::SORT_ASC,
        );

        $this->query->addSorts($sorts);
        $this->query->setSorts(array('field3' => Query::SORT_ASC));
        $this->assertSame(
            array('field3' => Query::SORT_ASC),
            $this->query->getSorts()
        );
    }

    public function testAddAndGetFilterQuery()
    {
        $fq = new FilterQuery;
        $fq->setKey('fq1')->setQuery('category:1');
        $this->query->addFilterQuery($fq);

        $this->assertSame(
            $fq,
            $this->query->getFilterQuery('fq1')
        );
    }

    public function testAddAndGetFilterQueryWithKey()
    {
        $key = 'fq1';

        $fq = $this->query->createFilterQuery($key);
        $fq->setQuery('category:1');

        $this->assertSame(
            $key,
            $fq->getKey()
        );

        $this->assertSame(
            $fq,
            $this->query->getFilterQuery('fq1')
        );
    }

    public function testAddFilterQueryWithoutKey()
    {
        $fq = new FilterQuery;
        $fq->setQuery('category:1');

        $this->expectException('Solarium\Exception\InvalidArgumentException');
        $this->query->addFilterQuery($fq);
    }

    public function testAddFilterQueryWithUsedKey()
    {
        $fq1 = new FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery;
        $fq2->setKey('fq1')->setQuery('category:2');

        $this->query->addFilterQuery($fq1);
        $this->expectException('Solarium\Exception\InvalidArgumentException');
        $this->query->addFilterQuery($fq2);
    }

    public function testGetInvalidFilterQuery()
    {
        $this->assertSame(
            null,
            $this->query->getFilterQuery('invalidtag')
        );
    }

    public function testAddFilterQueries()
    {
        $fq1 = new FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array('fq1' => $fq1, 'fq2' => $fq2);

        $this->query->addFilterQueries($filterQueries);
        $this->assertSame(
            $filterQueries,
            $this->query->getFilterQueries()
        );
    }

    public function testRemoveFilterQuery()
    {
        $fq1 = new FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array($fq1, $fq2);

        $this->query->addFilterQueries($filterQueries);
        $this->query->removeFilterQuery('fq1');
        $this->assertSame(
            array('fq2' => $fq2),
            $this->query->getFilterQueries()
        );
    }

    public function testRemoveFilterQueryWithObjectInput()
    {
        $fq1 = new FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array($fq1, $fq2);

        $this->query->addFilterQueries($filterQueries);
        $this->query->removeFilterQuery($fq1);
        $this->assertSame(
            array('fq2' => $fq2),
            $this->query->getFilterQueries()
        );
    }

    public function testRemoveInvalidFilterQuery()
    {
        $fq1 = new FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array('fq1' => $fq1, 'fq2' => $fq2);

        $this->query->addFilterQueries($filterQueries);
        $this->query->removeFilterQuery('fq3'); //continue silently
        $this->assertSame(
            $filterQueries,
            $this->query->getFilterQueries()
        );
    }

    public function testClearFilterQueries()
    {
        $fq1 = new FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array($fq1, $fq2);

        $this->query->addFilterQueries($filterQueries);
        $this->query->clearFilterQueries();
        $this->assertSame(
            array(),
            $this->query->getFilterQueries()
        );
    }

    public function testSetFilterQueries()
    {
        $fq1 = new FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries1 = array('fq1' => $fq1, 'fq2' => $fq2);

        $this->query->addFilterQueries($filterQueries1);

        $fq3 = new FilterQuery;
        $fq3->setKey('fq3')->setQuery('category:3');

        $fq4 = new FilterQuery;
        $fq4->setKey('fq4')->setQuery('category:4');

        $filterQueries2 = array('fq3' => $fq3, 'fq4' => $fq4);

        $this->query->setFilterQueries($filterQueries2);

        $this->assertSame(
            $filterQueries2,
            $this->query->getFilterQueries()
        );
    }

    public function testConfigMode()
    {
        $config = array(
            'query' => 'text:mykeyword',
            'sort' => array('score' => 'asc'),
            'fields' => array('id', 'title', 'category'),
            'rows' => 100,
            'start' => 200,
            'filterquery' => array(
                array('key' => 'pub', 'tag' => array('pub'), 'query' => 'published:true'),
                'online' => array('tag' => 'onl', 'query' => 'online:true')
            ),
            'component' => array(
                'facetset' => array(
                    'facet' => array(
                        array('type' => 'field', 'key' => 'categories', 'field' => 'category'),
                        'category13' => array('type' => 'query', 'query' => 'category:13')
                    )
                ),
            ),
            'resultclass' => 'MyResultClass',
            'documentclass' => 'MyDocumentClass',
            'tag' => array('t1', 't2'),
        );
        $query = new Query($config);

        $this->assertSame($config['query'], $query->getQuery());
        $this->assertSame($config['sort'], $query->getSorts());
        $this->assertSame($config['fields'], $query->getFields());
        $this->assertSame($config['rows'], $query->getRows());
        $this->assertSame($config['start'], $query->getStart());
        $this->assertSame($config['documentclass'], $query->getDocumentClass());
        $this->assertSame($config['resultclass'], $query->getResultClass());
        $this->assertSame('published:true', $query->getFilterQuery('pub')->getQuery());
        $this->assertSame('online:true', $query->getFilterQuery('online')->getQuery());

        $facets = $query->getFacetSet()->getFacets();
        $this->assertSame(
            'category',
            $facets['categories']->getField()
        );
        $this->assertSame(
            'category:13',
            $facets['category13']->getQuery()
        );

        $components = $query->getComponents();
        $this->assertCount(1, $components);
        $this->assertThat(
            array_pop($components),
            $this->isInstanceOf('Solarium\Component\FacetSet')
        );
        $this->assertSame(array('t1', 't2'), $query->getTags());
    }

    public function testConfigModeWithSingleValueTag()
    {
        $query = $query = new Query(array('tag' => 't1'));
        $this->assertSame(array('t1'), $query->getTags());
    }

    public function testSetAndGetComponents()
    {
        $mlt = new MoreLikeThis;
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            array('mlt' => $mlt),
            $this->query->getComponents()
        );
    }

    public function testSetAndGetComponent()
    {
        $mlt = new MoreLikeThis;
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            $mlt,
            $this->query->getComponent('mlt')
        );
    }

    public function testSetAndGetComponentQueryInstance()
    {
        $mlt = new MoreLikeThis;
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            $this->query,
            $this->query->getComponent('mlt')->getQueryInstance()
        );
    }

    public function testGetInvalidComponent()
    {
        $this->assertSame(
            null,
            $this->query->getComponent('invalid')
        );
    }

    public function testGetInvalidComponentAutoload()
    {
        $this->expectException('Solarium\Exception\OutOfBoundsException');
        $this->query->getComponent('invalid', true);
    }

    public function testRemoveComponent()
    {
        $mlt = new MoreLikeThis;
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            array('mlt' => $mlt),
            $this->query->getComponents()
        );

        $this->query->removeComponent('mlt');

        $this->assertSame(
            array(),
            $this->query->getComponents()
        );
    }

    public function testRemoveComponentWithObjectInput()
    {
        $mlt = new MoreLikeThis;
        $this->query->setComponent('mlt', $mlt);

        $this->assertSame(
            array('mlt' => $mlt),
            $this->query->getComponents()
        );

        $this->query->removeComponent($mlt);

        $this->assertSame(
            array(),
            $this->query->getComponents()
        );
    }

    public function testGetMoreLikeThis()
    {
        $mlt = $this->query->getMoreLikeThis();

        $this->assertSame(
            'Solarium\Component\MoreLikeThis',
            get_class($mlt)
        );
    }

    public function testGetDisMax()
    {
        $dismax = $this->query->getDisMax();

        $this->assertSame(
            'Solarium\Component\DisMax',
            get_class($dismax)
        );
    }

    public function testGetHighlighting()
    {
        $hlt = $this->query->getHighlighting();

        $this->assertSame(
            'Solarium\Component\Highlighting\Highlighting',
            get_class($hlt)
        );
    }

    public function testGetGrouping()
    {
        $grouping = $this->query->getGrouping();

        $this->assertSame(
            'Solarium\Component\Grouping',
            get_class($grouping)
        );
    }

    public function testRegisterComponentType()
    {
        $components = $this->query->getComponentTypes();
        $components['mykey'] = 'mycomponent';

        $this->query->registerComponentType('mykey', 'mycomponent');

        $this->assertSame(
            $components,
            $this->query->getComponentTypes()
        );
    }

    public function testCreateFilterQuery()
    {
        $options = array('optionA' => 1, 'optionB' => 2);
        $fq = $this->query->createFilterQuery($options);

        // check class
        $this->assertThat($fq, $this->isInstanceOf('Solarium\QueryType\Select\Query\FilterQuery'));

        // check option forwarding
        $fqOptions = $fq->getOptions();
        $this->assertSame(
            $options['optionB'],
            $fqOptions['optionB']
        );
    }

    public function testGetSpellcheck()
    {
        $spellcheck = $this->query->getSpellcheck();

        $this->assertSame(
            'Solarium\Component\Spellcheck',
            get_class($spellcheck)
        );
    }

    public function testGetSuggester()
    {
        $suggester = $this->query->getSuggester();

        $this->assertSame(
            'Solarium\Component\Suggester',
            get_class($suggester)
        );
    }

    public function testGetDistributedSearch()
    {
        $spellcheck = $this->query->getDistributedSearch();

        $this->assertSame(
            'Solarium\Component\DistributedSearch',
            get_class($spellcheck)
        );
    }

    public function testGetStats()
    {
        $stats = $this->query->getStats();

        $this->assertSame(
            'Solarium\Component\Stats\Stats',
            get_class($stats)
        );
    }

    public function testGetDebug()
    {
        $stats = $this->query->getDebug();

        $this->assertSame(
            'Solarium\Component\Debug',
            get_class($stats)
        );
    }

    public function testAddTag()
    {
        $this->query->addTag('testtag');
        $this->assertSame(array('testtag'), $this->query->getTags());
    }

    public function testAddTags()
    {
        $this->query->addTags(array('t1', 't2'));
        $this->assertSame(array('t1', 't2'), $this->query->getTags());
    }

    public function testRemoveTag()
    {
        $this->query->addTags(array('t1', 't2'));
        $this->query->removeTag('t1');
        $this->assertSame(array('t2'), $this->query->getTags());
    }

    public function testClearTags()
    {
        $this->query->addTags(array('t1', 't2'));
        $this->query->clearTags();
        $this->assertSame(array(), $this->query->getTags());
    }

    public function testSetTags()
    {
        $this->query->addTags(array('t1', 't2'));
        $this->query->setTags(array('t3', 't4'));
        $this->assertSame(array('t3', 't4'), $this->query->getTags());
    }

    public function testGetSpatial()
    {
        $spatial = $this->query->getSpatial();

        $this->assertSame(
            'Solarium\Component\Spatial',
            get_class($spatial)
        );
    }
}
