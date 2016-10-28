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

use Solarium\QueryType\Select\ResponseParser\Component\Spellcheck as Parser;
use Solarium\QueryType\Select\Query\Query;

class SpellcheckTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;
    protected $query;

    public function setUp()
    {
        $this->query = new Query();
        $this->parser = new Parser();
    }

    /**
     * @dataProvider providerParseExtended
     */
    public function testParseExtended($data)
    {
        $result = $this->parser->parse($this->query, null, $data);

        $suggestions = $result->getSuggestions();
        $this->assertEquals(false, $result->getCorrectlySpelled());
        $this->assertEquals('dell', $suggestions[0]->getWord());
        $this->assertEquals('ultrasharp', $suggestions[1]->getWord());
        $this->assertEquals(6, $suggestions[1]->getStartOffset());
        $this->assertEquals('ultrasharp', $suggestions[2]->getWord());
        $this->assertEquals(16, $suggestions[2]->getStartOffset());
        $this->assertEquals('dell ultrasharp', $result->getCollation()->getQuery());
        $collations = $result->getCollations();
        $this->assertEquals('dell ultrasharp', $collations[0]->getQuery());
        $this->assertEquals('dell ultrasharp new', $collations[1]->getQuery());
    }

    public function providerParseExtended()
    {
        return array(
            'solr4' => array(
                'data' => array(
                    'spellcheck' => array(
                        'suggestions' => array(
                            'delll',
                            array(
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'dell',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                            'ultrashar',
                            array(
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                            'ultrashar',
                            array(
                                'numFound' => 1,
                                'startOffset' => 16,
                                'endOffset' => 25,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                            'correctlySpelled',
                            false,
                            'collation',
                            array(
                                0 => 'collationQuery',
                                1 => 'dell ultrasharp',
                                2 => 'hits',
                                3 => 0,
                                4 => 'misspellingsAndCorrections',
                                5 => array(
                                    0 => 'delll',
                                    1 => 'dell',
                                    2 => 'ultrashar',
                                    3 => 'ultrasharp',
                                ),
                            ),
                            'collation',
                            array(
                                0 => 'collationQuery',
                                1 => 'dell ultrasharp new',
                                2 => 'hits',
                                3 => 0,
                                4 => 'misspellingsAndCorrections',
                                5 => array(
                                    0 => 'delll',
                                    1 => 'dell',
                                    2 => 'ultrashar',
                                    3 => 'ultrasharp',
                                ),
                            ),
                        )
                    )
                )
            ),
            'solr5' => array(
                'data' => array(
                    'spellcheck' => array(
                        'suggestions' => array(
                            'delll',
                            array(
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'dell',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                            'ultrashar',
                            array(
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                            'ultrashar',
                            array(
                                'numFound' => 1,
                                'startOffset' => 16,
                                'endOffset' => 25,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                        ),
                        'correctlySpelled',
                        false,
                        'collations' => array(
                            'collation',
                            array(
                                'collationQuery' => 'dell ultrasharp',
                                'hits' => 0,
                                'misspellingsAndCorrections' => array(
                                    0 => 'delll',
                                    1 => 'dell',
                                    2 => 'ultrashar',
                                    3 => 'ultrasharp',
                                ),
                            ),
                            'collation',
                            array(
                                'collationQuery' => 'dell ultrasharp new',
                                'hits' => 0,
                                'misspellingsAndCorrections' => array(
                                    0 => 'delll',
                                    1 => 'dell',
                                    2 => 'ultrashar',
                                    3 => 'ultrasharp',
                                ),
                            ),
                        )
                    )
                )
            )
        );
    }

    /**
     * @dataProvider providerParse
     */
    public function testParse($data)
    {
        $result = $this->parser->parse($this->query, null, $data);

        $suggestions = $result->getSuggestions();
        $this->assertEquals(false, $result->getCorrectlySpelled());
        $this->assertEquals('dell', $suggestions[0]->getWord());
        $this->assertEquals('dell ultrasharp', $result->getCollation()->getQuery());
        $collations = $result->getCollations();
        $this->assertEquals('dell ultrasharp', $collations[0]->getQuery());
        $this->assertEquals('dell ultrasharp new', $collations[1]->getQuery());

    }

    public function providerParse()
    {
        return array(
            'solr4' => array(
                'data' => array(
                    'spellcheck' => array(
                        'suggestions' => array(
                            0 => 'delll',
                            1 => array(
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => 'dell',
                                ),
                            ),
                            2 => 'ultrashar',
                            3 => array(
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                            4 => 'correctlySpelled',
                            5 => false,
                            6 => 'collation',
                            7 => 'dell ultrasharp',
                            8 => 'collation',
                            9 => 'dell ultrasharp new',
                        )
                    )
                )
            ),
            'solr5' => array(
                'data' => array(
                    'spellcheck' => array(
                        'suggestions' => array(
                            0 => 'delll',
                            1 => array(
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => 'dell',
                                ),
                            ),
                            2 => 'ultrashar',
                            3 => array(
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                        ),
                        'correctlySpelled',
                        false,
                        'collations' => array(
                            'collation',
                            'dell ultrasharp',
                            'collation',
                            'dell ultrasharp new',
                        )
                    )
                )
            )
        );
    }

    /**
     * @dataProvider providerParseSingleCollation
     */
    public function testParseSingleCollation($data)
    {
        $result = $this->parser->parse($this->query, null, $data);
        $collations = $result->getCollations();
        $this->assertEquals('dell ultrasharp', $collations[0]->getQuery());

        $words = $result->getSuggestion(1)->getWords();
        $this->assertEquals(array('word' => 'ultrasharpy', 'freq' => 1), $words[1]);
    }

    public function providerParseSingleCollation()
    {
        return array(
            'solr4' => array(
                'data' => array(
                    'spellcheck' => array(
                        'suggestions' => array(
                            0 => 'delll',
                            1 => array(
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => 'dell',
                                ),
                            ),
                            2 => 'ultrashar',
                            3 => array(
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'ultrasharp',
                                        'freq' => 2,
                                    ),
                                    1 => array(
                                        'word' => 'ultrasharpy',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                            4 => 'correctlySpelled',
                            5 => false,
                            6 => 'collation',
                            7 => 'dell ultrasharp',
                        )
                    )
                )
            ),
            'solr5' => array(
                'data' => array(
                    'spellcheck' => array(
                        'suggestions' => array(
                            0 => 'delll',
                            1 => array(
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => 'dell',
                                ),
                            ),
                            2 => 'ultrashar',
                            3 => array(
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => array(
                                    0 => array(
                                        'word' => 'ultrasharp',
                                        'freq' => 2,
                                    ),
                                    1 => array(
                                        'word' => 'ultrasharpy',
                                        'freq' => 1,
                                    ),
                                ),
                            ),
                        ),
                        'correctlySpelled',
                        false,
                        'collations' => array(
                            'collation',
                            'dell ultrasharp'
                        )
                    )
                )
            )
        );
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse($this->query, null, array());

        $this->assertEquals(null, $result);
    }
}
