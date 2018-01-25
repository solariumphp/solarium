<?php

namespace Solarium\Core\Plugin;

use Solarium\Core\Client\Client;
use Solarium\Core\Configurable;

/**
 * Base class for plugins.
 */
abstract class AbstractPlugin extends Configurable implements PluginInterface
{
    /**
     * Client instance.
     *
     * @var Client
     */
    protected $client;

    /**
     * Initialize.
     *
     * This method is called when the plugin is registered to a client instance
     *
     * @param Client $client
     * @param array  $options
     */
    public function initPlugin($client, $options)
    {
        $this->client = $client;
        parent::__construct($options);

        $this->initPluginType();
    }

    /**
     * Plugin init function.
     *
     * This is an extension point for plugin implementations.
     * Will be called as soon as $this->client and options have been set.
     */
    protected function initPluginType()
    {
    }
}
