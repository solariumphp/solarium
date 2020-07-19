<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\Query\Action\ActionInterface;

/**
 * CoreActionInterface.
 */
interface CoreActionInterface extends ActionInterface
{
    /**
     * Set the core name that should be reloaded.
     *
     * @param string $core
     *
     * @return CoreActionInterface
     */
    public function setCore(string $core): self;

    /**
     * Get the related core name.
     *
     * @return string|null
     */
    public function getCore(): ?string;
}
