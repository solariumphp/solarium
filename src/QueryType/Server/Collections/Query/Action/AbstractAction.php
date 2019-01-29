<?php

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\Core\Configurable;

/**
 * Collections API query command base class.
 */
abstract class AbstractAction extends Configurable implements ActionInterface
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
