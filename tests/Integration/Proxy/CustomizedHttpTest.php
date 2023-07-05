<?php

namespace Solarium\Tests\Integration\Proxy;

use Solarium\Core\Client\Adapter\Http;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Tests\Integration\TestClientFactory;

/**
 * Test connecting through a proxy with a customized Http adapter that sets the proxy options differently.
 *
 * @group integration
 */
class CustomizedHttpTest extends HttpTest
{
    protected static function createClient(): void
    {
        self::$client = TestClientFactory::createWithHttpAdapter(self::$config);
        self::$client->setAdapter(new CustomizedHttp());
    }

    protected static function setProxy(): void
    {
        self::$client->getAdapter()->setProxy(['server' => self::$proxy_server, 'port' => self::$proxy_port]);
    }
}

class CustomizedHttp extends Http
{
    protected $myProxyOptions;

    /**
     * Override to store custom options in our own property that doesn't trigger
     * the {@see Http} adapter's regular proxy handling.
     *
     * @param mixed|array $proxy An associative array with keys 'server' and 'port'
     *
     * @return self Provides fluent interface
     */
    public function setProxy($proxy): self
    {
        $this->myProxyOptions = $proxy;

        return $this;
    }

    public function getProxy()
    {
        return $this->myProxyOptions;
    }

    public function createContext(Request $request, Endpoint $endpoint)
    {
        $context = parent::createContext($request, $endpoint);

        // add our own options to the context
        stream_context_set_option($context, 'http', 'proxy', sprintf('%s:%d', $this->myProxyOptions['server'], $this->myProxyOptions['port']));
        stream_context_set_option($context, 'http', 'request_fulluri', true);

        return $context;
    }
}
