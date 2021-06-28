<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query;

/**
 * Marker interface to treat 4xx statuses as valid responses for a query.
 *
 * Classes extending AbstractQuery can implement this empty interface if they don't want
 * Result\Result to throw an exception for responses with a 4xx HTTP status.
 */
interface Status4xxNoExceptionInterface
{
}
