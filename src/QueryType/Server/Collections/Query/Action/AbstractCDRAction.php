<?php

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\QueryType\Server\Query\Action\AbstractAsyncAction;

/**
 * Abstract class for Create, Delete and Reload actions.
 */
abstract class AbstractCDRAction extends AbstractAsyncAction
{
    /**
     * Set the name of the collection. This parameter is required.
     *
     * @param string $collection
     *
     * @return self
     */
    public function setName(string $collection): self
    {
        $this->setOption('name', $collection);

        return $this;
    }

    /**
     * Get the name of the collection.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getOption('name');
    }
}
