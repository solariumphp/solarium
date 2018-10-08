<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

abstract class AbstractAsyncAction extends AbstractCoreAction
{
    /**
     * Can be used to set a requestId to track this action with asynchronous processing.
     *
     * @param string $async
     *
     * @return self Provides fluent interface
     */
    public function setAsync(string $async)
    {
        return $this->setOption('async', $async);
    }

    /**
     * Get the passed handle for asynchronous processing.
     *
     * @return string
     */
    public function getAsync(): string
    {
        return (string) $this->getOption('async');
    }
}
