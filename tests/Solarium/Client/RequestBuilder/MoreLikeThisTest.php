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

class Solarium_Client_RequestBuilder_MoreLikeThisTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_MoreLikeThis
     */
    protected $_query;

    /**
     * @var Solarium_Client_RequestBuilder_MoreLikeThis
     */
    protected $_builder;

    public function setUp()
    {
        $this->_query = new Solarium_Query_MoreLikeThis;
        $this->_builder = new Solarium_Client_RequestBuilder_MoreLikeThis;
    }

    public function testBuildParams()
    {
        $this->_query->setInterestingTerms('test');
        $this->_query->setMatchInclude(true);
        $this->_query->setStart(12);
        $this->_query->setMltFields('description,name');
        $this->_query->setMinimumTermFrequency(1);
        $this->_query->setMinimumDocumentFrequency(3);
        $this->_query->setMinimumWordLength(2);
        $this->_query->setMaximumWordLength(15);
        $this->_query->setMaximumQueryTerms(4);
        $this->_query->setMaximumNumberOfTokens(5);
        $this->_query->setBoost(true);
        $this->_query->setQueryFields('description');

        $request = $this->_builder->build($this->_query);

        $this->assertEquals(
            array(
                'mlt.interestingTerms' => 'test',
                'mlt.match.include' => 'true',
                'mlt.match.offset' => 12,
                'mlt.fl' => 'description,name',
                'mlt.mintf' => 1,
                'mlt.mindf' => 3,
                'mlt.minwl' => 2,
                'mlt.maxwl' => 15,
                'mlt.maxqt' => 4,
                'mlt.maxntp' => 5,
                'mlt.boost' => 'true',
                'mlt.qf' => 'description',
                'q' => '*:*',
                'fl' => '*,score',
                'rows' => 10,
                'start' => 12,
                'wt' => 'json'
            ),
            $request->getParams()
        );

        $this->assertEquals(
            Solarium_Client_Request::METHOD_GET,
            $request->getMethod()
        );
    }

    public function testBuildWithQueryStream()
    {
        $content = 'test content';

        $this->_query->setQuery($content);
        $this->_query->setQueryStream(true);

        $request = $this->_builder->build($this->_query);

        $this->assertEquals(Solarium_Client_Request::METHOD_POST,$request->getMethod());
        $this->assertEquals(null,$request->getParam('q'));
        $this->assertEquals($content,$request->getRawData());
        $this->assertTrue(in_array('Content-Type: text/plain; charset=utf-8', $request->getHeaders()));
    }
}