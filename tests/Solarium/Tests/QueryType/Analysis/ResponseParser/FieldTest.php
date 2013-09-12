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

namespace Solarium\Tests\QueryType\Analysis\ResponseParser;

use Solarium\QueryType\Analysis\ResponseParser\Field as FieldParser;
use Solarium\QueryType\Analysis\Query\Field as Query;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $data = array(
            'analysis' => array(
                'doc1' => array(
                    'field1' => array(
                        'type1' => array(
                            array(
                                'org.apache.solr.analysis.PatternReplaceCharFilter',
                                'string value',
                                'analysisClass',
                                array(
                                    array(
                                        'text' => 'test',
                                        'start' => 1,
                                        'end' => 23,
                                        'position' => 4,
                                        'positionHistory' => 'test',
                                        'type' => 'test',
                                    ),
                                    array(
                                        'text' => 'test2',
                                        'start' => 1,
                                        'end' => 23,
                                        'position' => 4,
                                        'positionHistory' => 'test',
                                        'type' => 'test',
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 5,
            )
        );

        $resultStub = $this->getMock('Solarium\Core\Query\Result\Result', array(), array(), '', false);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->once())
                     ->method('getQuery')
                     ->will($this->returnValue(new Query));

        $parser = new FieldParser();
        $result = $parser->parse($resultStub);

        $docs = $result['items'][0]->getItems();
        $fields = $docs[0]->getItems();
        $types = $fields[0]->getItems();
        $class1items = $types[0]->getItems();
        $class2items = $types[1]->getItems();

        $this->assertEquals('string value', $class1items[0]->getText());
        $this->assertEquals('test2', $class2items[1]->getText());
    }

    public function testParseNoData()
    {
        $data = array(
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 5,
            )
        );

        $resultStub = $this->getMock('Solarium\Core\Query\Result\Result', array(), array(), '', false);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));

        $parser = new FieldParser();
        $result = $parser->parse($resultStub);

        $this->assertEquals(
            array(
                'status' => 1,
                'queryTime' => 5,
                'items' => array()
            ),
            $result
        );
    }
}
