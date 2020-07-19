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
     * Build request for a select query.
     *
     * @param AbstractQuery $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request;
}
