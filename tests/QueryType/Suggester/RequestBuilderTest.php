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

namespace Solarium\Tests\QueryType\Suggester;

use Solarium\QueryType\Suggester\Query;
use Solarium\QueryType\Suggester\RequestBuilder;
use Solarium\Core\Client\Request;

class RequestBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var RequestBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->query = new Query;
        $this->builder = new RequestBuilder;
    }

    public function testBuildParams()
    {
        $this->query->setCollate(true);
        $this->query->setCount(13);
        $this->query->setDictionary('suggest');
        $this->query->setQuery('ap ip');
        $this->query->setOnlyMorePopular(true);

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            array(
                'spellcheck' => 'true',
                'q' => 'ap ip',
                'spellcheck.dictionary' => 'suggest',
                'spellcheck.count' => 13,
                'spellcheck.onlyMorePopular' => 'true',
                'spellcheck.collate' => 'true',
                'wt' => 'json',
                'json.nl' => 'flat',
                'omitHeader' => 'true',
            ),
            $request->getParams()
        );

        $this->assertEquals(
            Request::METHOD_GET,
            $request->getMethod()
        );
    }
}
