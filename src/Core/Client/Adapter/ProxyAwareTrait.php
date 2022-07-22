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
 * @internal
 */
trait ProxyAwareTrait
{
    /**
     * @var mixed|null
     */
    private $proxy;

    /**
     * {@inheritdoc}
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProxy()
    {
        return $this->proxy;
    }
}
