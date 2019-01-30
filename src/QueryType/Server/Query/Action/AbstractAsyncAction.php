<?php

namespace Solarium\QueryType\Server\Query\Action;

/**
 * Server query command base class.
 */
abstract class AbstractAsyncAction extends AbstractAction implements ActionAsyncInterface
{
    public function setAsync(string $requestId)
    {
        $this->setOption('async', $requestId);
        return $this;
    }

    public function getAsync(): int
    {
        return (string) $this->getOption('async');
    }
}
