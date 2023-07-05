<?php

namespace Solarium\Tests\Core\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\Helper;
use Solarium\Exception\InvalidArgumentException;
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

    public function setUp(): void
    {
        $this->query = new SelectQuery();
        $this->helper = new Helper($this->query);
    }

    public function testRangeQuery()
    {
        $this->assertEquals(
            'field:[1 TO 2]',
            $this->helper->rangeQuery('field', 1, 2)
        );

        $this->assertEquals(
            'field:[1.5 TO 2.5]',
            $this->helper->rangeQuery('field', 1.5, 2.5)
        );

        $this->assertSame(
            'store:[45,-94 TO 46,-93]',
            $this->helper->rangeQuery('store', '45,-94', '46,-93')
        );

        $this->assertEquals(
            'field:["A" TO "M"]',
            $this->helper->rangeQuery('field', 'A', 'M')
        );
    }

    public function testRangeQueryInclusive()
    {
        $this->assertEquals(
            'field:[1 TO 2]',
            $this->helper->rangeQuery('field', 1, 2, true)
        );

        $this->assertEquals(
            'field:[1.5 TO 2.5]',
            $this->helper->rangeQuery('field', 1.5, 2.5, true)
        );

        $this->assertSame(
            'store:[45,-94 TO 46,-93]',
            $this->helper->rangeQuery('store', '45,-94', '46,-93', true)
        );

        $this->assertEquals(
            'field:["A" TO "M"]',
            $this->helper->rangeQuery('field', 'A', 'M', true)
        );
    }

    public function testRangeQueryExclusive()
    {
        $this->assertSame(
            'field:{1 TO 2}',
            $this->helper->rangeQuery('field', 1, 2, false)
        );

        $this->assertSame(
            'field:{1.5 TO 2.5}',
            $this->helper->rangeQuery('field', 1.5, 2.5, false)
        );

        $this->assertSame(
            'store:{45,-94 TO 46,-93}',
            $this->helper->rangeQuery('store', '45,-94', '46,-93', false)
        );

        $this->assertEquals(
            'field:{"A" TO "M"}',
            $this->helper->rangeQuery('field', 'A', 'M', false)
        );
    }

    public function testRangeQueryLeftInclusiveRightInclusive()
    {
        $this->assertEquals(
            'field:[1 TO 2]',
            $this->helper->rangeQuery('field', 1, 2, [true, true])
        );

        $this->assertEquals(
            'field:[1.5 TO 2.5]',
            $this->helper->rangeQuery('field', 1.5, 2.5, [true, true])
        );

        $this->assertSame(
            'store:[45,-94 TO 46,-93]',
            $this->helper->rangeQuery('store', '45,-94', '46,-93', [true, true])
        );

        $this->assertEquals(
            'field:["A" TO "M"]',
            $this->helper->rangeQuery('field', 'A', 'M', [true, true])
        );
    }

    public function testRangeQueryLeftInclusiveRightExclusive()
    {
        $this->assertEquals(
            'field:[1 TO 2}',
            $this->helper->rangeQuery('field', 1, 2, [true, false])
        );

        $this->assertEquals(
            'field:[1.5 TO 2.5}',
            $this->helper->rangeQuery('field', 1.5, 2.5, [true, false])
        );

        $this->assertSame(
            'store:[45,-94 TO 46,-93}',
            $this->helper->rangeQuery('store', '45,-94', '46,-93', [true, false])
        );

        $this->assertEquals(
            'field:["A" TO "M"}',
            $this->helper->rangeQuery('field', 'A', 'M', [true, false])
        );
    }

    public function testRangeQueryLeftExclusiveRightInclusive()
    {
        $this->assertEquals(
            'field:{1 TO 2]',
            $this->helper->rangeQuery('field', 1, 2, [false, true])
        );

        $this->assertEquals(
            'field:{1.5 TO 2.5]',
            $this->helper->rangeQuery('field', 1.5, 2.5, [false, true])
        );

        $this->assertSame(
            'store:{45,-94 TO 46,-93]',
            $this->helper->rangeQuery('store', '45,-94', '46,-93', [false, true])
        );

        $this->assertEquals(
            'field:{"A" TO "M"]',
            $this->helper->rangeQuery('field', 'A', 'M', [false, true])
        );
    }

    public function testRangeQueryLeftExclusiveRightExclusive()
    {
        $this->assertEquals(
            'field:{1 TO 2}',
            $this->helper->rangeQuery('field', 1, 2, [false, false])
        );

        $this->assertEquals(
            'field:{1.5 TO 2.5}',
            $this->helper->rangeQuery('field', 1.5, 2.5, [false, false])
        );

        $this->assertSame(
            'store:{45,-94 TO 46,-93}',
            $this->helper->rangeQuery('store', '45,-94', '46,-93', [false, false])
        );

        $this->assertEquals(
            'field:{"A" TO "M"}',
            $this->helper->rangeQuery('field', 'A', 'M', [false, false])
        );
    }

    public function testRangeQueryNullValues()
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

    public function testRangeQueryInclusiveNullValues()
    {
        $this->assertSame(
            'field:[1 TO *]',
            $this->helper->rangeQuery('field', 1, null, true)
        );

        $this->assertSame(
            'store:[* TO 46,-93]',
            $this->helper->rangeQuery('store', null, '46,-93', true)
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

    public function testRangeQueryLeftInclusiveRightInclusiveNullValues()
    {
        $this->assertSame(
            'field:[1 TO *]',
            $this->helper->rangeQuery('field', 1, null, [true, true])
        );

        $this->assertSame(
            'store:[* TO 46,-93]',
            $this->helper->rangeQuery('store', null, '46,-93', [true, true])
        );
    }

    public function testRangeQueryLeftInclusiveRightExclusiveNullValues()
    {
        $this->assertSame(
            'field:[1 TO *}',
            $this->helper->rangeQuery('field', 1, null, [true, false])
        );

        $this->assertSame(
            'store:[* TO 46,-93}',
            $this->helper->rangeQuery('store', null, '46,-93', [true, false])
        );
    }

    public function testRangeQueryLeftExclusiveRightInclusiveNullValues()
    {
        $this->assertSame(
            'field:{1 TO *]',
            $this->helper->rangeQuery('field', 1, null, [false, true])
        );

        $this->assertSame(
            'store:{* TO 46,-93]',
            $this->helper->rangeQuery('store', null, '46,-93', [false, true])
        );
    }

    public function testRangeQueryLeftExclusiveRightExclusiveNullValues()
    {
        $this->assertSame(
            'field:{1 TO *}',
            $this->helper->rangeQuery('field', 1, null, [false, false])
        );

        $this->assertSame(
            'store:{* TO 46,-93}',
            $this->helper->rangeQuery('store', null, '46,-93', [false, false])
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

        $this->assertEquals(
            ['sfield' => 'store', 'pt' => '45.15,-93.85', 'd' => 5],
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

        $this->assertEquals(
            ['sfield' => 'store', 'pt' => '45.15,-93.85', 'd' => 5],
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

        $this->assertEquals(
            ['sfield' => 'store', 'pt' => '45.15,-93.85'],
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
            '{!parser a=1 b=0 c=test d=tag1,tag2 e=true f=false}',
            $this->helper->qparser('parser', ['a' => 1, 'b' => 0, 'c' => 'test', 'd' => ['tag1', 'tag2'], 'e' => true, 'f' => false])
        );
    }

    public function testQparserDereferencedNoQuery()
    {
        $helper = new Helper();
        $this->expectException(InvalidArgumentException::class);
        $helper->qparser('join', ['from' => 'manu_id', 'to' => 'id'], true);
    }

    public function testQparserDereferenced()
    {
        $this->assertSame(
            '{!join from=$deref_1 to=$deref_2}',
            $this->helper->qparser('join', ['from' => 'manu_id', 'to' => 'id'], true, true)
        );

        $this->assertEquals(
            ['deref_1' => 'manu_id', 'deref_2' => 'id'],
            $this->query->getParams()
        );

        // second call, params should have updated counts
        $this->assertSame(
            '{!join from=$deref_3 to=$deref_4}',
            $this->helper->qparser('join', ['from' => 'cat_id', 'to' => 'prod_id'], true, true)
        );

        // previous params should also still be there
        $this->assertEquals(
            ['deref_1' => 'manu_id', 'deref_2' => 'id', 'deref_3' => 'cat_id', 'deref_4' => 'prod_id'],
            $this->query->getParams()
        );
    }

    public function testFunctionCallNoParams()
    {
        $this->assertSame('sum()', $this->helper->functionCall('sum'));
    }

    public function testFunctionCall()
    {
        $this->assertSame('sum(1,2)', $this->helper->functionCall('sum', [1, 2]));
    }

    /**
     * @dataProvider escapeTermProvider
     */
    public function testEscapeTerm(string $term, string $expected)
    {
        $this->assertSame(
            $expected,
            $this->helper->escapeTerm($term)
        );
    }

    /**
     * @see https://solr.apache.org/guide/the-standard-query-parser.html#escaping-special-characters
     */
    public function escapeTermProvider(): array
    {
        return [
            ' ' => ['a b', 'a\\ b'],
            '+' => ['a+b', 'a\\+b'],
            '-' => ['a-b', 'a\\-b'],
            '&&' => ['a&&b', 'a\\&&b'],
            '||' => ['a||b', 'a\\||b'],
            '!' => ['a!b', 'a\\!b'],
            '(' => ['a(b', 'a\\(b'],
            ')' => ['a)b', 'a\\)b'],
            '{' => ['a{b', 'a\\{b'],
            '}' => ['a}b', 'a\\}b'],
            '[' => ['a[b', 'a\\[b'],
            ']' => ['a]b', 'a\\]b'],
            '^' => ['a^b', 'a\\^b'],
            '"' => ['a"b', 'a\\"b'],
            '~' => ['a~b', 'a\\~b'],
            '*' => ['a*b', 'a\\*b'],
            '?' => ['a?b', 'a\\?b'],
            ':' => ['a:b', 'a\\:b'],
            '/' => ['a/b', 'a\\/b'],
            '\\' => ['a\b', 'a\\\b'],
            'and' => ['and', '"and"'],
            'AND' => ['AND', '"AND"'],
            'or' => ['or', '"or"'],
            'OR' => ['OR', '"OR"'],
            'to' => ['to', '"to"'],
            'TO' => ['TO', '"TO"'],
            ' AnD ' => [' AnD ', '" AnD "'],
            'AND or' => ['AND or', '"AND or"'],
            'Animals and plants' => ['Animals and plants', '"Animals and plants"'],
            'boring' => ['boring', 'boring'],
            'Band' => ['Band', 'Band'],
        ];
    }

    public function testEscapeTermNoEscape()
    {
        $this->assertSame(
            'abc',
            $this->helper->escapeTerm('abc')
        );
    }

    /**
     * @dataProvider escapePhraseProvider
     */
    public function testEscapePhrase(string $phrase, string $expected)
    {
        $this->assertSame(
            $expected,
            $this->helper->escapePhrase($phrase)
        );
    }

    public function escapePhraseProvider(): array
    {
        return [
            '"' => ['a+"b', '"a+\\"b"'],
            '\\' => ['a+\b', '"a+\\\b"'],
        ];
    }

    public function testEscapePhraseNoEscape()
    {
        $this->assertSame(
            '"a+b"',
            $this->helper->escapePhrase('a+b')
        );
    }

    /**
     * @dataProvider escapeLocalParamValueProvider
     */
    public function testEscapeLocalParamValue(string $value, string $expected)
    {
        $this->assertSame(
            $expected,
            $this->helper->escapeLocalParamValue($value)
        );
    }

    public function escapeLocalParamValueProvider(): array
    {
        return [
            'space' => ['a b', "'a b'"],
            "'" => ["a'b", "'a\\'b'"],
            '"' => ['a"b', "'a\"b'"],
            "\\'" => ["a\\'b", "'a\\\\\\'b'"],
            '\\"' => ['a\\"b', "'a\\\\\"b'"],
            '}' => ['ab}', "'ab}'"],
        ];
    }

    /**
     * @dataProvider escapeLocalParamValuePreEscapedSeparatorProvider
     */
    public function testEscapeLocalParamValuePreEscapedSeparator(string $value, string $separator, string $expectedWithoutSeparator, string $expectedWithSeparator)
    {
        $this->assertSame(
            $expectedWithoutSeparator,
            $this->helper->escapeLocalParamValue($value)
        );

        $this->assertSame(
            $expectedWithSeparator,
            $this->helper->escapeLocalParamValue($value, $separator)
        );
    }

    public function escapeLocalParamValuePreEscapedSeparatorProvider(): array
    {
        return [
            'no other escapes needed' => ['a\\,b', ',', 'a\\,b', 'a\\,b'],
            'other escapes needed' => ['a b\\,c', ',', "'a b\\\\,c'", "'a b\\,c'"],
            'unescaped separator left alone' => ['a b\\,c,d', ',', "'a b\\\\,c,d'", "'a b\\,c,d'"],
            'multiple escaped separators' => ['a b\\,c\\,d', ',', "'a b\\\\,c\\\\,d'", "'a b\\,c\\,d'"],
            'separator can be only 1 char' => ['a b\\,\\;c', ',;', "'a b\\\\,\\\\;c'", "'a b\\,\\\\;c'"],
            'separator is also regex syntax' => ['a b\\|c', '|', "'a b\\\\|c'", "'a b\\|c'"],
        ];
    }

    /**
     * @testWith ["ab"]
     *           ["a\\b"]
     *           ["{!ab"]
     */
    public function testEscapeLocalParamValueNoEscape(string $value)
    {
        $this->assertSame(
            $value,
            $this->helper->escapeLocalParamValue($value)
        );
    }

    public function testFormatDateInputTimestamp()
    {
        $this->assertFalse(
            $this->helper->formatDate(strtotime('2011---')),
            'Expects invalid strtotime/timestamp input (false) not to be accepted'
        );

        // allow negative dates.
        $this->assertNotFalse(
            $this->helper->formatDate(strtotime('2011-10-01')),
            'Expects negative timestamp input to be accepted'
        );

        // @todo find out if we need to any test for php versions / platforms which do not support negative timestamp

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
        date_default_timezone_set('UTC'); // prevent timezone differences

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
        date_default_timezone_set('UTC'); // prevent timezone differences

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
        $timestamp = time();
        // check if timezone is stripped
        $expected = strtoupper('Z');
        $actual = substr($this->helper->formatDate($timestamp), 19, 20);
        $this->assertSame($expected, $actual, 'Expects last charachter to be uppercased Z');

        $this->assertSame(
            $this->mockFormatDateOutput($timestamp),
            $this->helper->formatDate($timestamp)
        );
    }

    public function testFormatDateDoesntModifyPassedObject()
    {
        $timezone = new \DateTimeZone('+02:00');
        $date = new \DateTime('2013-01-15 14:41:58', $timezone);

        $this->assertEquals('2013-01-15T12:41:58Z', $this->helper->formatDate($date));
        $this->assertEquals('2013-01-15T14:41:58+02:00', $date->format(\DateTimeInterface::ATOM));
    }

    public function testAssemble()
    {
        // test single basic placeholder
        $this->assertSame(
            'id:456 AND cat:2',
            $this->helper->assemble('id:%1% AND cat:2', [456])
        );

        // test multiple basic placeholders and placeholder repeat
        $this->assertSame(
            '(id:456 AND cat:2) OR (id:456 AND cat:1)',
            $this->helper->assemble('(id:%1% AND cat:%2%) OR (id:%1% AND cat:%3%)', [456, 2, 1])
        );

        // test literal placeholder (same as basic)
        $this->assertSame(
            'id:456 AND cat:2',
            $this->helper->assemble('id:%L1% AND cat:2', [456])
        );

        // test term placeholder
        $this->assertSame(
            'cat:2 AND content:a\\+b',
            $this->helper->assemble('cat:2 AND content:%T1%', ['a+b'])
        );

        // test term placeholder case-insensitive
        $this->assertSame(
            'cat:2 AND content:a\\+b',
            $this->helper->assemble('cat:2 AND content:%t1%', ['a+b'])
        );

        // test phrase placeholder
        $this->assertSame(
            'cat:2 AND content:"a+\\"b"',
            $this->helper->assemble('cat:2 AND content:%P1%', ['a+"b'])
        );
    }

    public function testAssembleInvalidPartNumber()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->helper->assemble('cat:%1% AND content:%2%', ['value1']);
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

        $this->assertEquals(
            ['deref_1' => 'manu_id', 'deref_2' => 'id'],
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

    /**
     * @deprecated Will be removed in Solarium 6
     */
    public function testCacheControlWithCost()
    {
        $this->assertSame(
            '{!cache=false cost=6}',
            $this->helper->cacheControl(false, 6)
        );
    }

    /**
     * @deprecated Will be removed in Solarium 6
     */
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

    public function testEscapeXMLCharacterData()
    {
        $this->assertSame(
            '&lt;&amp;&gt;',
            $this->helper->escapeXMLCharacterData('<&>')
        );
    }

    protected function mockFormatDateOutput($timestamp): string
    {
        $date = new \DateTime('@'.$timestamp);

        return strstr($date->format(\DateTime::ISO8601), '+', true).'Z';
    }
}
