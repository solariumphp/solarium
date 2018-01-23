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

namespace Solarium\Tests\Core\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\Helper;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

class HelperTest extends TestCase
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var SelectQuery
     */
    protected $query;

    public function setUp()
    {
        $this->query = new SelectQuery;
        $this->helper = new Helper($this->query);
    }

    public function testRangeQueryInclusive()
    {
        $this->assertSame(
            'field:[1 TO 2]',
            $this->helper->rangeQuery('field', 1, 2)
        );

        $this->assertSame(
            'store:[45,-94 TO 46,-93]',
            $this->helper->rangeQuery('store', '45,-94', '46,-93')
        );
    }

    public function testRangeQueryExclusive()
    {
        $this->assertSame(
            'field:{1 TO 2}',
            $this->helper->rangeQuery('field', 1, 2, false)
        );

        $this->assertSame(
            'store:{45,-94 TO 46,-93}',
            $this->helper->rangeQuery('store', '45,-94', '46,-93', false)
        );
    }

    public function testRangeQueryInclusiveNullValues()
    {
        $this->assertSame(
            'field:[1 TO *]',
            $this->helper->rangeQuery('field', 1, null)
        );

        $this->assertSame(
            'store:[* TO 46,-93]',
            $this->helper->rangeQuery('store', null, '46,-93')
        );
    }

    public function testRangeQueryExclusiveNullValues()
    {
        $this->assertSame(
            'field:{1 TO *}',
            $this->helper->rangeQuery('field', 1, null, false)
        );

        $this->assertSame(
            'store:{* TO 46,-93}',
            $this->helper->rangeQuery('store', null, '46,-93', false)
        );
    }

    public function testGeofilt()
    {
        $this->assertSame(
            '{!geofilt pt=45.15,-93.85 sfield=store d=5}',
            $this->helper->geofilt('store', 45.15, -93.85, 5)
        );
    }

    public function testGeofiltDereferenced()
    {
        $this->assertSame(
            '{!geofilt}',
            $this->helper->geofilt('store', 45.15, -93.85, 5, true)
        );

        $this->assertSame(
            array('sfield' => 'store', 'pt' => '45.15,-93.85', 'd' => 5),
            $this->query->getParams()
        );
    }

    public function testBbox()
    {
        $this->assertSame(
            '{!bbox pt=45.15,-93.85 sfield=store d=5}',
            $this->helper->bbox('store', 45.15, -93.85, 5)
        );
    }

    public function testBboxDereferenced()
    {
        $this->assertSame(
            '{!bbox}',
            $this->helper->bbox('store', 45.15, -93.85, 5, true)
        );

        $this->assertSame(
            array('sfield' => 'store', 'pt' => '45.15,-93.85', 'd' => 5),
            $this->query->getParams()
        );
    }

    public function testGeodist()
    {
        $this->assertSame(
            'geodist(store,45.15,-93.85)',
            $this->helper->geodist('store', 45.15, -93.85)
        );
    }

    public function testGeodistDereferenced()
    {
        $this->assertSame(
            'geodist()',
            $this->helper->geodist('store', 45.15, -93.85, true)
        );

        $this->assertSame(
            array('sfield' => 'store', 'pt' => '45.15,-93.85'),
            $this->query->getParams()
        );
    }

    public function testQparserNoParams()
    {
        $this->assertSame(
            '{!parser}',
            $this->helper->qparser('parser')
        );
    }

    public function testQparser()
    {
        $this->assertSame(
            '{!parser a=1 b=test}',
            $this->helper->qparser('parser', array('a' => 1, 'b' => 'test'))
        );
    }

    public function testQparserDereferencedNoQuery()
    {
        $helper = new Helper();
        $this->expectException('Solarium\Exception\InvalidArgumentException');
        $helper->qparser('join', array('from' => 'manu_id', 'to' => 'id'), true);
    }

    public function testQparserDereferenced()
    {
        $this->assertSame(
            '{!join from=$deref_1 to=$deref_2}',
            $this->helper->qparser('join', array('from' => 'manu_id', 'to' => 'id'), true, true)
        );

        $this->assertSame(
            array('deref_1' => 'manu_id', 'deref_2' => 'id'),
            $this->query->getParams()
        );

        // second call, params should have updated counts
        $this->assertSame(
            '{!join from=$deref_3 to=$deref_4}',
            $this->helper->qparser('join', array('from' => 'cat_id', 'to' => 'prod_id'), true, true)
        );

        // previous params should also still be there
        $this->assertSame(
            array('deref_1' => 'manu_id', 'deref_2' => 'id', 'deref_3' => 'cat_id', 'deref_4' => 'prod_id'),
            $this->query->getParams()
        );
    }

    public function testFunctionCallNoParams()
    {
        $this->assertSame(
            'sum()',
            $this->helper->functionCall('sum')
        );
    }

    public function testFunctionCall()
    {
        $this->assertSame(
            'sum(1,2)',
            $this->helper->functionCall('sum', array(1, 2))
        );
    }

    public function testEscapeTerm()
    {
        $this->assertSame(
            'a\\+b\/c',
            $this->helper->escapeTerm('a+b/c')
        );
    }

    public function testEscapeTermNoEscape()
    {
        $this->assertSame(
            'abc',
            $this->helper->escapeTerm('abc')
        );
    }

    public function testEscapePhrase()
    {
        $this->assertSame(
            '"a+\\"b"',
            $this->helper->escapePhrase('a+"b')
        );
    }

    public function testEscapePhraseNoEscape()
    {
        $this->assertSame(
            '"a+b"',
            $this->helper->escapePhrase('a+b')
        );
    }

    public function testFormatDateInputTimestamp()
    {
        $this->assertFalse(
            $this->helper->formatDate(strtotime('2011---')),
            'Expects invalid strtotime/timestamp input (false) not to be accepted'
        );

        //allow negative dates.
        $this->assertNotSame(
            false,
            $this->helper->formatDate(strtotime('2011-10-01')),
            'Expects negative timestamp input to be accepted'
        );

        //@todo find out if we need to any test for php versions / platforms which do not support negative timestamp

        $this->assertFalse(
            $this->helper->formatDate(strtotime('2010-31-02')),
            'Expects invalid timestamp input (not in calendar) not to be accepted'
        );

        $this->assertSame(
            $this->mockFormatDateOutput(strtotime('2011-10-01')),
            $this->helper->formatDate(strtotime('2011-10-01')),
            'Expects formatDate with Timstamp input to output ISO8601 with stripped timezone'
        );
    }

    public function testFormatDateInputString()
    {
        $this->assertFalse(
            $this->helper->formatDate('2011-13-31'),
            'Expects an invalid date string input not to be accepted'
        );

        $this->assertSame(
            $this->mockFormatDateOutput(strtotime('2011-10-01')),
            $this->helper->formatDate('2011-10-01'),
            'Expects formatDate with String input to output ISO8601 with stripped timezone'
        );
    }

    public function testFormatDateInputDateTime()
    {
        date_default_timezone_set("UTC"); // prevent timezone differences

        $this->assertFalse(
            $this->helper->formatDate(new \stdClass()),
            'Expect any other object not to be accepted'
        );

        $this->assertSame(
            $this->mockFormatDateOutput(strtotime('2011-10-01')),
            $this->helper->formatDate(new \DateTime('2011-10-01')),
            'Expects formatDate with DateTime input to output ISO8601 with stripped timezone'
        );
    }

    public function testFormatDateInputDateTimeImmutable()
    {
        date_default_timezone_set("UTC"); // prevent timezone differences

        $this->assertFalse(
            $this->helper->formatDate(new \stdClass()),
            'Expect any other object not to be accepted'
        );

        $this->assertSame(
            $this->mockFormatDateOutput(strtotime('2011-10-01')),
            $this->helper->formatDate(new \DateTimeImmutable('2011-10-01')),
            'Expects formatDate with DateTimeImmutable input to output ISO8601 with stripped timezone'
        );
    }

    public function testFormatDate()
    {
        //check if timezone is stripped
        $expected = strtoupper('Z');
        $actual = substr($this->helper->formatDate(time()), 19, 20);
        $this->assertSame($expected, $actual, 'Expects last charachter to be uppercased Z');

        $this->assertSame(
            $this->mockFormatDateOutput(time()),
            $this->helper->formatDate(time())
        );
    }

    public function testAssemble()
    {
        // test single basic placeholder
        $this->assertSame(
            'id:456 AND cat:2',
            $this->helper->assemble('id:%1% AND cat:2', array(456))
        );

        // test multiple basic placeholders and placeholder repeat
        $this->assertSame(
            '(id:456 AND cat:2) OR (id:456 AND cat:1)',
            $this->helper->assemble('(id:%1% AND cat:%2%) OR (id:%1% AND cat:%3%)', array(456, 2, 1))
        );

        // test literal placeholder (same as basic)
        $this->assertSame(
            'id:456 AND cat:2',
            $this->helper->assemble('id:%L1% AND cat:2', array(456))
        );

        // test term placeholder
        $this->assertSame(
            'cat:2 AND content:a\\+b',
            $this->helper->assemble('cat:2 AND content:%T1%', array('a+b'))
        );

        // test term placeholder case-insensitive
        $this->assertSame(
            'cat:2 AND content:a\\+b',
            $this->helper->assemble('cat:2 AND content:%t1%', array('a+b'))
        );

        // test phrase placeholder
        $this->assertSame(
            'cat:2 AND content:"a+\\"b"',
            $this->helper->assemble('cat:2 AND content:%P1%', array('a+"b'))
        );
    }

    public function testAssembleInvalidPartNumber()
    {
        $this->expectException('Solarium\Exception\InvalidArgumentException');
        $this->helper->assemble('cat:%1% AND content:%2%', array('value1'));
    }

    public function testJoin()
    {
        $this->assertSame(
            '{!join from=manu_id to=id}',
            $this->helper->join('manu_id', 'id')
        );
    }

    public function testJoinDereferenced()
    {
        $this->assertSame(
            '{!join from=$deref_1 to=$deref_2}',
            $this->helper->join('manu_id', 'id', true)
        );

        $this->assertSame(
            array('deref_1' => 'manu_id', 'deref_2' => 'id'),
            $this->query->getParams()
        );
    }

    public function testQparserTerm()
    {
        $this->assertSame(
            '{!term f=weight}1.5',
            $this->helper->qparserTerm('weight', 1.5)
        );
    }

    public function testCacheControlWithCost()
    {
        $this->assertSame(
            '{!cache=false cost=6}',
            $this->helper->cacheControl(false, 6)
        );
    }

    public function testCacheControlWithoutCost()
    {
        $this->assertSame(
            '{!cache=true}',
            $this->helper->cacheControl(true)
        );
    }

    public function testFilterControlCharacters()
    {
        $this->assertSame(
            'my string',
            $this->helper->filterControlCharacters("my\x08string")
        );
    }

    protected function mockFormatDateOutput($timestamp)
    {
        $date = new \DateTime('@'.$timestamp);

        return strstr($date->format(\DateTime::ISO8601), '+', true) . 'Z';
    }
}
