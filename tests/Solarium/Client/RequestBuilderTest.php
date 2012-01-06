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

class Solarium_Client_RequestBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TestRequestBuilder
     */
    protected $_builder;

    public function setup()
    {
        $this->_builder = new TestRequestBuilder;
    }

    public function testBuild()
    {
        $query = new Solarium_Query_Select;
        $query->addParam('p1','v1');
        $query->addParam('p2','v2');
        $request = $this->_builder->build($query);

        $this->assertEquals(
            'select?p1=v1&p2=v2&wt=json',
             urldecode($request->getUri())
        );
    }

    public function testRenderLocalParams()
    {
        $myParams = array('tag' => 'mytag', 'ex' => array('exclude1','exclude2'));

        $this->assertEquals(
            '{!tag=mytag ex=exclude1,exclude2}myValue',
            $this->_builder->renderLocalParams('myValue', $myParams)
        );
    }

    public function testRenderLocalParamsWithoutParams()
    {
        $this->assertEquals(
            'myValue',
            $this->_builder->renderLocalParams('myValue')
        );
    }

}

class TestRequestBuilder extends Solarium_Client_RequestBuilder{

}