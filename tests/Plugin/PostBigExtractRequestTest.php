<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\PostCreateRequest as PostCreateRequest;
use Solarium\Plugin\PostBigExtractRequest;
use Solarium\Tests\Integration\TestClientFactory;

class PostBigExtractRequestTest extends TestCase
{
    /**
     * @var PostBigExtractRequest
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Solarium\QueryType\Extract\Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->plugin = new PostBigExtractRequest();

        $this->client = TestClientFactory::createWithCurlAdapter();
        $this->query = $this->client->createExtract();
    }

    public function testSetAndGetMaxQueryStringLength()
    {
        $this->plugin->setMaxQueryStringLength(512);
        $this->assertSame(512, $this->plugin->getMaxQueryStringLength());
    }

    public function testPostCreateRequest()
    {
        $document = $this->query->createDocument();
        // create some literals
        $literalsAsList = [];
        for ($i = 1; $i <= 3; ++$i) {
            $field_name = "field_{$i}";
            $document->$field_name = "The number of the literal is #$i.";
            $literalsAsList[] = $document->$field_name;
        }
        $document['literalsAsList'] = $literalsAsList;
        $this->query->setDocument($document);
        $this->query->setFile(__FILE__);

        $requestOutput = $this->client->createRequest($this->query);
        $requestInput = clone $requestOutput;
        $event = new PostCreateRequest($this->query, $requestOutput);
        $this->plugin->setMaxQueryStringLength(1)->postCreateRequest($event);

        $this->assertSame(Request::METHOD_POST, $requestOutput->getMethod());
        $nlPattern = '(?:\\r\\n|\\r|\\n)';
        foreach (explode('&', urldecode($requestInput->getQueryString())) as $qs_parameter) {
            $qsParameterArr = explode('=', $qs_parameter);
            $qsParameterName = $qsParameterArr[0];
            $qsParameterValue = $qsParameterArr[1];
            $pattern = "/^-{2}.*?$nlPattern+^Content-Disposition: form-data; name=\"".preg_quote($qsParameterName)."\"$nlPattern+Content-Type:.*?$nlPattern+".preg_quote($qsParameterValue).'/m';
            $this->assertMatchesRegularExpression($pattern, $requestOutput->getRawData());
        }
        $this->assertSame('', $requestOutput->getQueryString());
    }

    public function testPostCreateRequestUnalteredSmallRequest()
    {
        $this->query->setFile(__FILE__);
        $requestOutput = $this->client->createRequest($this->query);
        $requestInput = clone $requestOutput;
        $event = new PostCreateRequest($this->query, $requestOutput);
        $this->plugin->postCreateRequest($event);

        $this->assertEquals($requestInput, $requestOutput);
    }

    public function testPostCreateRequestUnalteredPostRequest()
    {
        $query = $this->client->createUpdate();
        $query->addDeleteById(1);

        $requestOutput = $this->client->createRequest($query);
        $requestInput = clone $requestOutput;
        $event = new PostCreateRequest($query, $requestOutput);
        $this->plugin->postCreateRequest($event);

        $this->assertEquals($requestInput, $requestOutput);
    }

    public function testPluginIntegration()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $client->registerPlugin('testplugin', $this->plugin);
        $this->plugin->setMaxQueryStringLength(1); // this forces literals moved from query string for even the smallest queries

        $query = $this->client->createExtract();
        $query->setCommit(true);
        $document = $query->createDocument();
        $document['field'] = 'Ipse dixit.';
        $query->setDocument($document);
        $query->setFile(__FILE__);
        $query->setOmitHeader(false);

        $request = $client->createRequest($query);
        $adapter = $this->createMock(AdapterInterface::class);
        $client->setAdapter($adapter);
        $response = $client->executeRequest($request);

        // default extract method is POST
        $this->assertSame(Request::METHOD_POST, $request->getMethod());
    }
}
