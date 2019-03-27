<?php

namespace Solarium\Tests\Core\Client;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Endpoint;
use Solarium\Exception\UnexpectedValueException;

class EndpointTest extends TestCase
{
    /**
     * @var Endpoint
     */
    protected $endpoint;

    public function setUp()
    {
        $this->endpoint = new Endpoint();
    }

    public function testConfigMode()
    {
        $options = [
            'scheme' => 'http',
            'host' => '192.168.0.1',
            'port' => 123,
            'path' => '/mysolr/',
            'collection' => null,
            'core' => 'mycore',
            'timeout' => 3,
            'leader' => false,
            'username' => 'x',
            'password' => 'y',
        ];
        $this->endpoint->setOptions($options);

        $options['path'] = '/mysolr'; //expected trimming of trailing slash

        $this->assertSame($options, $this->endpoint->getOptions());
    }

    public function testSetAndGetHost()
    {
        $this->endpoint->setHost('myhost');
        $this->assertSame('myhost', $this->endpoint->getHost());
    }

    public function testSetAndGetPort()
    {
        $this->endpoint->setPort(8080);
        $this->assertSame(8080, $this->endpoint->getPort());
    }

    public function testSetAndGetPath()
    {
        $this->endpoint->setPath('/mysolr');
        $this->assertSame('/mysolr', $this->endpoint->getPath());
    }

    public function testSetAndGetPathWithTrailingSlash()
    {
        $this->endpoint->setPath('/mysolr/');
        $this->assertSame('/mysolr', $this->endpoint->getPath());
    }

    public function testSetAndGetCollection()
    {
        $this->endpoint->setCollection('collection1');
        $this->assertSame('collection1', $this->endpoint->getCollection());
    }

    public function testSetAndGetCore()
    {
        $this->endpoint->setCore('core1');
        $this->assertSame('core1', $this->endpoint->getCore());
    }

    public function testSetAndGetTimeout()
    {
        $this->endpoint->setTimeout(7);
        $this->assertSame(7, $this->endpoint->getTimeout());
    }

    public function testSetAndGetScheme()
    {
        $this->endpoint->setScheme('https');
        $this->assertSame('https', $this->endpoint->getScheme());
    }

    public function testGetCollectionBaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123);
        $this->expectException(UnexpectedValueException::class);
        $this->assertSame('http://myserver:123/mypath/solr/collection1/', $this->endpoint->getCollectionBaseUri());

        $this->endpoint->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/solr/collection1/', $this->endpoint->getCollectionBaseUri());
    }

    public function testGetCoreBaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123);
        $this->expectException(UnexpectedValueException::class);
        $this->assertSame('http://myserver:123/mypath/solr/core1/', $this->endpoint->getCoreBaseUri());

        $this->endpoint->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/solr/core1/', $this->endpoint->getCoreBaseUri());
    }

    public function testGetBaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123);
        $this->expectException(UnexpectedValueException::class);
        $this->assertSame('http://myserver:123/mypath/solr/core1/', $this->endpoint->getBaseUri());

        $this->endpoint->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/solr/core1/', $this->endpoint->getBaseUri());

        $this->endpoint->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/solr/collection1/', $this->endpoint->getBaseUri());
    }

    public function testGetV2BaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123);
        $this->assertSame('http://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());

        $this->endpoint->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());
    }

    public function testGetServerUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123);

        $this->assertSame('http://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testGetCoreBaseUriWithHttps()
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setPort(123)->setCore('core1');

        $this->assertSame('https://myserver:123/mypath/solr/core1/', $this->endpoint->getCoreBaseUri());
    }

    public function testGetServerUriWithHttps()
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setPort(123);

        $this->assertSame('https://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testServerUriDoesNotContainCollectionSegment()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123)->setCollection('mycollection');

        $this->assertSame('http://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testServerUriDoesNotContainCoreSegment()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123)->setCore('mycore');

        $this->assertSame('http://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testGetAndSetAuthentication()
    {
        $user = 'someone';
        $pass = 'S0M3p455';

        $this->endpoint->setAuthentication($user, $pass);

        $this->assertSame(
            [
                'username' => $user,
                'password' => $pass,
            ],
            $this->endpoint->getAuthentication()
        );
    }

    public function testToString()
    {
        $options = [
            'host' => '192.168.0.1',
            'port' => 123,
            'path' => '/mysolr/',
            'core' => 'mycore',
            'timeout' => 3,
            'username' => 'x',
            'password' => 'y',
        ];
        $this->endpoint->setOptions($options);

        $endpoint = <<<EOF
Solarium\Core\Client\Endpoint::__toString
base uri: http://192.168.0.1:123/mysolr/solr/mycore/
host: 192.168.0.1
port: 123
path: /mysolr
collection: 
core: mycore
timeout: 3
authentication: Array
(
    [username] => x
    [password] => y
)

EOF;

        $this->assertSame($endpoint, (string) $this->endpoint);
    }
}
