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

namespace Solarium\Tests\QueryType\Select\ResponseParser\Component;

use Solarium\QueryType\Select\ResponseParser\Component\Debug as Parser;
use Solarium\QueryType\Select\Result\Debug\Detail;

class DebugTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function testParse()
    {
        $data = array(
            'debug' => array(
                'querystring' => 'dummy-qs',
                'parsedquery' => 'dummy-pq',
                'QParser' => 'dummy-qp',
                'otherQuery' => 'dummy-oq',
                'explain' => array(
                    'MA147LL/A' => array(
                        'match' => true,
                        'value' => 0.5,
                        'description' => 'fieldWeight(text:ipod in 5), product of:',
                        'details' => array(
                            array(
                                'match' => true,
                                'value' => 0.5,
                                'description' => 'sum of:',
                                'details' => array(
                                    array(
                                        'match' => true,
                                        'value' => 0.25,
                                        'description' => 'weight(dummyfield:flachdach^250.0 in 1311) [], result of:'
                                    ),
                                    array(
                                        'match' => true,
                                        'value' => 0.25,
                                        'description' => 'tf(termFreq(text:ipod)=1)',
                                    )
                                )
                            )
                        ),
                    ),
                ),
                'explainOther' => array(
                    'IW-02' => array(
                        'match' => true,
                        'value' => 0.6,
                        'description' => 'fieldWeight(text:ipod in 6), product of:',
                        'details' => array(
                            array(
                                'match' => true,
                                'value' => 0.7,
                                'description' => 'tf(termFreq(text:ipod)=1)',
                            )
                        ),
                    ),
                ),
                'timing' => array(
                    'time' => 36,
                    'prepare' => array(
                        'time' => 12,
                        'org.apache.solr.handler.component.QueryComponent' => array(
                            'time' => 1,
                        ),
                        'org.apache.solr.handler.component.FacetComponent' => array(
                            'time' => 11,
                        ),
                    ),
                    'process' => array(
                        'time' => 8,
                        'org.apache.solr.handler.component.QueryComponent' => array(
                            'time' => 5,
                        ),
                        'org.apache.solr.handler.component.MoreLikeThisComponent' => array(
                            'time' => 3,
                        ),
                    )
                )
            )
        );

        $result = $this->parser->parse(null, null, $data);
        $this->assertEquals('dummy-qs', $result->getQueryString());
        $this->assertEquals('dummy-pq', $result->getParsedQuery());
        $this->assertEquals('dummy-qp', $result->getQueryParser());
        $this->assertEquals('dummy-oq', $result->getOtherQuery());

        $this->assertEquals(1, count($result->getExplain()));
        $doc = $result->getExplain()->getDocument('MA147LL/A');
        $this->assertEquals(0.5, $doc->getValue());
        $this->assertEquals(true, $doc->getMatch());
        $this->assertEquals('fieldWeight(text:ipod in 5), product of:', $doc->getDescription());

        $expectedDetail = new Detail(true, 0.5, 'sum of:');
        $expectedDetail->setSubDetails(
            array(
                array(
                    'match' => true,
                    'value' => 0.25,
                    'description' => 'weight(dummyfield:flachdach^250.0 in 1311) [], result of:'
                ),
                array(
                    'match' => true,
                    'value' => 0.25,
                    'description' => 'tf(termFreq(text:ipod)=1)',
                )
            )
        );
        $this->assertEquals(array($expectedDetail), $doc->getDetails());
        $this->assertEquals(1, count($result->getExplainOther()));
        $doc = $result->getExplainOther()->getDocument('IW-02');
        $this->assertEquals(0.6, $doc->getValue());
        $this->assertEquals(true, $doc->getMatch());
        $this->assertEquals('fieldWeight(text:ipod in 6), product of:', $doc->getDescription());
        $this->assertEquals(
            array(new Detail(true, 0.7, 'tf(termFreq(text:ipod)=1)')),
            $doc->getDetails()
        );

        $timing = $result->getTiming();
        $this->assertEquals(36, $timing->getTime());
        $this->assertEquals(2, count($timing->getPhases()));
        $phase = $timing->getPhase('process');
        $this->assertEquals(8, $phase->getTime());
        $this->assertEquals(2, count($phase->getTimings()));
        $this->assertEquals(5, $phase->getTiming('org.apache.solr.handler.component.QueryComponent'));
        $this->assertEquals(3, $phase->getTiming('org.apache.solr.handler.component.MoreLikeThisComponent'));
    }

    public function testParseNoExplainData()
    {
        $data = array(
            'debug' => array(
                'querystring' => 'dummy-qs',
                'parsedquery' => 'dummy-pq',
                'QParser' => 'dummy-qp',
                'otherQuery' => 'dummy-oq',
            )
        );

        $result = $this->parser->parse(null, null, $data);
        $this->assertEquals('dummy-qs', $result->getQueryString());
        $this->assertEquals('dummy-pq', $result->getParsedQuery());
        $this->assertEquals('dummy-qp', $result->getQueryParser());
        $this->assertEquals('dummy-oq', $result->getOtherQuery());

        $this->assertEquals(0, count($result->getExplain()));
        $this->assertEquals(0, count($result->getExplainOther()));
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse(null, null, array());
        $this->assertEquals(null, $result);
    }
}
