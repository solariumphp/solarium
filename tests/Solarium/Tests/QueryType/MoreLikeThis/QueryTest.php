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

namespace Solarium\Tests\QueryType\MoreLikeThis;

use Solarium\QueryType\MoreLikeThis\Query;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Select\Query\FilterQuery;
use Solarium\QueryType\Select\Query\Component\MoreLikeThis;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    protected $query;

    public function setUp()
    {
        $this->query = new Query;
    }

    public function testGetType()
    {
        $this->assertEquals(Client::QUERY_MORELIKETHIS, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\MoreLikeThis\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\MoreLikeThis\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testSetAndGetStart()
    {
        $this->query->setStart(234);
        $this->assertEquals(234, $this->query->getStart());
    }

    public function testSetAndGetQueryWithTrim()
    {
        $this->query->setQuery(' *:* ');
        $this->assertEquals('*:*', $this->query->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->query->setQuery('id:%1%', array(678));
        $this->assertEquals('id:678', $this->query->getQuery());
    }

    public function testSetAndGetQueryDefaultOperator()
    {
        $value = Query::QUERY_OPERATOR_AND;

        $this->query->setQueryDefaultOperator($value);
        $this->assertEquals($value, $this->query->getQueryDefaultOperator());
    }

    public function testSetAndGetQueryDefaultField()
    {
        $value = 'mydefault';

        $this->query->setQueryDefaultField($value);
        $this->assertEquals($value, $this->query->getQueryDefaultField());
    }

    public function testSetAndGetResultClass()
    {
        $this->query->setResultClass('MyResult');
        $this->assertEquals('MyResult', $this->query->getResultClass());
    }

    public function testSetAndGetDocumentClass()
    {
        $this->query->setDocumentClass('MyDocument');
        $this->assertEquals('MyDocument', $this->query->getDocumentClass());
    }

    public function testSetAndGetRows()
    {
        $this->query->setRows(100);
        $this->assertEquals(100, $this->query->getRows());
    }

    public function testAddField()
    {
        $expectedFields = $this->query->getFields();
        $expectedFields[] = 'newfield';
        $this->query->addField('newfield');
        $this->assertEquals($expectedFields, $this->query->getFields());
    }

    public function testClearFields()
    {
        $this->query->addField('newfield');
        $this->query->clearFields();
        $this->assertEquals(array(), $this->query->getFields());
    }

    public function testAddFields()
    {
        $fields = array('field1', 'field2');

        $this->query->clearFields();
        $this->query->addFields($fields);
        $this->assertEquals($fields, $this->query->getFields());
    }

    public function testAddFieldsAsStringWithTrim()
    {
        $this->query->clearFields();
        $this->query->addFields('field1, field2');
        $this->assertEquals(array('field1', 'field2'), $this->query->getFields());
    }

    public function testRemoveField()
    {
        $this->query->clearFields();
        $this->query->addFields(array('field1', 'field2'));
        $this->query->removeField('field1');
        $this->assertEquals(array('field2'), $this->query->getFields());
    }

    public function testSetFields()
    {
        $this->query->clearFields();
        $this->query->addFields(array('field1', 'field2'));
        $this->query->setFields(array('field3', 'field4'));
        $this->assertEquals(array('field3', 'field4'), $this->query->getFields());
    }

    public function testAddSort()
    {
        $this->query->addSort('field1', Query::SORT_DESC);
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
            array('field3' => Query::SORT_ASC),
            $this->query->getSorts()
        );
    }

    public function testAddAndGetFilterQuery()
    {
        $fq = new FilterQuery;
        $fq->setKey('fq1')->setQuery('category:1');
        $this->query->addFilterQuery($fq);

        $this->assertEquals(
            $fq,
            $this->query->getFilterQuery('fq1')
        );
    }

    public function testAddAndGetFilterQueryWithKey()
    {
        $key = 'fq1';

        $fq = $this->query->createFilterQuery($key, true);
        $fq->setQuery('category:1');

        $this->assertEquals(
            $key,
            $fq->getKey()
        );

        $this->assertEquals(
            $fq,
            $this->query->getFilterQuery('fq1')
        );
    }

    public function testAddFilterQueryWithoutKey()
    {
        $fq = new FilterQuery;
        $fq->setQuery('category:1');

        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->query->addFilterQuery($fq);
    }

    public function testAddFilterQueryWithUsedKey()
    {
        $fq1 = new FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new FilterQuery;
        $fq2->setKey('fq1')->setQuery('category:2');

        $this->query->addFilterQuery($fq1);
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->query->addFilterQuery($fq2);
    }

    public function testGetInvalidFilterQuery()
    {
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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

        $this->assertEquals(
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
            'matchoffset' => 15,
            'resultclass' => 'MyResultClass',
            'documentclass' => 'MyDocumentClass',
        );
        $query = new Query($config);

        $this->assertEquals($config['query'], $query->getQuery());
        $this->assertEquals($config['sort'], $query->getSorts());
        $this->assertEquals($config['fields'], $query->getFields());
        $this->assertEquals($config['rows'], $query->getRows());
        $this->assertEquals($config['start'], $query->getStart());
        $this->assertEquals($config['documentclass'], $query->getDocumentClass());
        $this->assertEquals($config['resultclass'], $query->getResultClass());
        $this->assertEquals($config['matchoffset'], $query->getMatchOffset());
        $this->assertEquals('published:true', $query->getFilterQuery('pub')->getQuery());
        $this->assertEquals('online:true', $query->getFilterQuery('online')->getQuery());

        $facets = $query->getFacetSet()->getFacets();
        $this->assertEquals(
            'category',
            $facets['categories']->getField()
        );
        $this->assertEquals(
            'category:13',
            $facets['category13']->getQuery()
        );

        $components = $query->getComponents();
        $this->assertEquals(1, count($components));
        $this->assertThat(
            array_pop($components),
            $this->isInstanceOf('Solarium\QueryType\Select\Query\Component\FacetSet')
        );
    }

    public function testSetAndGetComponents()
    {
        $mlt = new MoreLikeThis;
        $this->query->setComponent('mlt', $mlt);

        $this->assertEquals(
            array('mlt' => $mlt),
            $this->query->getComponents()
        );
    }

    public function testSetAndGetComponent()
    {
        $mlt = new MoreLikeThis;
        $this->query->setComponent('mlt', $mlt);

        $this->assertEquals(
            $mlt,
            $this->query->getComponent('mlt')
        );
    }

    public function testGetInvalidComponent()
    {
        $this->assertEquals(
            null,
            $this->query->getComponent('invalid')
        );
    }

    public function testGetInvalidComponentAutoload()
    {
        $this->setExpectedException('Solarium\Exception\OutOfBoundsException');
        $this->query->getComponent('invalid', true);
    }

    public function testRemoveComponent()
    {
        $mlt = new MoreLikeThis;
        $this->query->setComponent('mlt', $mlt);

        $this->assertEquals(
            array('mlt' => $mlt),
            $this->query->getComponents()
        );

        $this->query->removeComponent('mlt');

        $this->assertEquals(
            array(),
            $this->query->getComponents()
        );
    }

    public function testRemoveComponentWithObjectInput()
    {
        $mlt = new MoreLikeThis;
        $this->query->setComponent('mlt', $mlt);

        $this->assertEquals(
            array('mlt' => $mlt),
            $this->query->getComponents()
        );

        $this->query->removeComponent($mlt);

        $this->assertEquals(
            array(),
            $this->query->getComponents()
        );
    }

    public function testGetMoreLikeThis()
    {
        $mlt = $this->query->getMoreLikeThis();

        $this->assertEquals(
            'Solarium\QueryType\Select\Query\Component\MoreLikeThis',
            get_class($mlt)
        );
    }

    public function testGetDisMax()
    {
        $dismax = $this->query->getDisMax();

        $this->assertEquals(
            'Solarium\QueryType\Select\Query\Component\DisMax',
            get_class($dismax)
        );
    }

    public function testGetHighlighting()
    {
        $hlt = $this->query->getHighlighting();

        $this->assertEquals(
            'Solarium\QueryType\Select\Query\Component\Highlighting\Highlighting',
            get_class($hlt)
        );
    }

    public function testGetGrouping()
    {
        $grouping = $this->query->getGrouping();

        $this->assertEquals(
            'Solarium\QueryType\Select\Query\Component\Grouping',
            get_class($grouping)
        );
    }

    public function testRegisterComponentType()
    {
        $components = $this->query->getComponentTypes();
        $components['mykey'] = 'mycomponent';

        $this->query->registerComponentType('mykey', 'mycomponent', 'mybuilder', 'myparser');

        $this->assertEquals(
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
        $this->assertEquals(
            $options['optionB'],
            $fqOptions['optionB']
        );
    }

    public function testGetSpellcheck()
    {
        $spellcheck = $this->query->getSpellcheck();

        $this->assertEquals(
            'Solarium\QueryType\Select\Query\Component\Spellcheck',
            get_class($spellcheck)
        );
    }

    public function testGetDistributedSearch()
    {
        $spellcheck = $this->query->getDistributedSearch();

        $this->assertEquals(
            'Solarium\QueryType\Select\Query\Component\DistributedSearch',
            get_class($spellcheck)
        );
    }

    public function testGetStats()
    {
        $stats = $this->query->getStats();

        $this->assertEquals(
            'Solarium\QueryType\Select\Query\Component\Stats\Stats',
            get_class($stats)
        );
    }

    public function testGetDebug()
    {
        $stats = $this->query->getDebug();

        $this->assertEquals(
            'Solarium\QueryType\Select\Query\Component\Debug',
            get_class($stats)
        );
    }

    public function testSetAndGetMatchInclude()
    {
        $value = true;
        $this->query->setMatchInclude($value);

        $this->assertEquals(
            $value,
            $this->query->getMatchInclude()
        );
    }

    public function testSetAndGetMatchOffset()
    {
        $value = 20;
        $this->query->setMatchOffset($value);

        $this->assertEquals(
            $value,
            $this->query->getMatchOffset()
        );
    }

    public function testSetAndGetMltFields()
    {
        $value = 'name,description';
        $this->query->setMltFields($value);

        $this->assertEquals(
            array('name', 'description'),
            $this->query->getMltFields()
        );
    }

    public function testSetAndGetMltFieldsWithArray()
    {
        $value = array('name', 'description');
        $this->query->setMltFields($value);

        $this->assertEquals(
            $value,
            $this->query->getMltFields()
        );
    }

    public function testSetAndGetInterestingTerms()
    {
        $value = 'test';
        $this->query->setInterestingTerms($value);

        $this->assertEquals(
            $value,
            $this->query->getInterestingTerms()
        );
    }

    public function testSetAndGetQueryStream()
    {
        $value = true;
        $this->query->setQueryStream($value);

        $this->assertEquals(
            $value,
            $this->query->getQueryStream()
        );
    }

    public function testSetAndGetMinimumTermFrequency()
    {
        $value = 2;
        $this->query->setMinimumTermFrequency($value);

        $this->assertEquals(
            $value,
            $this->query->getMinimumTermFrequency()
        );
    }

    public function testMinimumDocumentFrequency()
    {
        $value = 4;
        $this->query->setMinimumDocumentFrequency($value);

        $this->assertEquals(
            $value,
            $this->query->getMinimumDocumentFrequency()
        );
    }

    public function testSetAndGetMinimumWordLength()
    {
        $value = 3;
        $this->query->setMinimumWordLength($value);

        $this->assertEquals(
            $value,
            $this->query->getMinimumWordLength()
        );
    }

    public function testSetAndGetMaximumWordLength()
    {
        $value = 15;
        $this->query->setMaximumWordLength($value);

        $this->assertEquals(
            $value,
            $this->query->getMaximumWordLength()
        );
    }

    public function testSetAndGetMaximumQueryTerms()
    {
        $value = 5;
        $this->query->setMaximumQueryTerms($value);

        $this->assertEquals(
            $value,
            $this->query->getMaximumQueryTerms()
        );
    }

    public function testSetAndGetMaximumNumberOfTokens()
    {
        $value = 5;
        $this->query->setMaximumNumberOfTokens($value);

        $this->assertEquals(
            $value,
            $this->query->getMaximumNumberOfTokens()
        );
    }

    public function testSetAndGetBoost()
    {
        $value = true;
        $this->query->setBoost($value);

        $this->assertEquals(
            $value,
            $this->query->getBoost()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'content,name';
        $this->query->setQueryFields($value);

        $this->assertEquals(
            array('content', 'name'),
            $this->query->getQueryFields()
        );
    }

    public function testSetAndGetQueryFieldsWithArray()
    {
        $value = array('content', 'name');
        $this->query->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->query->getQueryFields()
        );
    }
}
