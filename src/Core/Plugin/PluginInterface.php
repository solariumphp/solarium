<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
