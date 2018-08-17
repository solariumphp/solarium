<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

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
    public function setOther($other)
    {
        return $this->setOption('other', $other);
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
}
