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

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Suggester;
use Solarium\QueryType\Select\Query\Query;

class SuggesterTest extends TestCase
{
    /**
     * @var Spellcheck
     */
    protected $suggester;

    public function setUp()
    {
        $this->suggester = new Suggester();
        $this->suggester->setQueryInstance(new Query);
    }

    public function testGetType()
    {
        $this->assertSame(ComponentAwareQueryInterface::COMPONENT_SUGGESTER, $this->suggester->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\Suggester',
            $this->suggester->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Suggester',
            $this->suggester->getRequestBuilder()
        );
    }

    public function testSetAndGetQuery()
    {
        $value = 'testquery';
        $this->suggester->setQuery($value);

        $this->assertSame(
            $value,
            $this->suggester->getQuery()
        );
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->suggester->setQuery('id:%1%', array(678));
        $this->assertSame('id:678', $this->suggester->getQuery());
    }

    public function testSetAndGetContextFilterQuery()
    {
        $value = 'context filter query';
        $this->suggester->setContextFilterQuery($value);

        $this->assertSame(
            $value,
            $this->suggester->getContextFilterQuery()
        );
    }

    public function testSetAndGetBuild()
    {
        $value = true;
        $this->suggester->setBuild($value);

        $this->assertSame(
            $value,
            $this->suggester->getBuild()
        );
    }

    public function testSetAndGetReload()
    {
        $value = false;
        $this->suggester->setReload($value);

        $this->assertSame(
            $value,
            $this->suggester->getReload()
        );
    }

    public function testSetAndGetDictionary()
    {
        $value = 'myDictionary';
        $this->suggester->setDictionary($value);

        $this->assertSame(
            $value,
            $this->suggester->getDictionary()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 11;
        $this->suggester->setCount($value);

        $this->assertSame(
            $value,
            $this->suggester->getCount()
        );
    }
}
