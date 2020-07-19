<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Query\Action;

/**
 * Server query command base class.
 */
abstract class AbstractAsyncAction extends AbstractAction implements AsyncActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function setAsync(string $requestId): AsyncActionInterface
    {
        $this->setOption('async', $requestId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAsync(): string
    {
        return (string) $this->getOption('async');
    }
}
