<?php

namespace Solarium\Tests\QueryType\MoreLikeThis;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\MoreLikeThis\Query;
use Solarium\QueryType\MoreLikeThis\Result;

class ResultTest extends TestCase
{
    public function testGetInterestingTerms(): void
    {
        $query = new Query();
        $query->setInterestingTerms('list');

        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);
        $this->assertEmpty($result->getInterestingTerms());
    }

    public function testGetInterestingTermsException(): void
    {
        $query = new Query();
        $query->setInterestingTerms('none');
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP/1.1 200 OK']);

        $result = new Result($query, $response);
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('interestingterms is none');
        $result->getInterestingTerms();
    }

    public function testGetMatch(): void
    {
        $query = new Query();
        $query->setMatchInclude(true);
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);
        $this->assertEmpty($result->getMatch());
    }

    public function testGetMatchException(): void
    {
        $query = new Query();
        $query->setMatchInclude(false);
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->expectException(UnexpectedValueException::class);
        $result->getMatch();
    }

    public function testGetQuery(): void
    {
        $query = new Query();
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', ['HTTP/1.1 200 OK']);
        $ping = new Result($query, $response);
        $this->assertSame($query, $ping->getQuery());
    }
}
