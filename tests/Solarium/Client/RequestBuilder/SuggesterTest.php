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

class Solarium_Client_RequestBuilder_SuggesterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Suggester
     */
    protected $_query;

    /**
     * @var Solarium_Client_RequestBuilder_Suggester
     */
    protected $_builder;

    public function setUp()
    {
        $this->_query = new Solarium_Query_Suggester;
        $this->_builder = new Solarium_Client_RequestBuilder_Suggester;
    }

    public function testBuildParams()
    {
        $this->_query->setCollate(true);
        $this->_query->setCount(13);
        $this->_query->setDictionary('suggest');
        $this->_query->setQuery('ap ip');
        $this->_query->setOnlyMorePopular(true);

        $request = $this->_builder->build($this->_query);

        $this->assertEquals(
            array(
                'spellcheck' => 'true',
                'q' => 'ap ip',
                'spellcheck.dictionary' => 'suggest',
                'spellcheck.count' => 13,
                'spellcheck.onlyMorePopular' => 'true',
                'spellcheck.collate' => 'true',
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