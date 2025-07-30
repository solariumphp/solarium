<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\Adapter;

/**
 * Contract for Client Adapters that are aware of proxy settings.
 */
interface ProxyAwareInterface
{
    /**
     * @param mixed|null $proxy
     *
     * @return self Provides fluent interface
     */
    public function setProxy($proxy): self;

    /**
     * @return mixed|null
     */
    public function getProxy();
}
