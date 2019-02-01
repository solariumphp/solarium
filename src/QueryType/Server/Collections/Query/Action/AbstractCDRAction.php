<?php

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\QueryType\Server\Query\Action\AbstractAsyncAction;

/**
 * Abstract class for Create, Delete and Reload actions.
 */
abstract class AbstractCDRAction extends AbstractAsyncAction
{
    public function setName(string $collection)
    {
        $this->setOption('name', $collection);

        return $this;
    }

    public function getName(): string
    {
        return $this->getOption('name');
    }
}
