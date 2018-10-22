<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use Solarium\QueryType\ManagedResources\ResponseParser\Synonyms as ResponseParser;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;

class SynonymsTest extends TestCase
{
    public function testParse()
    {
        $data = '{ "responseHeader":{ "status":0, "QTime":3}, "synonymMappings":{ "initArgs":{ "ignoreCase":true, "format":"solr"}, "initializedOn":"2014-12-16T22:44:05.33Z", "managedMap":{ "GB": ["GiB", "Gigabyte"], "TV": ["Television"], "happy": ["glad", "joyful"]}}}';

        $response = new Response($data, ['HTTP 1.1 200 OK']);
        $result = new Synonyms(new SynonymsQuery(), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $this->assertSame('200 OK', $result->getResponse()->getStatusMessage());
        $this->assertSame(0, $parsed['status']);
        $this->assertSame(3, $parsed['queryTime']);
        $this->assertSame('2014-12-16T22:44:05.33Z', $parsed['items']->getInitializedOn());
        $this->assertSame(true, $parsed['items']->isIgnoreCase());
    }
}

