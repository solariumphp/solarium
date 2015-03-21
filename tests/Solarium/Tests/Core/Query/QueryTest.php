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

namespace Solarium\Tests\Core\Query;

use Solarium\Core\Query\AbstractQuery;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetHandler()
    {
        $query = new TestQuery;
        $query->setHandler('myhandler');
        $this->assertEquals('myhandler', $query->getHandler());
    }

    public function testSetAndGetResultClass()
    {
        $query = new TestQuery;
        $query->setResultClass('myResultClass');
        $this->assertEquals('myResultClass', $query->getResultClass());
    }

    public function testGetHelper()
    {
        $query = new TestQuery;
        $helper = $query->getHelper();

        $this->assertEquals(
            'Solarium\Core\Query\Helper',
            get_class($helper)
        );
    }

    public function testAddAndGetParams()
    {
        $query = new TestQuery;
        $query->addParam('p1', 'v1');
        $query->addParam('p2', 'v2');
        $query->addParam('p2', 'v3'); //should overwrite previous value

        $this->assertEquals(
            array('p1' => 'v1', 'p2' => 'v3'),
            $query->getParams()
        );
    }

    public function testGetDefaultResponseWriter()
    {
        $query = new TestQuery;
        $this->assertEquals('json', $query->getResponseWriter());
    }

    public function testSetAndGetResponseWriter()
    {
        $query = new TestQuery;
        $query->setResponseWriter('phps');
        $this->assertEquals('phps', $query->getResponseWriter());
    }

    public function testGetDefaultTimeAllowed()
    {
        $query = new TestQuery;
        $this->assertEquals(null, $query->getTimeAllowed());
    }

    public function testSetAndGetTimeAllowed()
    {
        $query = new TestQuery;
        $query->setTimeAllowed(1200);
        $this->assertEquals(1200, $query->getTimeAllowed());
    }
}

class TestQuery extends AbstractQuery
{
    public function getType()
    {
        return 'testType';
    }

    public function getRequestBuilder()
    {
        return null;
    }

    public function getResponseParser()
    {
        return null;
    }
}
