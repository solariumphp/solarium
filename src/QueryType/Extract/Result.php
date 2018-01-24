<?php

namespace Solarium\QueryType\Extract;

use Solarium\QueryType\Update\Result as UpdateResult;

/**
 * An extract result is similar to an update result, but we do want to return a query specific result class instead of
 * an update query result class.
 */
class Result extends UpdateResult
{
}
