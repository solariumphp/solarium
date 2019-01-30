<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;
use Solarium\QueryType\Server\CoreAdmin\Result\Result;
use Solarium\QueryType\Server\Query\Action\AbstractAsyncAction;

class Rename extends AbstractAsyncAction
{
    /**
     * Returns the action type of the core admin action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CoreAdminQuery::ACTION_RENAME;
    }

    /**
     * Set new name after renaming.
     *
     * @param string $other
     *
     * @return self Provides fluent interface
     */
    public function setOther($other): self
    {
        $this->setOption('other', $other);
        return $this;
    }

    /**
     * Get the other core that should be the new name.
     *
     * @return string
     */
    public function getOther(): string
    {
        return (string) $this->getOption('other');
    }

    /**
     * Returns the namespace and class of the result class for the action.
     * @return string
     */
    public function getResultClass(): string
    {
        return Result::class;
    }
}
