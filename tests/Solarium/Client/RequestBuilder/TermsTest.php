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

class Solarium_Client_RequestBuilder_TermsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Terms
     */
    protected $_query;

    /**
     * @var Solarium_Client_RequestBuilder_Terms
     */
    protected $_builder;

    public function setUp()
    {
        $this->_query = new Solarium_Query_Terms;
        $this->_builder = new Solarium_Client_RequestBuilder_Terms;
    }

    public function testBuildParams()
    {
        $this->_query->setFields('fieldA, fieldB');
        $this->_query->setLowerbound('d');
        $this->_query->setLowerboundInclude(true);
        $this->_query->setMinCount(3);
        $this->_query->setMaxCount(100);
        $this->_query->setPrefix('de');
        $this->_query->setRegex('det.*');
        $this->_query->setRegexFlags('case_insensitive,comments');
        $this->_query->setLimit(50);
        $this->_query->setUpperbound('x');
        $this->_query->setUpperboundInclude(false);
        $this->_query->setRaw(false);
        $this->_query->setSort('index');

        $request = $this->_builder->build($this->_query);

        $this->assertEquals(
            array(
                'terms' => 'true',
                'terms.fl' => array(
                    'fieldA',
                    'fieldB',
                ),
                'terms.limit' => 50,
                'terms.lower' => 'd',
                'terms.lower.incl' => 'true',
                'terms.maxcount' => 100,
                'terms.mincount' => 3,
                'terms.prefix' => 'de',
                'terms.raw' => 'false',
                'terms.regex' => 'det.*',
                'terms.regex.flag' => array(
                    'case_insensitive',
                    'comments',
                ),
                'terms.sort' => 'index',
                'terms.upper' => 'x',
                'terms.upper.incl' => 'false',
                'wt' => 'json',
            ),
            $request->getParams()
        );

        $this->assertEquals(
            Solarium_Client_Request::METHOD_GET,
            $request->getMethod()
        );
    }

}