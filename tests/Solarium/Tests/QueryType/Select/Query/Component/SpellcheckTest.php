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

namespace Solarium\Tests\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Component\Spellcheck;
use Solarium\QueryType\Select\Query\Query;

class SpellcheckTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Spellcheck
     */
    protected $spellCheck;

    public function setUp()
    {
        $this->spellCheck = new Spellcheck;
        $this->spellCheck->setQueryInstance(new Query);
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMPONENT_SPELLCHECK, $this->spellCheck->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\ResponseParser\Component\Spellcheck',
            $this->spellCheck->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder\Component\Spellcheck',
            $this->spellCheck->getRequestBuilder()
        );
    }

    public function testSetAndGetQuery()
    {
        $value = 'testquery';
        $this->spellCheck->setQuery($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getQuery()
        );
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->spellCheck->setQuery('id:%1%', array(678));
        $this->assertEquals('id:678', $this->spellCheck->getQuery());
    }

    public function testSetAndGetBuild()
    {
        $value = true;
        $this->spellCheck->setBuild($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getBuild()
        );
    }

    public function testSetAndGetReload()
    {
        $value = false;
        $this->spellCheck->setReload($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getReload()
        );
    }

    public function testSetAndGetDictionary()
    {
        $value = 'myDictionary';
        $this->spellCheck->setDictionary($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getDictionary()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 11;
        $this->spellCheck->setCount($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getCount()
        );
    }

    public function testSetAndGetOnlyMorePopular()
    {
        $value = false;
        $this->spellCheck->setOnlyMorePopular($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getOnlyMorePopular()
        );
    }

    public function testSetAndGetExtendedResults()
    {
        $value = false;
        $this->spellCheck->setExtendedResults($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getExtendedResults()
        );
    }

    public function testSetAndGetCollate()
    {
        $value = false;
        $this->spellCheck->setCollate($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getCollate()
        );
    }

    public function testSetAndGetMaxCollations()
    {
        $value = 23;
        $this->spellCheck->setMaxCollations($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getMaxCollations()
        );
    }

    public function testSetAndGetMaxCollationTries()
    {
        $value = 10;
        $this->spellCheck->setMaxCollationTries($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getMaxCollationTries()
        );
    }

    public function testSetAndGetMaxCollationEvaluations()
    {
        $value = 10;
        $this->spellCheck->setMaxCollationEvaluations($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getMaxCollationEvaluations()
        );
    }

    public function testSetAndGetCollateExtendedResults()
    {
        $value = true;
        $this->spellCheck->setCollateExtendedResults($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getCollateExtendedResults()
        );
    }

    public function testSetAndGetAccuracy()
    {
        $value = .1;
        $this->spellCheck->setAccuracy($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getAccuracy()
        );
    }

    public function testSetAndGetCollateParams()
    {
        $this->assertEquals(
            $this->spellCheck,
            $this->spellCheck->setCollateParam('mm', '100%')
        );

        $params = $this->spellCheck->getCollateParams();

        $this->assertArrayHasKey('mm', $params);
        $this->assertEquals('100%', $params['mm']);
    }
}
