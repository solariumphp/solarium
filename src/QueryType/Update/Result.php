<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Update;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Update result.
 *
 * An update query only returns a query time and status. Both are accessible
 * using the methods provided by {@link Solarium\Core\Query\Result\QueryType}.
 *
 * For now this class only exists to distinguish the different result
 * types. It might get some extra behaviour in the future.
 */
class Result extends BaseResult
{
}
