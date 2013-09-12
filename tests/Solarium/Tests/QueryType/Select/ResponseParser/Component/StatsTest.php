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

use Solarium\QueryType\Select\ResponseParser\Component\Stats as Parser;

class StatsTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function testParse()
    {
        $data = array(
            'stats' => array(
                'stats_fields' => array(
                    'fieldA' => array(
                        'min' => 3,
                    ),
                    'fieldB' => array(
                        'min' => 4,
                        'facets' => array(
                            'fieldC' => array(
                                'value1' => array(
                                    'min' => 5,
                                )
                            )
                        )
                    )
                )
            )
        );

        $result = $this->parser->parse(null, null, $data);

        $this->assertEquals(3, $result->getResult('fieldA')->getMin());
        $this->assertEquals(4, $result->getResult('fieldB')->getMin());

        $facets = $result->getResult('fieldB')->getFacets();
        $this->assertEquals(5, $facets['fieldC']['value1']->getMin());
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse(null, null, array());
        $this->assertEquals(0, count($result));
    }
}
