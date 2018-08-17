<?php

namespace Solarium\QueryType\Server;

use Solarium\Core\Query\AbstractQuery;

/**
 * Base class for all server queries, these query are not executed in the context of a collection or a core.
 */
abstract class AbstractServerQuery extends AbstractQuery
{
}
