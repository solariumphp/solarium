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

    public function testConfigMode(): void
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

    public function testSetAndGetHost(): void
    {
        $this->endpoint->setHost('myhost');
        $this->assertSame('myhost', $this->endpoint->getHost());
    }

    public function testSetAndGetPort(): void
    {
        $this->endpoint->setPort(8080);
        $this->assertSame(8080, $this->endpoint->getPort());
    }

    public function testSetAndGetPath(): void
    {
        $this->endpoint->setPath('/mysolr');
        $this->assertSame('/mysolr', $this->endpoint->getPath());
    }

    public function testSetAndGetPathWithTrailingSlash(): void
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
    public function testSetAndGetContext(string $context): void
    {
        $this->endpoint->setContext($context);
        $this->assertSame('lunr', $this->endpoint->getContext());
    }

    public function testSetAndGetCollection(): void
    {
        $this->endpoint->setCollection('collection1');
        $this->assertSame('collection1', $this->endpoint->getCollection());
    }

    public function testSetAndGetCore(): void
    {
        $this->endpoint->setCore('core1');
        $this->assertSame('core1', $this->endpoint->getCore());
    }

    public function testSetAndGetScheme(): void
    {
        $this->endpoint->setScheme('https');
        $this->assertSame('https', $this->endpoint->getScheme());
    }

    public function testGetCollectionBaseUri(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/lunr/collection1/', $this->endpoint->getCollectionBaseUri());
    }

    public function testGetCollectionBaseUriWithHttps(): void
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCollection('collection1');

        $this->assertSame('https://myserver:123/mypath/lunr/collection1/', $this->endpoint->getCollectionBaseUri());
    }

    public function testGetCollectionBaseUriException(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->expectException(UnexpectedValueException::class);
        $this->endpoint->getCollectionBaseUri();
    }

    public function testGetCoreBaseUri(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/lunr/core1/', $this->endpoint->getCoreBaseUri());
    }

    public function testGetCoreBaseUriWithHttps(): void
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCore('core1');

        $this->assertSame('https://myserver:123/mypath/lunr/core1/', $this->endpoint->getCoreBaseUri());
    }

    public function testGetCoreBaseUriException(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->expectException(UnexpectedValueException::class);
        $this->endpoint->getCoreBaseUri();
    }

    public function testGetBaseUri(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);

        $this->endpoint->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/lunr/core1/', $this->endpoint->getBaseUri());

        $this->endpoint->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/lunr/collection1/', $this->endpoint->getBaseUri());
    }

    public function testGetBaseUriWithHttps(): void
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);

        $this->endpoint->setCore('core1');
        $this->assertSame('https://myserver:123/mypath/lunr/core1/', $this->endpoint->getBaseUri());

        $this->endpoint->setCollection('collection1');
        $this->assertSame('https://myserver:123/mypath/lunr/collection1/', $this->endpoint->getBaseUri());
    }

    public function testGetBaseUriException(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->expectException(UnexpectedValueException::class);
        $this->endpoint->getBaseUri();
    }

    public function testGetV1BaseUri(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->assertSame('http://myserver:123/mypath/lunr/', $this->endpoint->getV1BaseUri());
    }

    public function testGetV1BaseUriWithHttps(): void
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->assertSame('https://myserver:123/mypath/lunr/', $this->endpoint->getV1BaseUri());
    }

    public function testV1BaseUriDoesNotContainCollectionSegment(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/lunr/', $this->endpoint->getV1BaseUri());
    }

    public function testV1BaseUriDoesNotContainCoreSegment(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/lunr/', $this->endpoint->getV1BaseUri());
    }

    public function testGetV2BaseUri(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->assertSame('http://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());
    }

    public function testGetV2BaseUriWithHttps(): void
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);
        $this->assertSame('https://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());
    }

    public function testV2BaseUriDoesNotContainCollectionSegment(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCollection('collection1');
        $this->assertSame('http://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());
    }

    public function testV2BaseUriDoesNotContainCoreSegment(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123)->setCore('core1');
        $this->assertSame('http://myserver:123/mypath/api/', $this->endpoint->getV2BaseUri());
    }

    public function testGetServerUri(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setContext('lunr')->setPort(123);

        $this->assertSame('http://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testGetServerUriWithHttps(): void
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setPort(123);

        $this->assertSame('https://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testServerUriDoesNotContainContextSegment(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123)->setContext('lunr');

        $this->assertSame('http://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testServerUriDoesNotContainCollectionSegment(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123)->setCollection('mycollection');

        $this->assertSame('http://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testServerUriDoesNotContainCoreSegment(): void
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123)->setCore('mycore');

        $this->assertSame('http://myserver:123/mypath/', $this->endpoint->getServerUri());
    }

    public function testGetAndSetAuthentication(): void
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

    /**
     * @requires PHP >= 8.2
     */
    public function testSetAuthenticationSensitiveParameter(): void
    {
        try {
            // trigger a \TypeError with the $user argument
            $this->endpoint->setAuthentication(null, 'S0M3p455');
        } catch (\TypeError $e) {
            $trace = $e->getTrace();

            // \SensitiveParameterValue::class trips phpstan in PHP versions that don't support it
            $this->assertInstanceOf('\SensitiveParameterValue', $trace[0]['args'][1]);
        }
    }

    public function testGetAndSetAuthorizationToken(): void
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

    /**
     * @requires PHP >= 8.2
     */
    public function testSetAuthorizationTokenSensitiveParameter(): void
    {
        try {
            // trigger a \TypeError with the $tokenname argument
            $this->endpoint->setAuthorizationToken(null, '1234567890ABCDEFG');
        } catch (\TypeError $e) {
            $trace = $e->getTrace();

            // \SensitiveParameterValue::class trips phpstan in PHP versions that don't support it
            $this->assertInstanceOf('\SensitiveParameterValue', $trace[0]['args'][1]);
        }
    }

    public function testIsAndSetLeader(): void
    {
        $this->endpoint->setLeader(true);
        $this->assertTrue($this->endpoint->isLeader());
    }

    public function testToString(): void
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
