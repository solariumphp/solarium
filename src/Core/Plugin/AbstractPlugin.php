<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Plugin;

use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Configurable;

/**
 * Base class for plugins.
 */
abstract class AbstractPlugin extends Configurable implements PluginInterface
{
    /**
     * Client instance.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * Initialize.
     *
     * This method is called when the plugin is registered to a client instance.
     *
     * @param ClientInterface $client
     * @param array           $options
     */
    public function initPlugin(ClientInterface $client, array $options)
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

    /**
     * Plugin cleanup function.
     *
     * This is an extension point for plugin implementations.
     * This method is called if the plugin is removed from a client instance.
     */
    public function deinitPlugin()
    {
    }
}
