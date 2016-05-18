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

namespace Solarium\Tests\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Component\BoostQuery;
use Solarium\QueryType\Select\Query\Component\DisMax;
use Solarium\QueryType\Select\Query\Query;

class DisMaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DisMax
     */
    protected $disMax;

    public function setUp()
    {
        $this->disMax = new DisMax;
    }

    public function testConfigMode()
    {
        $options = array(
            'queryparser' => 'edismax',
            'queryalternative' => '*:*',
            'queryfields' => 'title^2.0 description',
            'minimummatch' => '2.0',
            'phrasefields' => 'title^2.0 description^3.5',
            'phraseslop' => 2,
            'queryphraseslop' => 4,
            'tie' => 2.1,
            'boostquery' => 'cat:1^3',
            'boostfunctions' => 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2',
        );

        $this->disMax->setOptions($options);

        $this->assertEquals($options['queryparser'], $this->disMax->getQueryParser());
        $this->assertEquals($options['queryalternative'], $this->disMax->getQueryAlternative());
        $this->assertEquals($options['queryfields'], $this->disMax->getQueryFields());
        $this->assertEquals($options['minimummatch'], $this->disMax->getMinimumMatch());
        $this->assertEquals($options['phrasefields'], $this->disMax->getPhraseFields());
        $this->assertEquals($options['phraseslop'], $this->disMax->getPhraseSlop());
        $this->assertEquals($options['queryphraseslop'], $this->disMax->getQueryPhraseSlop());
        $this->assertEquals($options['tie'], $this->disMax->getTie());
        $this->assertEquals($options['boostquery'], $this->disMax->getBoostQuery());
        $this->assertEquals($options['boostfunctions'], $this->disMax->getBoostFunctions());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Query::COMPONENT_DISMAX,
            $this->disMax->getType()
        );
    }

    public function testGetResponseParser()
    {
        $this->assertEquals(null, $this->disMax->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder\Component\Dismax',
            $this->disMax->getRequestBuilder()
        );
    }

    public function testSetAndGetQueryParser()
    {
        $value = 'dummyparser';
        $this->disMax->setQueryParser($value);

        $this->assertEquals(
            $value,
            $this->disMax->getQueryParser()
        );
    }

    public function testSetAndGetQueryAlternative()
    {
        $value = '*:*';
        $this->disMax->setQueryAlternative($value);

        $this->assertEquals(
            $value,
            $this->disMax->getQueryAlternative()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'title^2.0 description';
        $this->disMax->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->disMax->getQueryFields()
        );
    }

    public function testSetAndGetMinimumMatch()
    {
        $value = '2.0';
        $this->disMax->setMinimumMatch($value);

        $this->assertEquals(
            $value,
            $this->disMax->getMinimumMatch()
        );
    }

    public function testSetAndGetPhraseFields()
    {
        $value = 'title^2.0 description^3.5';
        $this->disMax->setPhraseFields($value);

        $this->assertEquals(
            $value,
            $this->disMax->getPhraseFields()
        );
    }

    public function testSetAndGetPhraseSlop()
    {
        $value = '2';
        $this->disMax->setPhraseSlop($value);

        $this->assertEquals(
            $value,
            $this->disMax->getPhraseSlop()
        );
    }

    public function testSetAndGetQueryPhraseSlop()
    {
        $value = '3';
        $this->disMax->setQueryPhraseSlop($value);

        $this->assertEquals(
            $value,
            $this->disMax->getQueryPhraseSlop()
        );
    }

    public function testSetAndGetTie()
    {
        $value = 2.1;
        $this->disMax->setTie($value);

        $this->assertEquals(
            $value,
            $this->disMax->getTie()
        );
    }

    public function testSetAndGetBoostQuery()
    {
        $value = 'cat:1^3';
        $this->disMax->setBoostQuery($value);

        $this->assertEquals(
            $value,
            $this->disMax->getBoostQuery()
        );
    }

    public function testAddBoostQueryWithArray()
    {
        $query = 'cat:1^3';
        $key = 'cat';

        $this->disMax->addBoostQuery(array('query' => $query, 'key' => $key));

        $this->assertEquals(
            $query,
            $this->disMax->getBoostQuery($key)
        );
    }

    public function testAddBoostQueryWithObject()
    {
        $query = 'cat:1^3';
        $key = 'cat';

        $bq = new BoostQuery();
        $bq -> setKey($key);
        $bq -> setQuery($query);

        $this->disMax->addBoostQuery($bq);

        $this->assertEquals(
            $query,
            $this->disMax->getBoostQuery($key)
        );
    }

    public function testAddBoostQueryWithoutKey()
    {
        $bq = new BoostQuery;
        $bq->setQuery('category:1');

        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->disMax->addBoostQuery($bq);
    }

    public function testAddBoostQueryWithUsedKey()
    {
        $bq1 = new BoostQuery;
        $bq1->setKey('bq1')->setQuery('category:1');

        $bq2 = new BoostQuery;
        $bq2->setKey('bq1')->setQuery('category:2');

        $this->disMax->addBoostQuery($bq1);
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->disMax->addBoostQuery($bq2);
    }

    public function testAddBoostQueriesWithInnerKeys()
    {
        $bqs = array(
            array('key' => 'key1', 'query' => 'cat:1'),
            array('key' => 'key2', 'query' => 'cat:2')
        );

        $this->disMax->addBoostQueries($bqs);

        $bqs2 = array();

        foreach ($bqs as $bq) {
            $bqs2[$bq['key']] = new BoostQuery($bq);
        }

        $this->assertEquals(
            $bqs2,
            $this->disMax->getBoostQueries()
        );
    }

    public function testAddBoostQueriesWithOuterKeys()
    {
        $bqs = array(
            'key1' => array('query' => 'cat:1'),
            'key2' => array('query' => 'cat:2')
        );

        $this->disMax->addBoostQueries($bqs);

        $bqs2 = array();

        foreach ($bqs as $key => $bq) {
            $bq['key'] = $key;
            $bqs2[$key] = new BoostQuery($bq);
        }

        $this->assertEquals(
            $bqs2,
            $this->disMax->getBoostQueries()
        );
    }

    public function testSetAndGetBoostFunctions()
    {
        $value = 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2';
        $this->disMax->setBoostFunctions($value);

        $this->assertEquals(
            $value,
            $this->disMax->getBoostFunctions()
        );
    }
}
