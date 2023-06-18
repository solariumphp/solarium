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

    public function setUp(): void
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
            'context' => '/lunr/',
            'collection' => null,
            'core' => 'mycore',
            'leader' => false,
            'username' => 'x',
            'password' => 'y',
            'tokenname' => 'a',
            'token' => 'b',
        ];
        $this->endpoint->setOptions($options);

        $options['path'] = '/mysolr'; // expected trimming of trailing slash
        $options['context'] = 'lunr'; // expected trimming of leading and trailing slash

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

    /**
     * @testWith ["lunr"]
     *           ["lunr/"]
     *           ["/lunr"]
     *           ["/lunr/"]
     */
    public function testSetAndGetContext(string $context)
    {
        $this->endpoint->setContext($context);
        $this->assertSame('lunr', $this->endpoint->getContext());
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

    public function testSetAndGetScheme()
    {
        $this->endpoint->setScheme('https');
        $this->assertSame('https', $this->endpoint->getScheme());
    }

    public function testGetCollectionBaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/lunr/collection1/', $this->endpoint->getCollectionBaseUri());
    }

    public function testGetCollectionBaseUriWithHttps()
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCollection('collection1');

        $this->assertSame('https://myserver:123/mypath/lunr/collection1/', $this->endpoint->getCollectionBaseUri());
    }

    public function testGetCollectionBaseUriException()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->expectException(UnexpectedValueException::class);
        $this->endpoint->getCollectionBaseUri();
    }

    public function testGetCoreBaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/lunr/core1/', $this->endpoint->getCoreBaseUri());
    }

    public function testGetCoreBaseUriWithHttps()
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCore('core1');

        $this->assertSame('https://myserver:123/mypath/lunr/core1/', $this->endpoint->getCoreBaseUri());
    }

    public function testGetCoreBaseUriException()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->expectException(UnexpectedValueException::class);
        $this->endpoint->getCoreBaseUri();
    }

    public function testGetBaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);

        $this->endpoint->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/lunr/core1/', $this->endpoint->getBaseUri());

        $this->endpoint->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/lunr/collection1/', $this->endpoint->getBaseUri());
    }

    public function testGetBaseUriWithHttps()
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);

        $this->endpoint->setCore('core1');
        $this->assertSame('https://myserver:123/mypath/lunr/core1/', $this->endpoint->getBaseUri());

        $this->endpoint->setCollection('collection1');
        $this->assertSame('https://myserver:123/mypath/lunr/collection1/', $this->endpoint->getBaseUri());
    }

    public function testGetBaseUriException()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->expectException(UnexpectedValueException::class);
        $this->endpoint->getBaseUri();
    }

    public function testGetV1BaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->assertSame('http://myserver:123/mypath/lunr/', $this->endpoint->getV1BaseUri());
    }

    public function testGetV1BaseUriWithHttps()
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->assertSame('https://myserver:123/mypath/lunr/', $this->endpoint->getV1BaseUri());
    }

    public function testV1BaseUriDoesNotContainCollectionSegment()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/lunr/', $this->endpoint->getV1BaseUri());
    }

    public function testV1BaseUriDoesNotContainCoreSegment()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/lunr/', $this->endpoint->getV1BaseUri());
    }

    public function testGetV2BaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->assertSame('http://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());
    }

    public function testGetV2BaseUriWithHttps()
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->assertSame('https://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());
    }

    public function testV2BaseUriDoesNotContainCollectionSegment()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());
    }

    public function testV2BaseUriDoesNotContainCoreSegment()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());
    }

    public function testGetServerUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);

        $this->assertSame('http://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testGetServerUriWithHttps()
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setPort(123);

        $this->assertSame('https://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testServerUriDoesNotContainContextSegment()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123)->setContext('lunr');

        $this->assertSame('http://myserver:123/mypath/', $this->endpoint->getServerUri());
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

    public function testSetAuthenticationSensitiveParameter()
    {
        // #[\SensitiveParameter] was introduced in PHP 8.2
        if (!class_exists('\SensitiveParameter')) {
            $this->expectNotToPerformAssertions();

            return;
        }

        try {
            // trigger a \TypeError with the $user argument
            $this->endpoint->setAuthentication(null, 'S0M3p455');
        } catch (\TypeError $e) {
            $trace = $e->getTrace();

            // \SensitiveParameterValue::class trips phpstan in PHP versions that don't support it
            $this->assertInstanceOf('\SensitiveParameterValue', $trace[0]['args'][1]);
        }
    }

    public function testGetAndSetAuthorizationToken()
    {
        $tokenname = 'Token';
        $token = '1234567890ABCDEFG';

        $this->endpoint->setAuthorizationToken($tokenname, $token);

        $this->assertSame(
            [
                'tokenname' => $tokenname,
                'token' => $token,
            ],
            $this->endpoint->getAuthorizationToken()
        );
    }

    public function testSetAuthorizationTokenSensitiveParameter()
    {
        // #[\SensitiveParameter] was introduced in PHP 8.2
        if (!class_exists('\SensitiveParameter')) {
            $this->expectNotToPerformAssertions();

            return;
        }

        try {
            // trigger a \TypeError with the $tokenname argument
            $this->endpoint->setAuthorizationToken(null, '1234567890ABCDEFG');
        } catch (\TypeError $e) {
            $trace = $e->getTrace();

            // \SensitiveParameterValue::class trips phpstan in PHP versions that don't support it
            $this->assertInstanceOf('\SensitiveParameterValue', $trace[0]['args'][1]);
        }
    }

    public function testIsAndSetLeader()
    {
        $this->endpoint->setLeader(true);
        $this->assertTrue($this->endpoint->isLeader());
    }

    public function testToString()
    {
        $options = [
            'host' => '192.168.0.1',
            'port' => 123,
            'path' => '/mysolr/',
            'context' => 'lunr',
            'core' => 'mycore',
            'username' => 'x',
            'password' => 'y',
            'tokenname' => 'a',
            'token' => 'b',
        ];
        $this->endpoint->setOptions($options);

        $endpoint = <<<EOF
Solarium\Core\Client\Endpoint::__toString
host: 192.168.0.1
port: 123
path: /mysolr
context: lunr
collection: 
core: mycore
authentication: Array
(
    [username] => x
    [password] => y
    [tokenname] => a
    [token] => b
)

EOF;

        $this->assertSame($endpoint, (string) $this->endpoint);
    }
}
