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

namespace Solarium\Tests\QueryType\MoreLikeThis;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Response;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\MoreLikeThis\Query;
use Solarium\QueryType\MoreLikeThis\Result;

class ResultTest extends TestCase
{
    public function testGetInterestingTerms()
    {
        $query = new Query();
        $query->setInterestingTerms('list');

        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', array('HTTP 1.1 200 OK'));
        $result = new Result($query, $response);
        $this->assertEmpty($result->getInterestingTerms());
    }

    public function testGetInterestingTermsException()
    {
        $query = new Query();
        $query->setInterestingTerms('none');
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', array('HTTP 1.1 200 OK'));

        $result = new Result($query, $response);
        $this->expectException(UnexpectedValueException::class);
        $result->getInterestingTerms();
    }

    public function testGetMatch()
    {
        $query = new Query();
        $query->setMatchInclude(true);
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', array('HTTP 1.1 200 OK'));
        $result = new Result($query, $response);
        $this->assertEmpty($result->getMatch());
    }

    public function testGetMatchException()
    {
        $query = new Query();
        $query->setMatchInclude(false);
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', array('HTTP 1.1 200 OK'));
        $result = new Result($query, $response);

        $this->expectException(UnexpectedValueException::class);
        $result->getMatch();
    }

    public function testGetQuery()
    {
        $query = new Query;
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', array('HTTP 1.1 200 OK'));
        $ping = new Result($query, $response);
        $this->assertSame($query, $ping->getQuery());
    }
}
