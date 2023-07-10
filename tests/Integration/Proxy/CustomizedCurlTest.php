<?php

namespace Solarium\Tests\Integration\Proxy;

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Tests\Integration\TestClientFactory;

/**
 * Test connecting through a proxy with a customized Curl adapter that sets the proxy options differently.
 *
 * @group integration
 */
class CustomizedCurlTest extends CurlTest
{
    protected static function createClient(): void
    {
        self::$client = TestClientFactory::createWithCurlAdapter(self::$config);
        self::$client->setAdapter(new CustomizedCurl());
    }

    protected static function setProxy(): void
    {
        self::$client->getAdapter()->setProxy(['server' => self::$proxy_server, 'port' => self::$proxy_port]);
    }
}

class CustomizedCurl extends Curl
{
    protected $myProxyOptions;

    /**
     * Override to store custom options in our own property that doesn't trigger
     * the {@see Curl} adapter's regular proxy handling.
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

    public function createHandle(Request $request, Endpoint $endpoint): \CurlHandle
    {
        $handle = parent::createHandle($request, $endpoint);

        // add our own options to the cURL handle
        curl_setopt($handle, CURLOPT_PROXY, $this->myProxyOptions['server']);
        curl_setopt($handle, CURLOPT_PROXYPORT, $this->myProxyOptions['port']);

        return $handle;
    }
}
