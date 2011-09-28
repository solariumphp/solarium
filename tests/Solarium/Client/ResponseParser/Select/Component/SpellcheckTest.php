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

class Solarium_Client_ResponseParser_Select_Component_SpellcheckTest extends PHPUnit_Framework_TestCase
{

    protected $_parser;

    public function setUp()
    {
        $this->_parser = new Solarium_Client_ResponseParser_Select_Component_Spellcheck();
    }

    public function testParseExtended()
    {
        $data = array(
            'spellcheck' => array(
                'suggestions' => array(
                    0 => 'delll',
                    1 => array (
                        'numFound' => 1,
                        'startOffset' => 0,
                        'endOffset' => 5,
                        'origFreq' => 0,
                        'suggestion' => array (
                            0 => array (
                                'word' => 'dell', 'freq' => 1
                            ),
                        ),
                    ),
                    2 => 'ultrashar',
                    3 => array (
                        'numFound' => 1,
                        'startOffset' => 6,
                        'endOffset' => 15,
                        'origFreq' => 0,
                        'suggestion' => array (
                            0 => array (
                                'word' => 'ultrasharp',
                                'freq' => 1
                            ),
                        ),
                    ),
                    4 => 'correctlySpelled',
                    5 => false,
                    6 => 'collation',
                    7 => array (
                        0 => 'collationQuery',
                        1 => 'dell ultrasharp',
                        2 => 'hits',
                        3 => 0,
                        4 => 'misspellingsAndCorrections',
                        5 => array (
                            0 => 'delll',
                            1 => 'dell',
                            2 => 'ultrashar',
                            3 => 'ultrasharp'
                        ),
                    ),
                )
            )
        );

        $result = $this->_parser->parse(null, null, $data);

        $suggestions = $result->getSuggestions();
        $this->assertEquals(false, $result->getCorrectlySpelled());
        $this->assertEquals('dell', $suggestions[0]->getWord());
        $this->assertEquals('dell ultrasharp', $result->getCollation()->getQuery());
    }

    public function testParse()
    {
        $data = array(
            'spellcheck' => array(
                'suggestions' => array(
                    0 => 'delll',
                    1 => array (
                        'numFound' => 1,
                        'startOffset' => 0,
                        'endOffset' => 5,
                        'origFreq' => 0,
                        'suggestion' => array (
                            0 => 'dell',
                        ),
                    ),
                    2 => 'ultrashar',
                    3 => array (
                        'numFound' => 1,
                        'startOffset' => 6,
                        'endOffset' => 15,
                        'origFreq' => 0,
                        'suggestion' => array (
                            0 => array (
                                'word' => 'ultrasharp',
                                'freq' => 1
                            ),
                        ),
                    ),
                    4 => 'correctlySpelled',
                    5 => false,
                    6 => 'collation',
                    7 => 'dell ultrasharp',
                )
            )
        );

        $result = $this->_parser->parse(null, null, $data);

        $suggestions = $result->getSuggestions();
        $this->assertEquals(false, $result->getCorrectlySpelled());
        $this->assertEquals('dell', $suggestions[0]->getWord());
        $this->assertEquals('dell ultrasharp', $result->getCollation()->getQuery());
    }

    public function testParseNoData()
    {
        $result = $this->_parser->parse(null, null, array());

        $this->assertEquals(null, $result);
    }

}
