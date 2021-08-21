<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;
use Solarium\QueryType\Server\Query\Action\AbstractAsyncAction;

/**
 * Class Swap.
 *
 * @see https://solr.apache.org/guide/coreadmin-api.html#coreadmin-swap
 */
class Swap extends AbstractAsyncAction implements CoreActionInterface
{
    use CoreActionTrait;

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
    public function setOther($other): self
    {
        $this->setOption('other', $other);

        return $this;
    }

    /**
     * Get the other core that should ne used for swapping.
     *
     * @return string|null
     */
    public function getOther(): ?string
    {
        return $this->getOption('other');
    }
}
