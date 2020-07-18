<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;
use Solarium\QueryType\Server\Query\Action\AbstractAction;

/**
 * Class RequestRecovery.
 *
 * @see https://lucene.apache.org/solr/guide/coreadmin-api.html#coreadmin-requestrecovery
 */
class RequestRecovery extends AbstractAction implements CoreActionInterface
{
    use CoreActionTrait;

    /**
     * Returns the action type of the core admin action.
     *
     * @return string
     */
    public function getType(): string
    {
        return CoreAdminQuery::ACTION_REQUEST_RECOVERY;
    }
}
