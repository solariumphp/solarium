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

namespace Solarium\Tests\Query;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    protected $_helper;

    public function setUp()
    {
        $this->_helper = new \Solarium\Query\Helper;
    }

    public function testRangeQueryInclusive()
    {
        $this->assertEquals(
            'field:[1 TO 2]',
            $this->_helper->rangeQuery('field',1,2)
        );

        $this->assertEquals(
            'store:[45,-94 TO 46,-93]',
            $this->_helper->rangeQuery('store', '45,-94', '46,-93')
        );
    }

    public function testRangeQueryExclusive()
    {
        $this->assertEquals(
            'field:{1 TO 2}',
            $this->_helper->rangeQuery('field',1,2, false)
        );

        $this->assertEquals(
            'store:{45,-94 TO 46,-93}',
            $this->_helper->rangeQuery('store', '45,-94', '46,-93', false)
        );
    }

    public function testGeofilt()
    {
        $this->assertEquals(
            '{!geofilt pt=45.15,-93.85 sfield=store d=5}',
            $this->_helper->geofilt(45.15, -93.85, 'store', 5)
        );
    }

    public function testBbox()
    {
        $this->assertEquals(
            '{!bbox pt=45.15,-93.85 sfield=store d=5}',
            $this->_helper->bbox(45.15, -93.85, 'store', 5)
        );
    }

    public function testGeodist()
    {
        $this->assertEquals(
            'geodist(45.15,-93.85,store)',
            $this->_helper->geodist(45.15, -93.85, 'store')
        );
    }

    public function testQparserNoParams()
    {
        $this->assertEquals(
            '{!parser}',
            $this->_helper->qparser('parser')
        );
    }

    public function testQparser()
    {
        $this->assertEquals(
            '{!parser a=1 b=test}',
            $this->_helper->qparser('parser', array('a' => 1, 'b' => 'test'))
        );
    }

    public function testFunctionCallNoParams()
    {
        $this->assertEquals(
            'sum()',
            $this->_helper->functionCall('sum')
        );
    }

    public function testFunctionCall()
    {
        $this->assertEquals(
            'sum(1,2)',
            $this->_helper->functionCall('sum', array(1,2))
        );
    }

    public function testEscapeTerm()
    {
        $this->assertEquals(
            'a\\+b',
            $this->_helper->escapeTerm('a+b')
        );
    }

    public function testEscapeTermNoEscape()
    {
        $this->assertEquals(
            'abc',
            $this->_helper->escapeTerm('abc')
        );
    }

    public function testEscapePhrase()
    {
        $this->assertEquals(
            '"a+\\"b"',
            $this->_helper->escapePhrase('a+"b')
        );
    }

    public function testEscapePhraseNoEscape()
    {
        $this->assertEquals(
            '"a+b"',
            $this->_helper->escapePhrase('a+b')
        );
    }

    public function testAssemble()
    {
        // test single basic placeholder
        $this->assertEquals(
            'id:456 AND cat:2',
            $this->_helper->assemble('id:%1% AND cat:2',array(456))
        );

        // test multiple basic placeholders and placeholder repeat
        $this->assertEquals(
            '(id:456 AND cat:2) OR (id:456 AND cat:1)',
            $this->_helper->assemble('(id:%1% AND cat:%2%) OR (id:%1% AND cat:%3%)',array(456, 2, 1))
        );

        // test literal placeholder (same as basic)
        $this->assertEquals(
            'id:456 AND cat:2',
            $this->_helper->assemble('id:%L1% AND cat:2',array(456))
        );

        // test term placeholder
        $this->assertEquals(
            'cat:2 AND content:a\\+b',
            $this->_helper->assemble('cat:2 AND content:%T1%',array('a+b'))
        );

        // test term placeholder case-insensitive
        $this->assertEquals(
            'cat:2 AND content:a\\+b',
            $this->_helper->assemble('cat:2 AND content:%t1%',array('a+b'))
        );

        // test phrase placeholder
        $this->assertEquals(
            'cat:2 AND content:"a+\\"b"',
            $this->_helper->assemble('cat:2 AND content:%P1%',array('a+"b'))
        );
    }

    public function testAssembleInvalidPartNumber()
    {
        $this->setExpectedException('Solarium\Exception');
        $this->_helper->assemble('cat:%1% AND content:%2%',array('value1'));
    }

}
