<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query;

use Solarium\Core\Client\Request;

/**
 * Interface for requestbuilders.
 */
interface RequestBuilderInterface
{
    /**
     * Build request for a generic query.
     *
     * @param QueryInterface $query
     *
     * @return Request
     */
    public function build(QueryInterface $query): Request;
}
