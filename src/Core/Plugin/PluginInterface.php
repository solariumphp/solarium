<?php

namespace Solarium\Core\Plugin;

use Solarium\Core\Client\Client;
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
     * @param Client $client
     * @param array  $options
     */
    public function initPlugin($client, $options);
}
