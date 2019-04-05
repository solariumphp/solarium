<?php

namespace Solarium\QueryType\Server\Query\Action;

/**
 * Server query command base class.
 */
abstract class AbstractAsyncAction extends AbstractAction implements AsyncActionInterface
{
    public function setAsync(string $requestId): AsyncActionInterface
    {
        $this->setOption('async', $requestId);
        return $this;
    }

    public function getAsync(): string
    {
        return (string) $this->getOption('async');
    }
}
