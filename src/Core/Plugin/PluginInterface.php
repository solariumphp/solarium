<?php

namespace Solarium\Core\Plugin;

use Solarium\Core\Client\ClientInterface;
use Solarium\Core\ConfigurableInterface;

/**
 * Interface for plugins.
 */
interface PluginInterface extends ConfigurableInterface
{
    /**
     * Initialize.
     *
     * This method is called when the plugin is registered to a client instance
     *
     * @param ClientInterface $client
     * @param array           $options
     */
    public function initPlugin(ClientInterface $client, array $options);
}
