<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Collections\Result;

use Solarium\QueryType\Server\Query\AbstractResult;

/**
 * ReloadResult.
 */
class ReloadResult extends AbstractResult
{
    /**
     * Returns status of the request and the cores that were reloaded when the reload was successful.
     *
     * {@internal Deprecated in Solarium 7. Shouldn't override generic method
     *            {@link \Solarium\Core\Query\Result\QueryType::getStatus()}
     *            that returns the status code from the response header.}
     *
     * @return array status of the request and the cores that were reloaded when the reload was successful
     *
     * @deprecated Will be removed in Solarium 8. Use {@link getReloadStatus()} instead.
     */
    public function getStatus(): array
    {
        return $this->getReloadStatus();
    }

    /**
     * Returns status of the request and the cores that were reloaded when the reload was successful.
     *
     * @return array status of the request and the cores that were reloaded when the reload was successful
     */
    public function getReloadStatus(): array
    {
        $this->parseResponse();

        return $this->getData();
    }
}
