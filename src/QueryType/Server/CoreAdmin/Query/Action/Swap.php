<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class Swap extends AbstractAsyncAction
{
    /**
     * Returns the action type of the core admin action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CoreAdminQuery::ACTION_SWAP;
    }

    /**
     * Set core that should be used for swapping.
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
     * Get the other core that should ne used for swapping.
     *
     * @return string
     */
    public function getOther(): string
    {
        return (string) $this->getOption('other');
    }
}
