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

namespace Solarium\Tests\QueryType\Suggester;

use Solarium\QueryType\Suggester\Query;
use Solarium\QueryType\Suggester\ResponseParser;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Term;

use PHPUnit\Framework\TestCase;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = array(
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            ),
            'suggest' => array(
                'dictionary1' => array(
                    'foo' => array(
                        'numFound' => 2,
                        'suggestions' => array(
                            array(
                                'term' => 'foo',
                            ),
                            array(
                                'term' => 'foobar',
                            ),
                        ),
                    ),
                    'zoo' => array(
                        'numFound' => 1,
                        'suggestions' => array(
                            array(
                                'term' => 'zoo keeper',
                            ),
                        ),
                    ),
                ),
                'dictionary2' => array(
                    'free' => array(
                        'numFound' => 2,
                        'suggestions' => array(
                            array(
                                'term' => 'free beer',
                            ),
                            array(
                                'term' => 'free software',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $query = new Query();

        $resultStub = $this->getMock('Solarium\QueryType\Suggester\Result\Result', array(), array(), '', false);
        $resultStub->expects($this->any())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->any())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new ResponseParser;
        $result = $parser->parse($resultStub);

        $expected = array(
            'dictionary1' => new Dictionary([
                'foo' => new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
                'zoo' => new Term(1, [['term' => 'zoo keeper']]),
            ]),
            'dictionary2' => new Dictionary([
                'free' => new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
            ]),
        );
        $allExpected = array(
            new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            new Term(1, [['term' => 'zoo keeper']]),
            new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
        );

        $this->assertEquals($expected, $result['results']);
        $this->assertEquals($allExpected, $result['all']);
    }
}
