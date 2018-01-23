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

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\Debug as Parser;
use Solarium\Component\Result\Debug\Detail;

class DebugTest extends TestCase
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
        $this->assertSame('dummy-qs', $result->getQueryString());
        $this->assertSame('dummy-pq', $result->getParsedQuery());
        $this->assertSame('dummy-qp', $result->getQueryParser());
        $this->assertSame('dummy-oq', $result->getOtherQuery());

        $this->assertCount(1, $result->getExplain());
        $doc = $result->getExplain()->getDocument('MA147LL/A');
        $this->assertSame(0.5, $doc->getValue());
        $this->assertSame(true, $doc->getMatch());
        $this->assertSame('fieldWeight(text:ipod in 5), product of:', $doc->getDescription());

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
        $this->assertSame(array($expectedDetail), $doc->getDetails());
        $this->assertCount(1, $result->getExplainOther());
        $doc = $result->getExplainOther()->getDocument('IW-02');
        $this->assertSame(0.6, $doc->getValue());
        $this->assertSame(true, $doc->getMatch());
        $this->assertSame('fieldWeight(text:ipod in 6), product of:', $doc->getDescription());
        $this->assertSame(
            array(new Detail(true, 0.7, 'tf(termFreq(text:ipod)=1)')),
            $doc->getDetails()
        );

        $timing = $result->getTiming();
        $this->assertSame(36, $timing->getTime());
        $this->assertCount(2, $timing->getPhases());
        $phase = $timing->getPhase('process');
        $this->assertSame(8, $phase->getTime());
        $this->assertCount(2, $phase->getTimings());
        $this->assertSame(5, $phase->getTiming('org.apache.solr.handler.component.QueryComponent'));
        $this->assertSame(3, $phase->getTiming('org.apache.solr.handler.component.MoreLikeThisComponent'));
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
        $this->assertSame('dummy-qs', $result->getQueryString());
        $this->assertSame('dummy-pq', $result->getParsedQuery());
        $this->assertSame('dummy-qp', $result->getQueryParser());
        $this->assertSame('dummy-oq', $result->getOtherQuery());

        $this->assertCount(0, $result->getExplain());
        $this->assertCount(0, $result->getExplainOther());
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse(null, null, array());
        $this->assertSame(null, $result);
    }
}
