<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Extract;

use Solarium\QueryType\Update\Result as UpdateResult;

/**
 * An extract result is similar to an update result, but we do want to return a query specific result class instead of
 * an update query result class.
 */
class Result extends UpdateResult
{
}
