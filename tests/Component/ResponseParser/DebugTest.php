<?php

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
        $this->assertEquals('dummy-qs', $result->getQueryString());
        $this->assertEquals('dummy-pq', $result->getParsedQuery());
        $this->assertEquals('dummy-qp', $result->getQueryParser());
        $this->assertEquals('dummy-oq', $result->getOtherQuery());

        $this->assertCount(1, $result->getExplain());
        $doc = $result->getExplain()->getDocument('MA147LL/A');
        $this->assertEquals(0.5, $doc->getValue());
        $this->assertTrue($doc->getMatch());
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
        $this->assertCount(1, $result->getExplainOther());
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
        $this->assertCount(2, $timing->getPhases());
        $phase = $timing->getPhase('process');
        $this->assertEquals(8, $phase->getTime());
        $this->assertCount(2, $phase->getTimings());
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

        $this->assertCount(0, $result->getExplain());
        $this->assertCount(0, $result->getExplainOther());
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse(null, null, array());
        $this->assertEquals(null, $result);
    }
}
