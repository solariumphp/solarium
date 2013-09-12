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

namespace Solarium\Tests\QueryType\Terms;

use Solarium\QueryType\Terms\Query;
use Solarium\QueryType\Terms\ResponseParser;

class ResponseParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $data = array(
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            ),
            'terms' => array(
                'fieldA' => array(
                    'term1',
                    5,
                    'term2',
                    3
                ),
                'fieldB' => array(
                    'term3',
                    6,
                    'term4',
                    2
                )
            ),
        );

        $query = new Query();
        $query->setFields('fieldA,fieldB');

        $resultStub = $this->getMock('Solarium\QueryType\Terms\Result', array(), array(), '', false);
        $resultStub->expects($this->any())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->any())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new ResponseParser;
        $result = $parser->parse($resultStub);

        $expected = array(
            'fieldA' => array(
                'term1' => 5,
                'term2' => 3,
            ),
            'fieldB' => array(
                'term3' => 6,
                'term4' => 2,
            )
        );

        $this->assertEquals($expected, $result['results']);
        $this->assertEquals(2, count($result['results']));
    }

    public function testParseSolr14Format()
    {
        $data = array(
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            ),
            'terms' => array(
                'fieldA',
                 array(
                    'term1',
                    5,
                    'term2',
                    3
                ),
                'fieldB',
                array(
                    'term3',
                    6,
                    'term4',
                    2
                )
            ),
        );

        $query = new Query();
        $query->setFields('fieldA,fieldB');

        $resultStub = $this->getMock('Solarium\QueryType\Terms\Result', array(), array(), '', false);
        $resultStub->expects($this->any())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->any())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new ResponseParser;
        $result = $parser->parse($resultStub);

        $expected = array(
            'fieldA' => array(
                'term1' => 5,
                'term2' => 3,
            ),
            'fieldB' => array(
                'term3' => 6,
                'term4' => 2,
            )
        );

        $this->assertEquals($expected, $result['results']);
        $this->assertEquals(2, count($result['results']));
    }
}
