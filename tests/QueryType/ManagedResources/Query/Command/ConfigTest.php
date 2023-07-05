<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;
use Solarium\QueryType\ManagedResources\Query\Command\Config;
use Solarium\QueryType\ManagedResources\Query\InitArgsInterface;

class ConfigTest extends TestCase
{
    /** @var Config */
    protected $config;

    /** @var InitArgsInterface */
    protected $initArgs;

    public function setUp(): void
    {
        $this->config = new Config();
        $this->initArgs = new DummyInitArgs();
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMMAND_CONFIG, $this->config->getType());
    }

    public function testGetRequestMethod()
    {
        $this->assertSame(Request::METHOD_PUT, $this->config->getRequestMethod());
    }

    public function testSetAndGetInitArgs()
    {
        $this->config->setInitArgs($this->initArgs);
        $this->assertSame($this->initArgs, $this->config->getInitArgs());
    }

    public function testGetRawData()
    {
        $this->initArgs->setInitArgs(['ignoreCase' => true]);
        $this->config->setInitArgs($this->initArgs);
        $this->assertSame('{"initArgs":{"ignoreCase":true}}', $this->config->getRawData());
    }

    public function testGetRawDataEmptyInitArgs()
    {
        $this->initArgs->setInitArgs([]);
        $this->config->setInitArgs($this->initArgs);
        $this->assertNull($this->config->getRawData());
    }

    public function testGetRawDataNoInitArgs()
    {
        $this->assertNull($this->config->getRawData());
    }
}

/**
 * Dummy InitArgs.
 */
class DummyInitArgs implements InitArgsInterface
{
    /**
     * @var array
     */
    protected $initArgs = [];

    /**
     * Constructor.
     *
     * @param array $initArgs
     */
    public function __construct(array $initArgs = null)
    {
        if (null !== $initArgs) {
            $this->setInitArgs($initArgs);
        }
    }

    /**
     * Sets the configuration parameters to be sent to Solr.
     *
     * @param array $initArgs
     *
     * @return self Provides fluent interface
     */
    public function setInitArgs(array $initArgs): self
    {
        $this->initArgs = $initArgs;

        return $this;
    }

    /**
     * Returns the configuration parameters to be sent to Solr.
     *
     * @return array
     */
    public function getInitArgs(): array
    {
        return $this->initArgs;
    }
}
